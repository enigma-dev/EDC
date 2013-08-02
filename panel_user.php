<div class="edcpane">
  <div class="edctitlebar">User</div>
  <?php
    if ($context['user']['is_guest'])
    {
      echo "Welcome, guest!<br/>" .
      "<a href=\"http://enigma-dev.org/forums/index.php?action=login\">Login</a><br/>" .
      "<a href=\"http://enigma-dev.org/forums/index.php?action=register\">Register</a>";
    }
    else
    {
      echo "<a href=\"blogs.php?u=" . $user_info['id'] . "\">" . $user_info['name'] . "</a><br />\n";
      echo "<a href=\"http://enigma-dev.org/forums/index.php?action=pm\">Private Messages</a><br />\n";
      echo "<a href=\"blogs.php?action=new\">Post new blog</a><br />\n";
      echo "<a href=\"games.php?action=new\">Submit game</a><br />\n";
      ssi_logout();
    }
  ?>
</div><br />