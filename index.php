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
$gamesPerRow = 3;
echo "
  <div class=\"edcmainpane\">
    <div class=\"edcpane\">
      <div class=\"edctitlebar\">Recent Games</div>
      <table cols=3 rows=2>";
        $postedgame = false;
        while (($game = mysqli_fetch_assoc($fp_games)) != NULL) 
        {
          $postedgame = true;
          if (($thiscell++) % $gamesPerRow == 0)
            echo (($rows++) > 0 ? "
        </tr>" : "") . "
        <tr>";
          
          $lmd = loadMemberData(array($game['id_author']));
          $lmc = loadMemberContext($lmd[0]);
          $game_author_info = $memberContext[$lmd[0]]; 
          
          echo "
          <td class=\"gameEntry\">
            <a href=\"games.php?game=" . $game['id_game'] . "\" title=\"Submitted by " . htmlspecialchars($game_author_info['name']) . "\">
              <img class=\"edcGameThumb\" alt=\"" . htmlspecialchars($game['name']) . "\" src=\"" . reURL($game['image']) . "\" /><br />" . htmlspecialchars($game['name']) . "
            </a>
          </td>";
        }
        if (!$postedgame)
          echo "
        <tr><td><i><span style=\"font-size: 8pt\">No games to display...</span></i></td></tr>";
        echo "
      </table>
    </div>";

$smcFunc['db_select_db']($db_name);
$activity_query = 
"SELECT `date`, id_author, 'posted a new blog,' as action, title as place, concat('blogs.php?action=comments&blog=', id_blog) as href
   FROM `edc_blogs`
UNION SELECT `date`, id_author, concat('posted a new ', lower(type), ',') as action, name as place, concat('games.php?game=', id_game) as href
   FROM `edc_games`
UNION SELECT edc_comments.`date`, id_author, concat('commented on the ', type, ',') as action, place, href from edc_comments join (
  SELECT id_thread, `date`, 'blog' as type, title as place, concat('blogs.php?action=comments&blog=', id_blog) as href FROM `edc_blogs`
  UNION
  SELECT id_thread, `date`, lower(type) as type, name as place, concat('games.php?game=', id_game) as href FROM `edc_games`
) as mix on edc_comments.id_thread = mix.id_thread
order by date desc
limit 10";

echo "
    <br/>
    <div class=\"edcpane\">
      <div class=\"edctitlebar\">Recent Activity</div>";
      
      //echo "<textarea>" . $activity_query . "</textarea>";
      $modSettings['disableQueryCheck'] = 1;
      $eventq = $smcFunc['db_query']('', $activity_query, array());
      $modSettings['disableQueryCheck'] = 0;
      $messages = array();
      $userids = array();
      $dates = array();
      
      if ($eventq == NULL) {
        echo 'Well, crap. Try again later? Maybe?';
      } else {
        while (($event = mysqli_fetch_assoc($eventq)) != NULL) {
          array_push($messages, htmlspecialchars($event['action']) . ' <a href="' . htmlspecialchars($event['href']) . '">' . htmlspecialchars($event['place']) . '</a>' );
          array_push($userids, $event['id_author']);
          array_push($dates, $event['date']);
        }
      
        $lmd = loadMemberData($userids);
        $len = count($messages);
        for ($i = 0; $i < $len; ++$i) {
          $lmc = loadMemberContext($userids[$i]);
          $author_info = $memberContext[$userids[$i]];
          echo '
      <div class="edc_activity">
        <div class="activity_icondiv">
          <a href="blogs.php?u=' . $author_info['id'] . '">
            <img src="' . $author_info['avatar']['href'] . '" alt="A" style="width:48px; height:48px;" />
          </a>
        </div>
        <div class="activity_datadiv">
          <div class="activity_message">
            <a href="blogs.php?u=' . $author_info['id'] . '">' . $author_info['name'] . '</a> ' . $messages[$i] . '
          </div>
          <div class="activity_footer">' . $dates[$i] . '</div>
        </div>
      </div>';
        }
      }
      echo "
    </div>
  </div>";
?>
