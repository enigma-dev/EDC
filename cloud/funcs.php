<?php
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

define("DEBUG", true);

function curl($url, $headers, $post = null, $method = null, $flags = array()) {
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if (isset($post) && !empty($post))
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	if (isset($method) && !empty($method))
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	if (in_array('HEADER',$flags))
		curl_setopt($ch, CURLOPT_HEADER, true);
	if (in_array('NOBODY',$flags) || in_array('CODE',$flags))
		curl_setopt($ch, CURLOPT_NOBODY, true);

	$output = curl_exec($ch);
	if (curl_errno($ch) != 0) {
		$error = curl_error($ch);
		curl_close($ch);
		die($error);
	}
	if (in_array('CODE',$flags))
		$output = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);
	return $output;
}

function rawurlencode_except_slashes($object) {
	$parts = explode('/',$object);
	$parts = array_map('rawurlencode',$parts);
	return implode('/',$parts);
}

function getUrl($store, $container, $object = null, $params = array()) {
	//store
	$url = $store->url . '/';

	//container
	if (is_string($container))
		$url .= rawurlencode($container);
	else
		$url .= $container->name;

	//object
	if (!empty($object)) {
		//doesn't seem to matter if we encode the slashes or not
		//so I've chosen not to, for readability.
		//You may opt to encode them for faster operation.
		$url .= '/' . rawurlencode_except_slashes($object);
	}
	else if (!empty($params)) {
		//can't keep slashes here. Different encoding for url params.
		$url .= '?' . http_build_query($params);
	}

	return $url;
}

function getContainers($store, $authHdr) {
	$output = curl($store->url . '?format=json',$authHdr);
	return json_decode($output);
}

function getObjects($store, $container, $prefix = null, $authHdr) {
	$params = array('format' => 'json');
	if (!empty($prefix))
		$params['prefix'] = $prefix;
	$url = getUrl($store, $container, null, $params);
	$output = curl($url, $authHdr);
	return json_decode($output);
}

function copyObject($store, $container, $obj, $newObj, $authHdr) {
	$url = getUrl($store, $container, $obj);
	$hdrs = $authHdr;
	$hdrs[] = "Destination: /$container/$newObj";
	return curl($url, $hdrs, null, 'COPY', array('CODE'));
}

function copyObjectToContainer($store, $container, $obj, $newContainer, $newObj, $authHdr) {
        $url = getUrl($store, $container, $obj);
        $hdrs = $authHdr;
        $hdrs[] = "Destination: /$newContainer/$newObj";
        return curl($url, $hdrs, null, 'COPY', array('CODE'));
}

function deleteObject($store, $container, $obj, $authHdr) {
	$url = getUrl($store, $container, $obj);
	return curl($url, $authHdr, null, 'DELETE', array('CODE'));
}

function getHdrs($store, $container, $obj, $authHdr) {
	$url = getUrl($store, $container, $obj);
echo $url . "<br />\n";
	return curl($url, $authHdr, null, 'HEAD', array('CODE'));
}
?>
