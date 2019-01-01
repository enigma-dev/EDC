<?php
function scour_url_for_filename($url) {
  $pieces = parse_url($url);
  $pnames = Array($pieces['path']);
  if (!empty($pieces['fragment'])) {
    array_push($pnames, $pieces['fragment']);
  }
  if (!empty($pieces['query']))
  foreach (explode('&', $pieces['query']) as $qp) {
    $kv	= explode('=', $qp, 2);
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

echo scour_url_for_filename('http://files.enigma-dev.org/game/69/screen/1/c40679/30dgp6h.png') . '<br/>';
echo scour_url_for_filename('https://www.dropbox.com/s/9garjy0rb53a50h/100_3607.jpg?dl=0') . '<br/>';
echo scour_url_for_filename('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTC2Ktbj2sPvHKISBq8d8joCtZdC-m7u2TTG_5Y8jVgeD_i2TJc') . '<br/>';
echo scour_url_for_filename('https://www.dropbox.com/s/9garjy0rb53a50h/#100_3607.jpg') . '<br/>';
echo scour_url_for_filename('https://www.dropbox.com/s/9garjy0rb53a50h/?foo=Ihateyou&f=100_3607.jpg&file=hatemeyet#bugger') . '<br/>';

?>
