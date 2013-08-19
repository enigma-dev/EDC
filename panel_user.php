<div class="edcpane">
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
