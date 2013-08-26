<?
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

<div 
class="edcpane">
  <div class="edctitlebar">User</div>
  <?php
    if ($context['user']['is_logged'])
    {
      echo "    <a href=\"blogs.php?u=" . $user_info['id'] . "\">" . $user_info['name'] . "</a><br />\n";
      echo "    <a href=\"$forum_path/index.php?action=pm\">Private Messages</a><br />\n";
      echo "    <a href=\"blogs.php?action=new\">Post new blog</a><br />\n";
      echo "    <a href=\"games.php?action=new\">Submit game</a><br />\n";
      ssi_logout();
    }
    else
    {
      echo 
      "    Welcome, guest!<br/>" .
      "    <a href=\"$forum_path/index.php?action=login\">Login</a><br/>" .
      "    <a href=\"$forum_path/index.php?action=register\">Register</a>";
    }
  ?>
</div><br />
