<?php
ob_start();
require_once('common.php');
require_once('fuck_php.php');

$smcFunc['db_select_db']("enigma_forums");

echo "Fixme: Make this page less ugly.<br/>";
$game_list_q = $smcFunc['db_query']('', 'SELECT * FROM edc_games ORDER BY `date` DESC LIMIT 20',array());
$games_printed = 0;
echo "<table style=\"border: 3px double #0000C0; width: 75pc;\">";
while (($tgame = mysql_fetch_assoc($game_list_q)) !== false) {
  echo "<tr>";
  // Game thumbnail image
  echo "<td rowspan=\"2\" style=\"border-top: 3px double #0000C0; padding: 4px; width: 162px; height: 104px;\">"
     . "<img class=\"edcGameThumb\" alt=\"" . htmlspecialchars($tgame['name'])
     . "\" src=\"" . reURL($tgame['image']) . "\" style=\"vertical-align:top\"/></td>";
  
  $sbs = "border-top: 3px double #0000C0; border-right: 1px solid #0000CO; padding: 3px; height: 24px;";
  echo "<td style=\"$sbs\"><a href=\"games.php?game=" 
     . $tgame["id_game"] . '">' . htmlspecialchars($tgame["name"]) . "</a> <i>by</i> <a href=\"blogs.php?u=" 
     . $tgame["id_author"] . '">' . htmlspecialchars("Author name here") . "</a></td>";
  
  echo "</tr><tr><td style=\"border: 1px solid #0000CO\; padding: 3px; height: 80px;\"><div style=\"overflow: auto; height: 80px;\">"
     . parse_bbc(htmlspecialchars($tgame['text'])) . "</div></td>";
  
  echo "</td></tr>\n";
  $games_printed++;
}
echo "</table>";
if ($games_printed == 0) {
  echo "<h1>Failed to list games.</h1>";
  return false;
}
?>
