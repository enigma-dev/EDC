<div class="edcpane">
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
