<div id="submenu">
  <ul>
<?php
  echo "    <li><a href=\"index.php\">Home</a></li>\n";
  if (!$context['user']['is_guest'])
    echo "    <li><a href=\"blogs.php?u=" . $context['user']['id'] . "\">My Page</a></li>\n";
  echo "    <li><a href=\"blogs.php?action=list\">Blogs</a></li>\n";
  echo "    <li><a href=\"games.php?action=list\">Games</a></li>\n";
?>
  </ul>
</div>
