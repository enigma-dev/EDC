 <div class="edcpane">
  <?php
    echo "  <div class=\"edctitlebar\">" . $member['name'] . "</div>\n";
    echo "  <div class=\"edcContent\">\n";
    echo "  <center>" . $member['avatar']['image'] . "<br /></center>\n";
    echo "  <a href=\"/forums/index.php?action=profile;u=" . $member_id . "\">Profile</a><br /><br />";
    echo "  <b>Badges</b><br /><div class=\"edcBadges\">&nbsp;</div><br />\n";
    
    $smcFunc['db_select_db']($db_name);
    $comments_query = $smcFunc['db_query']('', 'SELECT * FROM edc_games WHERE id_author={int:aid} ORDER BY id_game', array("aid"=>$member_id));
    
    $gamecount = 0; $excount = 0;
    $gamelist = ""; $exlist = "";
    while (($gex = mysql_fetch_assoc($comments_query)) !== false) {
      if (strcasecmp($gex['type'],"Game") == 0) {
        $gamecount++;
        $gamelist .= "    <a href=\"games.php?game=" . $gex['id_game'] . "\">" . htmlspecialchars($gex['name']) . "</a><br />\n";
      } else if (strcasecmp($gex['type'],"Example") == 0) {
        $excount++;
        $exlist .= "    <a href=\"games.php?game=" . $gex['id_game'] . "\">" . htmlspecialchars($gex['name']) . "</a><br />\n";
      }
    }
    
    echo "  <b>Games (" . $gamecount . ")</b><br />";
    echo empty($gamelist) ? "<i>This user hasn't posted any games</i><br /><br />" : $gamelist . "<br />";
    echo "  <b>Examples (" . $excount . ")</b><br />";
    echo empty($exlist) ? "<i>This user hasn't posted any examples</i><br /><br />" : $exlist . "<br />";
    
    echo "</div>";
  ?>
</div><br />
