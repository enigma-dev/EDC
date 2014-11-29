<?php
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//Copyright (C) 2013 Josh Ventura <JoshV10@gmail.com
//This file is licensed under the MIT license. See LICENSE for details.

require_once('cloud/funcs.php');
require_once('cloud/config.php');

function authorize($baseurl, $user, $auth_key) {
	//auth json
	$auth = array(
	 'auth' => array(
	  'RAX-KSKEY:apiKeyCredentials' => array(
	   'username' => $user,
	   'apiKey' => $auth_key
	  )
	 )
	);

	//authorize
	$url = 'https://identity.' . $baseurl . '/v2.0/tokens';
	$hdr = array('Content-Type: application/json');
	$pst = json_encode($auth);
	$output = curl($url,$hdr,$pst);

	return json_decode($output);
}

class Store {
	public $region;
	public $url;
	public $internal;

	function __construct($reg, $pub) {
		$this->region = $reg;
		$this->url = $pub;
		// $this->internal = $int;
	}
}

$out = authorize($baseurl,$user,$auth_key);
unset($auth_key);

//get token and stores
$authHdr = array('X-Auth-Token: ' . $out->access->token->id);
$endpts = $out->access->serviceCatalog[0]->endpoints;
foreach ($out->access->serviceCatalog as $service)
	if (!strcasecmp($service->name, 'CloudFiles'))
		$endpts = $service->endpoints;

/*
echo '<pre style="border: 1px solid green; padding: 8px;">';
print_r($out->access->serviceCatalog);
echo '</pre>';
*/
$stores = array();
foreach ($endpts as $endpt) {
	// echo "STORE:<br/>";
	// print nl2br(print_r($endpt,true));
	$stores[$endpt->region] = new Store(
		$endpt->region,
		$endpt->publicURL
	);
}

$main_store = $stores[$main_store_label];

?>
