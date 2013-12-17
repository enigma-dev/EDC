<?php
//Functional example to rename an object.
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

require_once('config.php');
$s = $stores['ORD'];
$c = 'screens-00000';
$o = '53thumb.gif';
$o2 = '53\\thumb.gif';
$code = copyObject($s,$c,$o,$o2,$authHdr);
echo "$code<br />\n";
if ($code >= 200 && $code < 300)
 $code = deleteObject($s,$c,$o,$authHdr);
echo "$code<br />\n";
?>
