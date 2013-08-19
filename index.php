<?php
ob_start();
require_once('common.php');
require_once('fuck_php.php');
$smcFunc['db_select_db']($db_name);
$_GET['action'] = 'edc_index';

echo
"<div class=\"edcpanes_left\">\n";
include('panel_user.php');
include('panel_blogs.php');
include('panel_activeusers.php');
echo "</div>";

// Game section

$fp_games = $smcFunc['db_query']('', 'SELECT * FROM edc_games ORDER BY id_game DESC LIMIT 6');
$thiscell = 0;
$rows = 0;
echo "<div class=\"edcmainpane\">";
  echo
  "  <div class=\"edcpane\">
     <div class=\"edctitlebar\">Recent Games</div>
        <table cols=3 rows=2>\n";
        $postedgame = false;
	      while(($game = mysql_fetch_assoc($fp_games)) !== false) 
        {
          $postedgame = true;
          if (($thiscell++) % 3 == 0) echo (($rows++) > 0 ? "        </tr><tr>\n" : "        <tr>\n");
          
          $lmd = loadMemberData(array($game['id_author']));
          $lmc = loadMemberContext($lmd[0]);
          $game_author_info = $memberContext[$lmd[0]]; 
          
          echo "          <td class=\"gameEntry\"><a href=\"games.php?game=" . $game['id_game'] . "\" title=\"Submitted by " . htmlspecialchars($game_author_info['name']) . "\">\n" . 
               "              <img class=\"edcGameThumb\" alt=\"" . htmlspecialchars($game['name']) . "\" src=\"" . reURL($game['image']) . "\" /><br />" . htmlspecialchars($game['name']) . "</a></td>\n";
        }
        if (!$postedgame)
          echo "<tr><td><i><span style=\"font-size: 8pt\">No games to display...</span></i></td>";
        echo "        </tr>
        </table>
     </div>
  </div>";
?>
