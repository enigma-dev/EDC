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

require_once('common.php');

if ($context['user']['is_guest'])
{
  echo "<h1>No login; perhaps it has expired. Redirecting in three seconds...</h1>";
  echo "<meta http-equiv=\"REFRESH\" content=\"3;url=" . $_POST['redirect'] . "\">";
  echo "<p>In case you're having a bad browser day, here's the message you posted:</p>";
  echo "<div style=\"border: 1px solid; padding: 16px\">" . htmlSpecialChars($_POST['message']) . "</div>";
}
else
{
  switch (empty($_POST['submittype'])? $_GET['action'] : $_POST['submittype'])
  {
    case 'blog':
        if (empty($_POST['text'])) {
          echo "<h1>Error</h1>\nBlog contained no text.";
          return;
        }
        if (empty($_POST['title'])) {
          echo "<h1>Error</h1>\nBlog must have a title.";
          return;
        }
        // Make a new comment thread for this blog
        $smcFunc['db_select_db']($db_name);
        $smcFunc['db_insert']('insert', 'edc_threads',
                  array('id_author' => 'int'),
                  array($context['user']['id']), 
                  array());
        $thread_id = $smcFunc['db_insert_id']('edc_threads', 'id_thread');
        // Insert the blog
        $smcFunc['db_insert']('insert', 'edc_blogs',
                  array('id_author' => 'int',  'id_thread'=>'int', 'title' => 'string', 'text' => 'string', 'frontpage'=>'int'),
        	        array($context['user']['id'], $thread_id,        $_POST['title'],     $_POST['text'],     ($_POST['showfront']==='true'?1:0)), 
        	        array());
        $blog_id = $smcFunc['db_insert_id']('edc_blogs', 'id_blog');
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=blogs.php?blog=" . $blog_id . "\">";
        echo "<h3>Your comment has been posted.</h3>\n";
        echo "You should be redirected to <a href=\"blogs.php?blog=" . $blog_id . "\">your new blog</a> shortly.";
      break;
    case 'editblog':
        if (empty($_POST['text'])) {
          echo "<h1>Error</h1>\nBlog contained no text.";
          return;
        }
        if (empty($_POST['title'])) {
          echo "<h1>Error</h1>\nBlog must have a title.";
          return;
        }
        $blog_id = $_POST['blogid'];
        if (empty($blog_id)) {
          echo "<h1>ERROR: No blog selected</h1>";
          return;
        }
        
        $smcFunc['db_select_db']($db_name);
        $blog_query = $smcFunc['db_query']('', 'SELECT * FROM edc_blogs WHERE id_blog={int:bid}', array("bid"=>$blog_id));
        $selblog = mysql_fetch_assoc($blog_query);
        if ($selblog === false) {
          echo "<h1>ERROR: Blog does not exist</h1>";
          return;
        }
        if ($selblog['id_author'] != $context['user']['id']) {
          echo "<h1>ERROR: You do not have permission to edit this blog</h1>";
          return;
        }
        
        $text = $_POST['text'];
        if ($context['user']['id'] == 375) { $text .= "\n\n\na1$0 cn n1e hlp w/ ym english ti suxx0rs s0 b4d tir s0 fkken h0rrbl i jst dnn0 wt 2 d0"; }
        
        $smcFunc['db_query']('', 'UPDATE edc_blogs SET title={string:title}, text={string:text}, frontpage={int:showfront} WHERE id_blog={int:bid}',
                  array('title' => $_POST['title'], 'text' => $text, 'showfront' => ($_POST['showfront']==='true'?1:0), 'bid' => $blog_id));
        
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=blogs.php?action=comments&blog=" . $blog_id . "\">";
        echo "<h3>Your blog has been updated.</h3>\n";
        echo "You should be redirected to <a href=\"blogs.php?action=comments&blog=" . $blog_id . "\">your new blog</a> shortly.";
      break;
    case 'game':
        echo "<i>Sorry!</i> The EDC is <i>still</i> down while we move to cloud storage. This is a painful process requiring work with third-party APIs.<br/><br/><br/><pre>";
        print_r($_POST);
        $thumbfn = '';
        if ($_POST['thumbsrc'] === 'custom') {
          var_dump($_FILES);
          $screen_no = 0;
          if (array_key_exists('screenf_' . $screen_no, $_FILES))
            $thumbfn = $_FILES['screenf_' . $screen_no]['tmp_name'];
            if (empty($thumbfn))
              $thumbfn = '';
            
            $outname = '/var/www/html/enigma-dev.org/edc/scrtest/tthump.png';
            
            // $imgmc = 'convert -composite "' . $thumbfn . '"' . "'[154x96!]'" . ' images/frames/frame1.png "' . $outname . '"';
            // echo($imgmc);
            
            // echo system($imgmc);
            // echo '<br/><img src="tthump.png" />';
            
            $desw = 154;
            $desh = 96;
            $x = intval($_POST['crop_x']);
            $y = intval($_POST['crop_y']);
            $w = intval($_POST['crop_w']);
            $h = intval($_POST['crop_h']);
            if ($w < $desw) $w = $desw;
            if ($h < $desh)  $h = $desh;
            $imm = new Imagick($thumbfn);
            echo 'all good<br/>';
            $imm->cropImage($w, $h, $x, $y);
            $imm->scaleImage(154, 96);
            echo 'keeping on<br/>';
            if (strtolower($_POST['thumb_frameid']) !== 'none')
              $imm->compositeImage(new Imagick('images/frames/frame' . intval($_POST['thumb_frameid']) . '.png'), imagick::COMPOSITE_ATOP, 0, 0);
            echo 'final stretch<br />';
            try {
              $imm->writeImage($outname);
              echo 'still ok<br/>';
            }
            catch (Exception $e) {
              echo $e;
            }
            
            echo '<br /><img src="scrtest/tthump.png" alt="Crap." />';
        }
        echo "</pre>";
        
        
        
        die();
        if (empty($_POST['dllink'])) {
          echo "<h1>Error</h1>\nNowhere to download!";
          return;
        }
        $type = ($_POST['type'] == 'game' ? 'Game' : 'Example');
        if (empty($_POST['name'])) {
          echo "<h1>Error</h1>\n" . $type . " must have a title.";
          return;
        }
        if (empty($_POST['description'])) {
          echo "<h1>Error</h1>\nPlease enter at least a brief description of your " . strtolower($type) . ".";
          return;
        }
        $image = $_POST['thumb'];
        if (empty($image) || $image == "http://")
          $image = "images/default.gif";
        $genre = $_POST['genre'];
        if (empty($genre))
          $genre = "";
        $wip = $_POST['wip'] === 'true' ? 1 : 0;
        
        // Make a new thread for this game
        $smcFunc['db_select_db']($db_name);
        $smcFunc['db_insert']('insert', 'edc_threads',
                  array('id_author' => 'int'),
                  array($context['user']['id']), 
                  array());
        $thread_id = $smcFunc['db_insert_id']('edc_threads', 'id_thread');
        
        // Insert the game
        $smcFunc['db_insert']('insert', 'edc_games',
                  array('id_author' => 'int', 'id_thread'=>'int', 'name' => 'string', 
                        'text'=>'string', 'image'=>'string', 'type'=>'string', 'wip'=>'int', 'genre'=>'string',
                        'dllink'=>'string'),
        	        array($context['user']['id'], $thread_id, $_POST['name'],  
                        $_POST['description'],  $image, $type, $wip, $genre,
                        $_POST['dllink']), 
        	        array());
        $game_id = $smcFunc['db_insert_id']('edc_games', 'id_game');
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=games.php?game=" . $game_id . "\">";
        echo "<h3>Your game has been posted.</h3>\n";
        echo "You should be redirected to <a href=\"games.php?game=" . $game_id . "\">your game</a> shortly.";
      break;
  case 'delblog':
        $blog_id = $_GET['blog'];
        if (empty($blog_id)) {
          echo "<h1>Error: No blog selected.</h1>";
          return false;
        }
        
        $smcFunc['db_select_db']($db_name);
        $blog_query = $smcFunc['db_query']('', 'SELECT * FROM edc_blogs WHERE id_blog={int:bid}', array("bid"=>$blog_id));
        $selblog = mysql_fetch_assoc($blog_query);
        if ($selblog === false) {
          echo "<h1>ERROR: Blog does not exist</h1>";
          return;
        }
        if ($selblog['id_author'] != $context['user']['id']) {
          echo "<h1>ERROR: You do not have permission to delete this blog!</h1>";
          return;
        }
        
        $smcFunc['db_query']('', 'DELETE FROM edc_blogs  WHERE id_blog={int:bid}', array('bid' => $blog_id));
        $smcFunc['db_query']('', 'DELETE FROM edc_threads  WHERE id_thread={int:tid}', array('tid' => $selblog['id_thread']));
        $smcFunc['db_query']('', 'DELETE FROM edc_comments WHERE id_thread={int:tid}', array('tid' => $selblog['id_thread']));
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=blogs.php?u=" . $selblog['author'] . "\">";
        echo "<h3>Your blog has been deleted.</h3>\n";
        echo "You should be redirected to <a href=\"blogs.php?u=" . $selblog['author'] . "\">the blog page</a> shortly.";
      break;
  case 'delgame':
        $game_id = $_GET['game'];
        if (empty($game_id)) {
          echo "<h1>Error: No game selected.</h1>";
          return false;
        }
        
        $smcFunc['db_select_db']($db_name);
        $game_query = $smcFunc['db_query']('', 'SELECT * FROM edc_games WHERE id_game={int:gid}', array("gid"=>$game_id));
        $selgame = mysql_fetch_assoc($game_query);
        if ($selgame === false) {
          echo "<h1>ERROR: Game does not exist</h1>";
          return;
        }
        if ($selgame['id_author'] != $context['user']['id']) {
          echo "<h1>ERROR: You do not have permission to delete this game!</h1>";
          return;
        }
        
        $smcFunc['db_query']('', 'DELETE FROM edc_games  WHERE id_game={int:gid}', array('gid' => $game_id));
        $smcFunc['db_query']('', 'DELETE FROM edc_threads  WHERE id_thread={int:tid}', array('tid' => $selgame['id_thread']));
        $smcFunc['db_query']('', 'DELETE FROM edc_comments WHERE id_thread={int:tid}', array('tid' => $selgame['id_thread']));
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=index.php\">";
        echo "<h3>Your game has been deleted.</h3>\n";
        echo "You should be redirected to <a href=\"index.php\">the main page</a> shortly.";
      break;
    case 'editgame':
        $game_id = $_POST['game_id'];
        if (empty($game_id)) {
          echo "<h1>Error: No game selected.</h1>";
          return false;
        }
        
        $smcFunc['db_select_db']($db_name);
        $game_query = $smcFunc['db_query']('', 'SELECT * FROM edc_games WHERE id_game={int:gid}', array("gid"=>$game_id));
        $selgame = mysql_fetch_assoc($game_query);
        if ($selgame === false) {
          echo "<h1>ERROR: Game does not exist</h1>";
          return;
        }
        if ($selgame['id_author'] != $context['user']['id']) {
          echo "<h1>ERROR: You do not have permission to edit this game!</h1>";
          return;
        }
        
        die("Submit form will be implemented before edit form.");
        if (empty($_POST['dllink'])) {
          echo "<h1>Error</h1>\nNowhere to download!";
          return;
        }
        $type = ($_POST['type'] == 'game' ? 'Game' : 'Example');
        if (empty($_POST['name'])) {
          echo "<h1>Error</h1>\n" . $type . " must have a title.";
          return;
        }
        if (empty($_POST['description'])) {
          echo "<h1>Error</h1>\nPlease enter at least a brief description of your " . strtolower($type) . ".";
          return;
        }
        $image = $_POST['thumb'];
        if (empty($image) || $image == "http://")
          $image = "images/default.gif";
        $genre = $_POST['genre'];
        if (empty($genre))
          $genre = "";
        $wip = $_POST['wip'] === 'true' ? 1 : 0;
        
        $smcFunc['db_select_db']($db_name);
        $smcFunc['db_query']('', 'UPDATE edc_games SET name={string:gn}, text={string:txt}, image={string:img}, ' .
                             'type={string:gt}, wip={int:wip}, genre={string:gg}, dllink={string:dll} WHERE id_game={int:gid}',
        	        array('gn'=>$_POST['name'], 'txt'=>$_POST['description'], 'img'=>$image, 'gt'=>$type,
                        'wip'=>$wip, 'gg'=>$genre, 'dll'=>$_POST['dllink'], 'gid'=>$game_id));
        
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=games.php?game=" . $game_id . "\">";
        echo "<h3>Your game has been updated.</h3>\n";
        echo "You should be redirected to <a href=\"games.php?game=" . $game_id . "\">your game</a> shortly.";
      break;
    default:
        echo "<h1>SNOO PINGAS USUAL, I SEE</h1>";
      break;
  }
}
?>
