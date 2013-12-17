<?php
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

require_once('config.php');

function showObjects($store, $container, $authHdr, $method = null) {
	$objects = getObjects($store,$container,null,$authHdr);
	if ($method != null)
		$method($objects);
	else
		showObjectsTable($objects);
}

function showObjectsTable($objects) {
?><table border=1><tr>
<th>Id</th><th>Name</th><th>Full path</th><th>Size</th>
<th>Hash</th><th>Modified</th></tr>
<?php

	foreach ($objects as $object) {
		$parts = explode('/',$object->name);
		echo '<tr><td>';
		echo $parts[1] . '</td><td>';
		echo $parts[count($parts) - 1] . '</td><td>';
		echo $object->name . '</td><td>';
		echo $object->bytes . '</td><td>';
		echo $object->hash . '</td><td>';
		echo $object->last_modified . "</td></tr>\n";
	}

	echo "</table>";
}

function showObjectsList($objects) {
	echo '<ul>';
	foreach ($objects as $object)
		echo "<li>$object->name ($object->bytes b)</li>\n";
	echo "</ul>\n";
}

function showContainers($store, $authHdr, $showObjectsMethod = null) {
	echo '<ul>';
	$containers = getContainers($store, $authHdr);
	foreach ($containers as $container) {
		echo '<li><a href="?s=';
		echo $store->region . '&c=';
		echo $container->name . '">';
		echo "$container->name</a> ($container->count)\n";
		if ($container->count > 0)
			showObjects($store,$container,$authHdr,$showObjectsMethod);
		echo "</li>\n";
	}
	echo "</ul>\n";
}
?>
