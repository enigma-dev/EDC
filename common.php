<?php
require_once('../forums/SSI.php');
require_once('config.php');

register_shutdown_function('edcj_clearfix');
function edcj_clearfix() { echo '<div class="clear"></div>'; }
include('../site/template.php');

echo "<LINK href=\"edc.css\" rel=\"stylesheet\" type=\"text/css\">";
include('navbar.php');
?>
