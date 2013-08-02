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
