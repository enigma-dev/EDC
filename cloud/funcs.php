<?php
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

define("DEBUG", true);

function curl($url, $headers, $post = null, $method = null, $flags = array()) {
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);

	//curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
	//curl_setopt ($ch, CURLOPT_CAINFO, "/etc/ssl/cacert.pem");
	//curl_setopt ($ch, CURLOPT_VERBOSE, TRUE);

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
		$url .= '?' . str_replace("%2F", '/', http_build_query($params, '', '&'));
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
	// echo "Calling with <span style=\"border: 1px solid blue; padding: 3px;\">" . htmlspecialchars($url) . "</span><br/>";
	$output = curl($url, $authHdr);
	// echo ("<pre style=\"border:1px solid red; padding: 8px;\">" . htmlspecialchars($output) . "</pre>");
	return json_decode($output);
}

function getObjectsByPath($store, $container, $path, $authHdr, $limits = FALSE) {
	$params = array('format' => 'json', "prefix" => $path, "delimiter" => "/");
	if ($limits !== FALSE) {
		foreach ($limits as $kind => $limit) {
			switch ($kind) {
				case "limit": case "count":
					$params["limit"] = intval($limit);
					break;
				case "upper": case "before": case "marker_end":
					$params["marker_end"] = $limit;
					break;
				case "lower": case "after": case "marker":
					$params["marker"] = $limit;
					break;
			}
		}
	}
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

/**
 * Delete the given object, returning TRUE iff successful.
 */
function deleteObject($store, $container, $obj, $authHdr) {
	$url = getUrl($store, $container, $obj);
	return curl($url, $authHdr, null, 'DELETE', array('CODE')) == 204;
}

/**
 * Parse HTTP headers into an associative array.
 */
function parse_headers($header) {
	$arr = preg_split('/\s*([A-Za-z0-9\-]+)\s*:\s*(.*?)\s*(?=\n)/', $header, -1, PREG_SPLIT_DELIM_CAPTURE);
	$num = count($arr) - 1;
	$result	= Array();
	for ($i	= 1; $i < $num;	$i += 3) {
		$result[strtolower($arr[$i])] = $arr[$i+1];
	}
	return $result;
}

/**
 * Uploads the given file to the given path.
 * @return TRUE if the upload succeeded; otherwise, the error text
 */
function putObject($store, $container, $destpath, $file, $authHdr) {
	$params =  array('format' => 'json');
	$ch = curl_init(getUrl($store, $container, $destpath, $params));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $authHdr);
	if (isset($post) && !empty($post))
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	if (isset($method) && !empty($method))
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

	$fname = is_string($file)          ? $file
               : (isset($file['tmp_name']) ? $file['tmp_name']
                                           : $file['name']);
	$size = is_string($file)      ? filesize($file)
              : (isset($file['size']) ? $file['size']
                                      : $file['filesize']);

	$fstream = fopen($fname, 'rb');
	if ($fstream === FALSE) {
		 return array(
                        'success' => FALSE,
                        'code'    => 404,
                        'error'   => 'The file you specified could not be opened.');
	}

	curl_setopt($ch, CURLOPT_PUT, TRUE);
	curl_setopt($ch, CURLOPT_INFILE, $fstream);
	curl_setopt($ch, CURLOPT_INFILESIZE, $size);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);

	$response = curl_exec($ch);

	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$output = substr($response, $header_size);

	$headerdata = parse_headers($header);
	// echo '<pre>headers: '; print_r($header); echo '</pre>';
	// echo '<pre>headerdata: '; print_r($headerdata); echo '</pre>';

	if (curl_errno($ch) != 0) {
		$error = curl_error($ch);
		curl_close($ch);
		return array(
			'success' => FALSE,
			'code'    => $status,
			'error'   => $error);
	}

	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($status != 201) {
		return  array(
                        'success' => FALSE,
                        'code'    => $status,
                        'error'   => $output);
	}
	$info = json_decode($output);
	// echo "<pre>dump: `"; print_r($info); echo '`</pre>';
	// echo '<pre>Output: '; print_r($output); echo '</pre>';
	return array(
		'success' => TRUE,
		'code'    => $status,
		'type'    => $headerdata['content-type'],
		'hash'    => $headerdata['etag'],
		'date'    => $headerdata['date']);
}

function getHdrs($store, $container, $obj, $authHdr) {
	$url = getUrl($store, $container, $obj);
	echo $url . "<br />\n";
	return curl($url, $authHdr, null, 'HEAD', array('CODE'));
}
?>
