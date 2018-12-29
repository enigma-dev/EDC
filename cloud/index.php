<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cloud Overview</title>
</head><body>

<?php
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

require_once('config.php');
include('tree.php');
include('interface.php');

if (isset($_REQUEST['s'])
 && !empty($_REQUEST['s'])
 && array_key_exists($_REQUEST['s'],$stores)) {
	echo '<h1>' . $_REQUEST['s'] . '</h1>';
	if (isset($_REQUEST['c']) && !empty($_REQUEST['c'])) {
		echo '<h2>' . htmlspecialchars($_REQUEST['c']) . '</h2>';
		showObjects($stores[$_REQUEST['s']],$_REQUEST['c'],$authHdr);
	} else
		showContainers($stores[$_REQUEST['s']],$authHdr);
} else {
	echo "<ul>\n";
	foreach ($stores as $region => $store) {
		$storeurl = $store->url;
		echo "<li>$region\n";
		showContainers($store,$authHdr,'showObjectsList');
		echo "</li>";
	}
	echo "</ul>\n";
}

?>
</body></html>
