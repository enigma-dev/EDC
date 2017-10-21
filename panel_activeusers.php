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
?>

<div class="edcpane">
  <div class="edctitlebar">Active Users</div>
  <?php
    $onlineMembers = ssi_logOnline('array');
    foreach ($onlineMembers['users'] as $activemember)
    {
      echo "<a href=\"blogs.php?u=" . $activemember['id'] . "\">" . $activemember['name'] . "</a><br />";
    }
    echo "<span class=\"edcSmallInfo\">" . ($onlineMembers['hidden']>0? $onlineMembers['hidden']." hidden, " : "") . $onlineMembers['guests'] . " guests</span>\n";
  ?>
</div><br />
