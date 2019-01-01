<?php
/* Copyright (C) 2011-2013 Josh Ventura <JoshV10@gmail.com>
 *
 * This file is part of the ENIGMA Developers Community (EDC).
 *
 * The EDC is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, version 3 of the License, or (at your option) any later version.
 *
 * This source is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this code. If not, see <http://www.gnu.org/licenses/>.
*/

require_once(__DIR__.'/interface.php');

define('GAMES_PREFIX',  "game/");

function get_game_files($gid) {
  global $stores, $authHdr, $main_store, $main_container, $download_root;
  $cats = array(); // Source, Windows, Linux, OS X...
  $links = array(); // The actual download links
  $metrics = array(); // Info about given files
  $screens = array(); // Screenshot links
  $thumb = "";
  
  $prefix = GAMES_PREFIX . $gid . '/';
  
  // Fetch the objects
  $objs = getObjects($main_store, $main_container, $prefix, $authHdr);
  
  $regpre = preg_quote($prefix, '/');
  foreach ($objs as $file)
  {
    // print "sexy file dance<br/>";
    // print nl2br(print_r($file,true));
    $matches = array();
    if (preg_match('/' . $regpre . 'file\/[0-9]+\/[0-9a-fA-F]+\/(.*?)\/(.*)/', $file->name, $matches))
    {
      array_push($cats,    $matches[1]);
      array_push($links,   $download_root . $file->name);
      array_push($metrics, array(
       'size' => $file->bytes,
       'hash' => $file->hash,
       'modified' => $file->last_modified
      ));
    }
    else if (preg_match('/' . $regpre . 'screen\/[0-9]+\/[0-9a-fA-F]+\/(.*)/', $file->name, $matches))
      array_push($screens, $download_root . $file->name);
    else if (preg_match('/' . $regpre . 'thumb\/.*/', $file->name) && empty($thumb))
      $thumb = $file->name;
    else {
      array_push($cats,    "ERROR");
      array_push($links,   $download_root . $file->name);
      array_push($metrics, array(
        'size' => (property_exists($file, "size")? $file->size : property_exists($file, "bytes")? $file->bytes : "UNKNOWN SIZE"),
        'hash' => (property_exists($file, "hash")? $file->hash : "??????"),
        'modified' => (property_exists($file, "last_modified")? $file->last_modified : "??/??/??")
      ));
    }
  }
  
  return array('cats' => $cats, 'links' => $links, 'screens' => $screens, 'thumb' => $thumb);
}

function determine_file_extension($file) {
  $mime = mime_content_type($file);
  $known = Array(
      'application/zip'              => '.zip',
      'application/x-rar-compressed' => '.rar',
      'application/x-7z-compressed'  => '.7z',
      'image/png'                    => '.png',
      'image/jpeg'                   => '.jpg',
      'image/gif'                    => '.gif',
      'image/bmp'                    => '.bmp',
      'image/tiff'                   => '.tiff',
      'image/svg+xml'                => '.svg',
      'image/vnd.microsoft.icon'     => '.ico');
  if (array_key_exists($mime, $known)) return $known[$mime];
  return '.bin';
}

function gen_upload_filename($src_fname, $identifier, $preferred = NULL, $desc = NULL) {
  if (!empty($preferred)) {
    $fname = $preferred;
  } else {
    $pinfo = pathinfo($src_fname);
    $extension = empty($pinfo['extension']) ? '' : '.' . $pinfo['extension'];
    if (empty($extension)) $extension = determine_file_extension($src_fname);
    $name = $pinfo['filename'];
    $fname = $name . $extension;
  }
  if (empty($desc)) {
    $label_part = '';
  } else {
    $label_part	= $desc . '/';
  }
  $md5 = md5_file($src_fname);
  if (strlen($md5) < 6) die('Failed to verify checksum of ' . $identifier . '.');
  $hash = substr($md5, 0, 6);
  return $hash . '/' . $label_part . $fname;
}

function upload_game_data($game_id, $thumbnail, $screens, $files) {
  global $stores, $authHdr, $main_store, $main_container, $download_root;

  $STORE = $stores['ORD'];
  $CONTAINER = 'files-00000';
  $GAME_PATH = GAMES_PREFIX . $game_id . '/';
  $SCREEN_PATH = $GAME_PATH . 'screen/';
  $FILE_PATH   = $GAME_PATH . 'file/';
  $THUMB_PATH  = $GAME_PATH . 'thumb/';

  $uploads = array();

  $thumb_cloud = $THUMB_PATH . gen_upload_filename($thumbnail, 'thumbnail');
  array_push($uploads, array(
      'src'  => $thumbnail,
      'dest' => $thumb_cloud,
      'id'   => 'thumbnail',
  ));
  $thumb_cloud = 'http://files.enigma-dev.org/' . $thumb_cloud;

  $lin_id = 0;
  foreach ($screens as $ind => $screen) {
    $id = 'screenshot [' . $ind . ']';
    array_push($uploads, array(
        'src'  => $screen,
        'dest' => $SCREEN_PATH . ++$lin_id . '/' . gen_upload_filename($screen, $id),
        'id'   => $id, 
    ));
  }

  $lin_id = 0;
  foreach ($files as $ind => $file) {
    $id = 'game file [' . $ind . '] ("' . $file['name'] . '")';
    array_push($uploads, array(
        'src'  => $file['file'],
        'dest' => $FILE_PATH . ++$lin_id . '/' . gen_upload_filename($file['file'], $id, $file['user-fname'], $file['name']),
        'id'   => $id,
    ));
  }

  // Upload all game data to cloud.
  $errors = Array();
  $successes = Array();
  foreach ($uploads as $upload) {
    $srcpath = $upload['src'];
    $destpath = $upload['dest'];
    // $success = TRUE; echo 'putObject(' . gettype($STORE) . ', ' . $CONTAINER . ', ' . $destpath . ', ' . $srcpath . ', ' . sizeof($authHdr) . '-byte header)<br/>';
    $success = putObject($STORE, $CONTAINER, $destpath, $srcpath, $authHdr);
    if ($success) {
      array_push($successes, $upload['id']);
    } else {
      array_push($errors, 'Failed to upload ' . $upload['id'] . ' to ' . $destpath);
    }
  }

  return array(
      'errors' => $errors,
      'successful' => $successes,
      'thumbnail-url' => $thumb_cloud);
}

?>
