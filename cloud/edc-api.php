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

require_once('cloud/interface.php');

function get_game_files($gid) {
  global $stores, $authHdr, $main_store, $main_container, $download_root;
  $cats = array(); // Source, Windows, Linux, OS X...
  $links = array(); // The actual download links
  $metrics = array(); // Info about given files
  $screens = array(); // Screenshot links
  $thumb = "";
  
  $games_prefix = "game/";
  $prefix = $games_prefix . $gid . '/';
  
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

?>
