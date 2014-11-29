<?php
//Functional example to rename an object.
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

require_once('config.php');
#require_once('funcs.php');
require_once('interface.php');
require_once('tree.php');

$s = $stores['ORD'];
$c = 'files-00000';
$o = 'game/43/file/1/fps.zip';
$o2 = 'game/43/file/1/30c577/Source/fps.zip';

// Rename code
//$code = copyObject($s,$c,$o,$o2,$authHdr);
//echo "$code<br />\n";
//if ($code >= 200 && $code < 300)
// $code = deleteObject($s,$c,$o,$authHdr);
//echo "$code<br />\n";

showObjects($s,$c,$authHdr,'showObjectsList');

?>
