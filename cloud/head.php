<?php
//Example of getting the Header information on an Object
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

require_once('config.php');
$s = $stores['ORD'];
$c = 'games-00000';

$objs = getObjects($s,$c,null,$authHdr);

$go = true;
foreach ($objs as $obj) {
 if ($go) { $go = false; continue; }
 $n = $obj->name;

 $code = getHdrs($s,$c,$n,$authHdr);
 echo "$code $n<br />\n";
die();
}

include('tree.php');
showObjects($s,$c,$authHdr,'showObjectsList');

?>
