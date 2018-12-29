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

ob_start();
require_once('common.php');
require_once('reURL.php');

$smcFunc['db_select_db']($db_name);

$action = isset($_GET['action']) ? $_GET['action'] : 'view';
switch ($action)
{
  case 'view':
    // Where are we?
    $_GET['action'] = 'edc_game_view';
    
    // Grab some info about the game we're displaying
    $game_id = !empty($_GET['game']) ? $_GET['game'] : -1;
    $game_info_q = $smcFunc['db_query']('', 'SELECT * FROM edc_games WHERE id_game={int:gid}',array("gid" => $game_id));
    if (($game_info = mysqli_fetch_assoc($game_info_q)) == NULL) {
      echo "Game doesn't exist.";
      return false;
    }
    
    // Grab game files and screenshots
    require_once('file-api.php');
    $gfiles = get_game_files($game_id);
    
    echo "<div class=\"edcpanes_left\">\n";
    echo "<div class=\"edcpane\">
      <div class=\"edctitlebar\">Game Information</div>";
        if (($lmd = loadMemberData(array($game_info['id_author']))) === false)
          echo "        <b>Author:</b><br /><div style=\"padding:24px 0px 0px 12px\">unknown</div>\n";
        else
        {
          $lmc = loadMemberContext($lmd[0]);
          $author_info = $memberContext[$lmd[0]];// $user_profile[$game_info['id_author']];
          echo "        <center><div class=\"edcAvatar\">" . $author_info['avatar']['image'] . "</div></center>\n" .
               "        <b>Author:</b><br /><div style=\"padding: 0px 0px 12px 24px\"><a href=\"blogs.php?u=" . $game_info['id_author'] . "\">" . $author_info['name'] . "</a></div>\n";
        }

        echo   "        <b>Rating:</b><br /><div style=\"padding: 0px 0px 12px 24px\">" . ($game_info['ratecount'] ? $game_info['totalrating'] / $game_info['ratecount'] : "Unrated") . "</div>\n";
        echo   "        <b>Type:</b><br /><div style=\"padding: 0px 0px 12px 24px\">" . htmlspecialchars($game_info['type']) . "</div>\n";
        echo   "        <b>Genre:</b><br /><div style=\"padding: 0px 0px 12px 24px\">" . htmlspecialchars($game_info['genre']) . "</div>\n";
        echo   "        <b>Submited:</b><br /><div style=\"padding: 0px 0px 12px 24px\">" . htmlspecialchars($game_info['date']) . "</div>\n";
        echo   "        <b>Download" . (count($gfiles['links']) == 1? "" : "s (" . count($gfiles['links']) . ")") . ":</b><br />";
        for ($i = 0; $i < count($gfiles['links']); ++$i)
          echo "<div style=\"padding: 0px 0px 12px 24px\"><a href=\"" . $gfiles['links'][$i] . "\">" . $gfiles['cats'][$i] . "</a></div>\n";
      echo "</div><br />";
    include('panel_activeusers.php');
    
    // Main panel, showing game
    echo "</div><div class=\"edcmainpane\">";
      echo "  <div class=\"edcpane\">\n    <div class=\"edctitlebar\">Recent Games</div>\n";
        echo "      <div class=\"edcContent\">\n";
        echo "      <h1 class=\"edcGameTitle\">" . htmlspecialchars($game_info['name']) . "</h1>\n";
        echo "      <div class=\"edcGameImage\"><img class=\"edcGameThumb\" alt=\"" . htmlspecialchars($game_info['name']) . "\" src=\"" . reURL($game_info['image']) . "\" /></div>\n";
        echo parse_bbc(htmlspecialchars($game_info['text']));
      echo "    <div class=\"edcGameFooter\"><b>Screenshots</b>:<br />";
      foreach ($gfiles['screens'] as $screen)
        echo '<a href="' . $screen . '" target="_blank"><img class="screenshot" src="' . $screen . '" alt="Screenshot" /></a>';
      if ($game_info['id_author'] == $context['user']['id'])
        echo "<span style=\"float:right\"><a href=\"games.php?action=edit&game=" . $game_id . "\">Edit</a> | "
           . "<a href=\"submit.php?action=delgame&game=" . $game_id . "\" onclick=\"javascript:return confirmDelete('game');\">Delete</a></span>";
      echo "</div>\n";
      echo "  </div></div><br />\n";
      $thread_id = $game_info['id_thread'];
      include('panel_comments.php');
    echo "</div>";
  break;
  
  
  case 'new':
      require_once('form-game.php');
      require_once('form-designer.php');
      echo "<div class=\"edcpanes_left\">\n";
      include('panel_user.php');
      include('panel_blogs.php');
      include('panel_activeusers.php');
      echo "</div><div class=\"edcmainpane\"><div class=\"edcpane\">\n";
      echo "<div class=\"edctitlebar\">Submit Game/Example</div>\n";
      echo "<form method=\"post\" action=\"submit.php\" style=\"margin-top: 3px;\" enctype=\"multipart/form-data\">";
      $gfres = print_game_form();
      echo "<input type=\"hidden\" name=\"submittype\" value=\"game\" />";
      echo "</form></div></div>";
      print_designer_form($gfres);
    break;
    
  case 'edit':
      // Grab some info about the game we're displaying
      require_once('form-game.php');
      require_once('form-designer.php');
      
      $game_id = $_GET['game'];
      if (empty($game_id)) {
        echo "<h1>ERROR: No game to edit!</h1>";
        return false;
      }
      $game_info_q = $smcFunc['db_query']('', 'SELECT * FROM edc_games WHERE id_game={int:gid}',array("gid" => $game_id));
      if (($game_info = mysqli_fetch_assoc($game_info_q)) == NULL) {
        echo "<h1>Game doesn't exist.</h1>";
        return false;
      }
      echo "<div class=\"edcpanes_left\">\n";
      include('panel_user.php');
      include('panel_blogs.php');
      include('panel_activeusers.php');
      echo "</div><div class=\"edcmainpane\"><div class=\"edcpane\">\n";
      echo "<div class=\"edctitlebar\">Edit Game/Example</div>\n";
      echo "<form method=\"post\" action=\"submit.php\" style=\"margin-top: 3px;\">";
      $isgame = strtolower($game_info['type']) == "game";
      
      require_once('file-api.php');
      $gameFiles = get_game_files($game_id);
      $gfres = print_game_form($game_info['name'], $game_info['genre'], $game_info['text'], $isgame, $game_info['wip'], $game_info['image'], $gameFiles['cats'], $gameFiles['links'], $gameFiles['screens']);
      echo "<input type=\"hidden\" name=\"submittype\" value=\"editgame\" />";
      echo "<input type=\"hidden\" name=\"game_id\" value=\"" . $game_id . "\" />";
      echo "</form></div></div>";
      print_designer_form($gfres);
    break;
  
  
  case 'list':
      if (!empty($_GET['b'])) $begin = preg_replace("[^0-9]","",$_GET['b']);
      if (empty($begin)) $begin = 0;
      if (!empty($_GET['s'])) $show = preg_replace("[^0-9]","",$_GET['s']);
      if (empty($show) || $show > 20 || $show < 1) $show = 20;
      
      $game_list_q = $smcFunc['db_query']('', 'SELECT SQL_CALC_FOUND_ROWS * FROM edc_games ORDER BY `date` DESC LIMIT ' . $begin . ', ' . $show,array());
      $games_printed = 0;
      $games_total_q = $smcFunc['db_query']('', "SELECT FOUND_ROWS() as found");
      $games_total = mysqli_fetch_assoc($games_total_q)["found"];
      
      echo "<script type=\"text/javascript\">
        function togglescroll(obj) {
          obj.focus();
          if (obj.style.overflowY != 'scroll') {
            obj.style.overflowY = 'scroll', obj.style.overflowX = 'auto';
            return false;
          }
          return true;
        }
        function toggleoffscroll(obj) {
          obj.style.overflow=\"hidden\";
        }
</script>";
      echo "<span style=\"font-style: italic; font-size: 8px;\">Double click a description to activate scroll.</span>";
      echo "<table style=\"border: 3px double #0000C0; width: 80%;\">";
      while (($tgame = mysqli_fetch_assoc($game_list_q)) != NULL) {
        echo "<tr>";
        // Game thumbnail image
        echo "<td rowspan=\"2\" class=\"thumbnailcell\">"
           . "<a href=\"games.php?game=" . $tgame["id_game"] . "\">"
           . "<img class=\"edcGameThumb\" alt=\"" . htmlspecialchars($tgame['name'])
           . "\" src=\"" . reURL($tgame['image']) . "\" style=\"vertical-align:top\"/></a></td>";
        
        $sbs = "";
        echo "<td class=\"bylinecell\"><a href=\"games.php?game=" 
           . $tgame["id_game"] . '">' . htmlspecialchars($tgame["name"]) . "</a> <i>by</i> <a href=\"blogs.php?u=" 
           . $tgame["id_author"] . '">';
        if (($lmd = loadMemberData(array($tgame['id_author']))) === false)
          echo "Some guy who's dead now";
        else {
          $lmc = loadMemberContext($lmd[0]);
          $author_info = $memberContext[$lmd[0]];// $user_profile[$game_info['id_author']];
          echo $author_info['name'];
        }
        echo "</a></td>";
        
        echo "</tr><tr><td class=\"descriptioncell\"><div class=\"descpreview\" ondblclick=\"togglescroll(this)\" onmouseout=\"toggleoffscroll(this)\">"
           . parse_bbc(htmlspecialchars($tgame['text'])) . "</div></td>";
        
        echo "</td></tr>\n";
        $games_printed++;
      }
      echo "</table>";
      if ($games_printed == 0) {
        echo "<h1>No games to display" . (s > 0? " in the given range" : "") . ".</h1>";
        return false;
      }
      
      #$tcq = $smcFunc['db_query']('', 'SELECT NULL FROM edc_games',array());
      echo "<p style=\"width: 80%;\">Showing games " . ($begin + 1) . "-" . ($begin+$games_printed) . " of $games_total.";
      
      echo "<span style=\"float:right;\">";
      if ($begin > 0) echo "<a href=\"games.php?action=list&b=" . (floor(($begin-1)/$show)*$show) . "&s=$show\" title=\"Previous Page\">&lt;</a>";
      else echo "&lt;";
      echo "&nbsp;&nbsp;Page ";
      
      $pn = 0;
      for ($gp = 0; $gp < $games_total; $gp += $show) {
        echo "&nbsp;"; $pn++;
        if ($gp >= $begin+$show || $gp < $begin)
          echo "<a href=\"games.php?action=list&b=$gp&s=$show\" title=\"Games " . ($gp+1) . "-" . min($gp+$show,$games_total) . "\">$pn</a>&nbsp;&nbsp;";
        else echo "$pn&nbsp;&nbsp;";
      }
      
      if ($begin + $show >= $games_total) echo "&gt;";
      else echo "<a href=\"games.php?action=list&b=" . (floor(($begin+$show)/$show)*$show) . "&s=$show\" title=\"Next Page\">&gt;</a>";
      echo "</span></p>\n";
  break;
  
  
  default:
    echo "I have no idea what you want from me.";
  break;
}
?>
