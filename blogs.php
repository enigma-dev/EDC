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

require_once('common.php');
require_once('reURL.php');

// The panels to the left remain constant; load info for those here

$selblog = null;
$blog_id = !empty($_GET['blog']) ? $_GET['blog'] : -1;
$member_id = !empty($_GET['u']) ? $_GET['u'] : (!empty($_GET['user']) ? $_GET['user'] : 
             ($context['user']['is_guest'] ? 1 : $user_info['id']));
if ($blog_id != -1) {
  $smcFunc['db_select_db']($db_name);
  $blog_query = $smcFunc['db_query']('', 'SELECT * FROM edc_blogs WHERE id_blog={int:bid}', array("bid"=>$blog_id));
  $selblog = mysqli_fetch_assoc($blog_query);
  if ($selblog != NULL)
    $member_id = $selblog['id_author'];
  else
    $selblog = null;
}

$lmd = loadMemberData(array($member_id));
if ($lmd === false) echo "Failed to load member data";
//$member = $user_profile[$member_id];

$lmc = loadMemberContext($lmd[0]);
$member = $memberContext[$lmd[0]];

echo "<div class=\"edcpanes_left\">\n";
include('panel_member.php');
include('panel_activeusers.php');
echo "</div>\n";

echo "<div class=\"edcmainpane\">
  <center><a href=\"blogs.php?u=" . $member_id . "\"><img alt=\"Banner\" src=\"" . (empty($member['banner']) ? "images/Banner_Default.png" : $member['banner']) . "\" /></a></center>\n";

// Now we do the action-specific part
$action = !empty($_GET['action']) ? $_GET['action'] : "viewall";

switch ($action)
{
  case "viewall":
      echo "  <script language=\"JavaScript\" src=\"script/edit.js\"></script>\n";
      $smcFunc['db_select_db']($db_name);
      $comments_query = $smcFunc['db_query']('', 'SELECT * FROM edc_blogs WHERE id_author={int:aid} ORDER BY id_blog DESC LIMIT 10', array("aid"=>$member_id));
      $hadblogs = false;
      while (($blog = mysqli_fetch_assoc($comments_query)) != NULL)
      {
        $hadblogs = true;
        echo "  <div class=\"edcBlog\">";
        echo "<h1 class=\"edcBlogTitle\">" . htmlspecialchars($blog['title']) . "</h1><span class=\"edcBlogDate\">Posted on " . $blog['date'] . "</span><hr>";
        echo parse_bbc(htmlspecialchars($blog['text'])) . "\n    <div class=\"edcBlogOptions\">";
        if (!$context['user']['is_guest'] && ($context['user']['id'] == $blog['id_author']))
          echo "<a href=\"blogs.php?action=edit&blog=" . $blog['id_blog'] . "\">Edit</a> | " .
               "<a href=\"submit.php?action=delblog&blog=" . $blog['id_blog'] . "\" onclick=\"javascript:return confirmDelete('blog')\">Delete</a> | ";
        $ccount_query = $smcFunc['db_query']('', 'SELECT * FROM edc_comments WHERE id_thread={int:tid}', array("tid"=>$blog['id_thread']));
        $cc = mysqli_num_rows($ccount_query);
        echo "<a href=\"blogs.php?action=comments&blog=" . $blog['id_blog'] . "\">Comments" . (empty($cc)?"":" (".$cc.")"). "</a>";
        echo "</div></div>";
      }
      if (!$hadblogs)
        echo "<div class=\"edcBlog\">" . $member['name'] . " has not posted any blogs yet.</div>\n";
    break;
  case "comments":
      echo "<div class=\"edcBlog\">\n";
      if ($selblog == null) {
        echo "  <h1>ERROR: No blog selected</h1>\n</div>";
        break;
      }
      echo "<h1 class=\"edcBlogTitle\">" . htmlspecialchars($selblog['title']) . "</h1><span class=\"edcBlogDate\">Posted on " . $selblog['date'] . "</span><hr>";
      echo parse_bbc(htmlspecialchars($selblog['text'])) . "\n    <div class=\"edcBlogOptions\">";
        if (!$context['user']['is_guest'] && ($context['user']['id'] == $selblog['id_author']))
          echo "<a href=\"blogs.php?action=edit&blog=" . $selblog['id_blog'] . "\">Edit</a> | " .
               "<a href=\"#\">Delete</a>";
        echo "</div>";
      echo "</div>";
      $thread_id = $selblog['id_thread'];
      include('panel_comments.php');
    break;
  case "new":
      echo "<div class=\"edcBlog\">\n";
      echo "<form method=\"post\" action=\"submit.php\">" .
           "  Title: <input type=\"text\" name=\"title\" />" .
           "  <textarea rows=\"32\" style=\"width:100%\" name=\"text\"></textarea><br />" .
           "  <input type=\"checkbox\" name=\"showfront\" value=\"true\" checked=\"1\"/> Show on front page" .
           "  <input type=\"hidden\" name=\"submittype\" value=\"blog\" />" .
           "  <input type=\"submit\" value=\"Post\" style=\"float:right\"/>" .
           "</form>";
      echo "</div>";
    break;
  case "edit":
      echo "<div class=\"edcBlog\">\n";
      if ($selblog == null) {
        echo "  <h1>ERROR: No blog selected</h1>\n</div>";
        break;
      }
      echo "<form method=\"post\" action=\"submit.php\">" .
           "  Title: <input type=\"text\" name=\"title\" value=\"" . htmlspecialchars($selblog['title']) . "\" />" .
           "  <textarea rows=\"32\" style=\"width:100%\" name=\"text\">" . htmlspecialchars($selblog['text']) . "</textarea><br />" .
           "  <input type=\"checkbox\" name=\"showfront\" value=\"true\"" . ($selblog['frontpage']==1?" checked=\"1\"":"") . " /> Show on front page" .
           "  <input type=\"hidden\" name=\"submittype\" value=\"editblog\" />" .
           "  <input type=\"hidden\" name=\"blogid\" value=\"" . $selblog['id_blog'] . "\" />" .
           "  <input type=\"submit\" value=\"Post\" style=\"float:right\"/>" .
           "</form>";
      echo "</div>";
    break;
  default:
    echo "<h1>Unknown request.</h1>";
    break;
}       

echo "</div>"; 
?>
