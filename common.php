<?php
/* Copyright (C) 2011-2013 Josh Ventura <JoshV10@gmail.com>
 *
 * This file is part of the ENIGMA Developers Community (EDC).
 *
 * The EDC is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, version 3 of the License, or (at your option) any later version.
 *
 * This source is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this code. If not, see <http://www.gnu.org/licenses/>.
*/

require_once('../forums/SSI.php');
require_once('config.php');

register_shutdown_function('edcj_clearfix');
function edcj_clearfix() { echo '<div class="clear"></div>'; }
include('../site/template.php');

echo "<LINK href=\"edc.css\" rel=\"stylesheet\" type=\"text/css\">";
include('navbar.php');
?>
