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
  <div class="edctitlebar">Comments</div>
  <script language="JavaScript" src="script/edit.js"></script>
  <?php
    /*
     *   PANEL_COMMENTS.PHP
     *   Inserts a comment panel where included. Utilizes one variable:
     *     $thread_id must be the id of the thread whose comments are to be displayed
     *     $insert_rating is reserved.
    */
    $smcFunc['db_select_db']($db_name);
    $comments_query = $smcFunc['db_query']('', 'SELECT * FROM edc_comments WHERE id_thread={int:tid} ORDER BY id_comment', array("tid"=>$thread_id));
    $i = 1;
    while(($comment = mysqli_fetch_assoc($comments_query)) != NULL) {
      $lmd = loadMemberData(array($comment['id_author']));
      $lmc = loadMemberContext($lmd[0]);
      $c_author_info = $memberContext[$lmd[0]]; //$user_profile[$comment['id_author']];
      echo "
    <div class=\"edcComment\" id=\"comment" . $comment['id_comment'] . "\"><table cols=\"2\" rows=\"2\" style=\"width: 100%\"><a name=\"c" . $comment['id_comment'] . "\"></a>
      <tr>
        <td class=\"edcCommentAvatar\">" . ($lmd === false ? "images/qmark.png" : $c_author_info['avatar']['image']) . "</td>
        <td class=\"edcContent edcCommentBody\" id=\"commentBody" . $comment['id_comment'] . "\">" . parse_bbc(htmlspecialchars($comment['message'])) . "</td>
      </tr>
      <tr>
        <td class=\"edcCommentByline\" colspan=\"2\">Posted by " . 
           ($lmd !== false ? "<a href=\"blogs.php?u=" . $comment['id_author'] . "\">" . $c_author_info['name'] . "</a>" : "<i>Unknown</i>") . 
           " on " . $comment['date'] . "<span style=\"float:right\">" . 
           ($user_info['id'] === $comment['id_author'] ? "<a href=\"#c" . $comment['id_comment'] . "\" onclick=\"javascript:do_edit('" . $comment['id_comment'] . "')\">Edit</a> | " : "") .
           ($user_info['id'] === $comment['id_author'] ? "<a href=\"#c" . $comment['id_comment'] . "\" onclick=\"javascript:do_delete('" . $comment['id_comment'] . "')\">Delete</a> - " : "") .
           "#$i" .  "</span>" . "</td>
      </tr>
    </table></div>";
      $i++;
    }
    if (!$context['user']['is_guest']) {
      $uri = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] 
                                         : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      echo "<b>Leave a comment</b><br />\n" .
           "<form action=\"comment.php\" method=\"post\">\n" .
           "  <textarea style=\"width:500px\" name=\"message\"></textarea><br />\n" .
           "  <input type=\"hidden\" name=\"thread_id\" value=\"" . $thread_id . "\" />\n" .
           "  <input type=\"hidden\" name=\"redirect\" value=\"" . $uri . "\" />\n" .
           "  <input type=\"submit\" value=\"Submit\" />" .
           "</form>";
    } elseif ($i == 1) {
      echo '<i>No comments have been posted, yet. <a href="/forums/index.php?action=login">Log in</a> to post comments.</i>';
    }
  ?>
</div><br />
