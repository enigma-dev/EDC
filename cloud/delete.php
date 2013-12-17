<?php
//Functional example of deleting an object.
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

//passworded to prevent accidental deletion
if (!isset($_REQUEST['pw'])
 || empty($_REQUEST['pw'])
 || $_REQUEST['pw'] != '61')
  die('nope');
require_once('config.php');
$out = deleteObject($stores['ORD'],'games-00000','test.txt',$authHdr);
var_dump($out);
?>
