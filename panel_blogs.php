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

<div
class="edcpane">
  <div class="edctitlebar">Blogs</div>
  <?php
    // Grab some blogs to display
    $smcFunc['db_select_db']($db_name);
    $fp_blog_q = $smcFunc['db_query']('', 'SELECT * FROM edc_blogs WHERE frontpage=true ORDER BY id_blog DESC LIMIT 10',array());
    $wrote_blog = false;
    while (($fp_blog = mysql_fetch_assoc($fp_blog_q)) !== false) {
      $fp_lmd = loadMemberData(array($fp_blog['id_author']));
      $fp_lmc = loadMemberContext($fp_lmd[0]);
      $fp_blog_author_info = $memberContext[$fp_lmd[0]]; //$user_profile[$comment['id_author']];
      $poster = $fp_blog_author_info['name'];
      echo "<a href=\"blogs.php?action=comments&blog=" . $fp_blog['id_blog'] . "\" title=\"Posted " . $fp_blog['date'] . " by " . $poster. "\">" . htmlspecialchars($fp_blog['title']) . "</a><br/>";
      $wrote_blog = true;
    }
    if (!$wrote_blog)
      echo "<i>No one has posted a blog yet. Sadface</i>";
  ?>
</div><br />
