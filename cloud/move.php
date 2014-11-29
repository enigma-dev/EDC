<?php
//Code template for batch moving/renaming objects. Used and left for posterity.
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

die('Nope');
require_once('config.php');
$s = $stores['ORD'];
$c0 = 'games-00000';
$c1 = 'screens-00000';

$objs = getObjects($s,$c1,null,$authHdr);

foreach ($objs as $obj) {
  $n = $obj->name;

  $url = getUrl($s, $c1, $n);
  $hdrs = $authHdr;
  $hdrs[] = "Destination: /$c0/$n";
  $code = curl($url, $hdrs, null, 'COPY', array('CODE'));

  if ($code < 200 || $code >= 300)
    echo '<h3>';
  echo "$code $c1/$n -> $c0/$n";
  if ($code < 200 || $code >= 300)
    echo '</h3>';
   echo '<br />';
}

include('tree.php');
showObjects($s,$c,$authHdr,'showObjectsList');

?>
