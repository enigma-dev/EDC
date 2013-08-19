<div class="edcpane">
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
    while(($comment = mysql_fetch_assoc($comments_query)) !== false)

    {
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
    if (!$context['user']['is_guest'])
    {
      $uri = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] 
                                         : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      echo "<b>Leave a comment</b><br />\n" .
           "<form action=\"comment.php\" method=\"post\">\n" .
           "  <textarea style=\"width:500px\" name=\"message\"></textarea><br />\n" .
           "  <input type=\"hidden\" name=\"thread_id\" value=\"" . $thread_id . "\" />\n" .
           "  <input type=\"hidden\" name=\"redirect\" value=\"" . $uri . "\" />\n" .
           "  <input type=\"submit\" value=\"Submit\" />" .
           "</form>";
    }
  ?>
</div><br />
