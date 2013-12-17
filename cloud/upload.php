<?php
//The general code to upload a file from a user's computer.
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

var_dump($_FILES);
$fi = reset($_FILES);

if (isset($fi['error']) && ($fi['error'] !== UPLOAD_ERR_OK)) {
	if ($fi['error'] == UPLOAD_ERR_NO_FILE)
	 echo 'Please specify a file.';
	else
	 echo 'An error occurred (' . $f['error'] . '). Please try again.';
	$fi = null;
}

if (!isset($fi) || ($fi == null)) {
	//enctype needed or $_FILES won't be set
	?><form action="upload.php" method="post" enctype="multipart/form-data">
	File: <input type="file" name="f" /><br />
	<input type="submit" />
	</form><?php
	die();
}

echo $fi['name'] . ' (' . $fi['size'] . ') -> ' . $fi['tmp_name'] . "<br />\n";

/*require_once('config.php');

$f = fopen($fi['tmp_name'],'r');

$store = $stores['ORD'];
$con = 'games-00000';

$url = $store->url. "/$con/test.txt";
$hdrs = $authHdr;
$hdrs[1] = 'Content-Length: ' . $fi['size'];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_INFILE, $f);
curl_setopt($ch, CURLOPT_INFILESIZE, $fi['size']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$output = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);
fclose($f);
echo "Error: $error<br />\n";
echo "Return Code: $code<br />\n";
var_dump($output);*/

?>
