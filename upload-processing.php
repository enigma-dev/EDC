<?php

define('THUMB_W', 154);
define('THUMB_H', 96);

function generate_game_thumbnail($image, $frame_selection, $x, $y, $w, $h, $outname) {
  try {
    $imm = new Imagick($image);
    $imm->cropImage($w, $h, $x, $y);
    $imm->scaleImage(THUMB_W, THUMB_H);
    if ($frame_selection !== NULL) {
      $imm->compositeImage(new Imagick('images/frames/frame' . $frame_selection . '.png'), imagick::COMPOSITE_ATOP, 0, 0);
    }
    $imm->writeImage($outname);
  } catch (Exception $e) {
    return FALSE;
  }
  return TRUE;
}

function generate_game_thumbnail_gross($image, $outname) {
  try {
    $imm = new Imagick($image);
    $imm->scaleImage(THUMB_W, THUMB_H);
    $imm->compositeImage(new Imagick('images/frames/frame1.png'), imagick::COMPOSITE_ATOP, 0, 0);
    $imm->writeImage($outname);
  } catch (Exception $e) {
    return FALSE;
  }
  return TRUE;
}

function validate_game_thumbnail($image, $outname) {
  try {
    $imm = new Imagick($image);
    $imm->scaleImage(THUMB_W, THUMB_H);
    $ifmt = strtolower($imm->getImageFormat());
    if ($ifmt == 'svg' || $ifmt == 'svgz') {
      copy($image, $outname);
      return TRUE;
    }
    $imm->writeImage($outname);
  } catch (Exception $e) {
    return FALSE;
  }
  return TRUE;
}

function scour_url_for_filename($url) {
  $pieces = parse_url($url);
  $pnames = Array($pieces['path']);
  if (!empty($pieces['fragment'])) {
    array_push($pnames, $pieces['fragment']);
  }
  if (!empty($pieces['query']))
  foreach (explode('&', $pieces['query']) as $qp) {
    $kv = explode('=', $qp, 2);
    if (!empty($kv)) {
      array_push($pnames, array_pop($kv));
    }
  }
  $known_extensions = Array(
      'gmd' => 'gmd', 'gm6' => 'gm6', 'gmk' => 'gmk', 'gm8' => 'gm8',
      'gm81' => 'gm81', 'gmx' => 'gmx', 'gmz' => 'gmz', 'egm' => 'egm',
      'zip' => 'zip', '7z' => '7z', 'rar' => 'rar', 'gz' => 'gz',
      'tar' => 'tar', 'lzma' => 'lzma', 'exe' => 'exe',
      'png' => 'png', 'jpg' => 'jpg', 'jpeg' => 'jpeg', 'svg' => 'svg',
      'svgz' => 'svgz', 'tif' => 'tif', 'tiff' => 'tiff', 'bmp' => 'bmp');
  foreach ($pnames as $name) {
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    if (array_key_exists($ext, $known_extensions)) {
      return pathinfo($name, PATHINFO_BASENAME);
    }
  }
  return NULL;
}

function stage_file_or_url($file_key, $url_key, $category, $index) {
  $local_fname = NULL;
  $user_fname = NULL;
  if (array_key_exists($file_key, $_FILES)
  &&  array_key_exists('tmp_name', $_FILES[$file_key])) {
    $local_fname = $_FILES[$file_key]['tmp_name'];
    $user_fname = $_FILES[$file_key]['name'];
  }
  if (empty($local_fname)) {
    if (array_key_exists($url_key, $_POST)) {
      $file_url = $_POST[$url_key];
      if (!empty($file_url)) {
        $local_fname = tempnam(sys_get_temp_dir(), 'edc');
        $content = file_get_contents($file_url);
        $user_fname = scour_url_for_filename($file_url);
        if (empty($content)) {
          die("Could not retrieve " . $category . " " . $index .
              " from URL: " . $file_url);
        }
        if (!file_put_contents($local_fname, $content)) {
          die("Could not retrieve " . $category . " " . $index .
              " for cloud upload. The download seemed to succeed," .
              " but storing the file failed.");
        }
      }
    }
  }
  return $local_fname === NULL ? NULL : Array(
    'local' => $local_fname,
    'user'  => $user_fname
  );
}

?>
