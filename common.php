<?php
require_once('../forums/SSI.php');

$siteroot = '/var/www/html/enigma-dev.org/';
register_shutdown_function('edcj_clearfix');
function edcj_clearfix() { echo '<div class="clear"></div>'; }
include('../site/template.php');

echo "<LINK href=\"edc.css\" rel=\"stylesheet\" type=\"text/css\">";
include('navbar.php');
?>
