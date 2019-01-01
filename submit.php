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
require_once('cloud/edc-api.php');
require_once('upload-processing.php');

function key_or($arr, $key, $def) {
  return array_key_exists($key, $arr) ? $arr[$key] : $def;
}

if ($context['user']['is_guest'])
{
  echo "<h1>No login; perhaps it has expired. Redirecting in three seconds...</h1>";
  echo "<meta http-equiv=\"REFRESH\" content=\"3;url=" . $_POST['redirect'] . "\">";
  echo "<p>In case you're having a bad browser day, here's the message you posted:</p>";
  echo "<div style=\"border: 1px solid; padding: 16px\">" . htmlSpecialChars($_POST['message']) . "</div>";
}
else
{
  switch (key_or($_POST, 'submittype', key_or($_GET, 'action', ''))) {
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
        $selblog = mysqli_fetch_assoc($blog_query);
        if ($selblog == NULL) {
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
    case 'delblog':
        $blog_id = $_GET['blog'];
        if (empty($blog_id)) {
          echo "<h1>Error: No blog selected.</h1>";
          return false;
        }
        
        $smcFunc['db_select_db']($db_name);
        $blog_query = $smcFunc['db_query']('', 'SELECT * FROM edc_blogs WHERE id_blog={int:bid}', array("bid"=>$blog_id));
        $selblog = mysqli_fetch_assoc($blog_query);
        if ($selblog == NULL) {
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
    case 'game':
        $type = ($_POST['type'] == 'game' ? 'Game' : 'Example');
        if (empty($_POST['name']) || !strlen(trim($_POST['name']))) {
          echo "<h1>Error</h1>\n" . $type . " must have a title.";
          return;
        }
        $game_name = $_POST['name'];
        if (empty($_POST['description'])) {
          echo "<h1>Error</h1>\nPlease enter at least a brief description of your " . strtolower($type) . ".";
          return;
        }
        $game_description = $_POST['description'];
        // print_r($_POST); echo "<br /><br />"; print_r($_FILES);
        
        // Prepare files for cloud upload
        $files = Array();
        for ($file_no = 0; $file_no < 100; ++$file_no) {
          $file_key = 'dlfile_' . $file_no;
          $url_key  = 'dllink_' . $file_no;
          $name_key = 'dlname_' . $file_no;
          $game_fname = stage_file_or_url($file_key, $url_key, 'file', $file_no);
          $game_vname = key_or($_POST, $name_key, NULL);

          if ($game_fname === NULL) break;
          if (!empty($game_fname['local'])) {
            if (empty($game_vname)) {
              echo "<h1>Error</h1> You supplied " . htmlspecialchars($game_fname['user']) .
                   " but forgot to specify a name for the download (to help users choose the right version).";
              return;
            }
            $files[$file_no] = Array(
              'name' => $game_vname,
              'file' => $game_fname['local'],
              'user-fname' => $game_fname['user'],
            );
          }
        }

        if (empty($files)) {
          echo "<h1>Error</h1> You must provide at least one download file.";
          return;
        }

        // Prepare screenshots for cloud upload
        $screens = Array();
        for ($screen_no = 0; $screen_no < 100; ++$screen_no) {
          $file_key = 'screenf_' . $screen_no;
          $url_key = 'screenu_' . $screen_no;
          $screen_fname = stage_file_or_url($file_key, $url_key, 'screenshot', $screen_no);
          if ($screen_fname === NULL) break;
          if (!empty($screen_fname['local'])) {
            $screens[$screen_no] = $screen_fname['local'];
          }
        }

        $thumbnail = tempnam(sys_get_temp_dir(), 'edc');
        $thumbsrc = key_or($_POST, 'thumbsrc', NULL);
        if ($thumbsrc === 'custom') {
          $screen_no = intval($_POST['thumbsrc_screen']);
          if (!array_key_exists($screen_no, $screens)) {
            echo "<h1>Error</h1> Failed to generate thumbnail: selected source screenshot (' . $screen_no . ') not provided!";
            return;
          }
          $thumbin = $screens[$screen_no];
          $frame_selection = (strtolower($_POST['thumb_frameid']) !== 'none') ?
              intval($_POST['thumb_frameid']) : NULL;
          if (!generate_game_thumbnail($thumbin, $frame_selection,
                                       intval($_POST['crop_x']), intval($_POST['crop_y']),
                                       intval($_POST['crop_w']), intval($_POST['crop_h']),
                                       $thumbnail)) {
            echo '<h1>Error</h1> Failed to generate game thumbnail. Was the screenshot a valid image?';
            return;
          }
          echo '<br /><img src="scrtest/tthump.png" alt="Crap." /><br/><br/>';
        }

        elseif ($thumbsrc === 'generated') {
          $thumbin = NULL;
          foreach ($screens as $unused => $thumbin) break;
          if (empty($thumbin)) {
            echo "<h1>Error</h1> I can't generate a thumbnail for you without a screenshot. " .
                 "Also, have you considered using the editor?";
            return;
          }
          if (!generate_game_thumbnail_gross($thumbin, $thumbnail)) {
            echo '<h1>Error</h1> Failed to generate game thumbnail. Was the screenshot a valid image?';
            return;
          }
          echo '<br /><img src="scrtest/tthump.png" alt="Crap." /><br/><br/>';
        }

        elseif ($thumbsrc === 'upload') {
          $thumbin = key_or(key_or($_FILES, 'thumbfile', NULL), 'tmp_name', NULL);
          if (empty($thumbin)) {
            echo "<h1>Error</h1> No thumbnail image provided!";
            return;
          }
          if (!validate_game_thumbnail($thumbin, $thumbnail)) {
            echo "<h1>Error</h1> Provided thumbnail is not a valid image...";
            return;
          }
          echo '<br /><img src="scrtest/tthump.png" alt="Crap." /><br/><br/>';
        }

        else {
          echo "<h1>Error</h1> No thumbnail selected!";
          return;
        }

        $genre = key_or($_POST, 'genre', '');
        if (empty($genre))
          $genre = "";
        $wip = key_or($_POST, 'wip', 'false') === 'true' ? 1 : 0;

        // if ($context['user']['name'] != "Josh @ Dreamland") {
        //   die("<h1>Hey!</h1> Finishing touches! Try again shortly.<br/><br/><br/>");
        // }
	// upload_game_data(123, $thumbnail, $screens, $files); die();

        // Make a new thread for this game
        $smcFunc['db_select_db']($db_name);
        $smcFunc['db_insert']('insert', 'edc_threads',
                  array('id_author' => 'int'),
                  array($context['user']['id']), 
                  array());
        $thread_id = $smcFunc['db_insert_id']('edc_threads', 'id_thread');
        
        // Use a placeholder thumbnail until we finish the cloud upload.
        $ph_thumbnail = 'enigma-dev.org/edc/images/default.gif';
        
        // Insert the game
        $smcFunc['db_insert']('insert', 'edc_games',
                  array('id_author' => 'int', 'id_thread'=>'int', 'name' => 'string',
                        'text'=>'string', 'image'=>'string', 'type'=>'string',
                        'wip'=>'int', 'genre'=>'string'),
                  array($context['user']['id'], $thread_id, $game_name,
                        $game_description, $ph_thumbnail, $type, $wip, $genre),
                  array());
        $game_id = $smcFunc['db_insert_id']('edc_games', 'id_game');
        
        // Upload all game data to cloud.
        $stat = upload_game_data($game_id, $thumbnail, $screens, $files);
        
        // Print any errors
        foreach ($stat['errors'] as $error) {
          echo '<div class="edc-error">' . htmlspecialchars($error) . '</div>';
        }
        if (empty($stat['successful'])) {
          echo '<div class="edc-notice">All uploads seem to have failed. Is it foggy outside? Perhaps the cloud is down.<br/>' .
               'Your game has been saved without this information; you may edit it and upload these files later.</div>';
        } else foreach ($stat['successful'] as $file) {
          echo '<div class="edc-success">Successfully uploaded ' . htmlspecialchars($file) . '.</div>';
        }
        $thumbnail = $stat['thumbnail-url'];
        
        // Finish the game upload
        $smcFunc['db_query']('',
                  'UPDATE edc_games SET image={string:thumb}, uploading=0 ' .
                  'WHERE id_game={int:tid}',
                  array('tid' => $game_id, 'thumb' => $thumbnail));

        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=games.php?game=" . $game_id . "\">";
        echo "<h3>Your game has been posted.</h3>\n";
        echo "You should be redirected to <a href=\"games.php?game=" . $game_id . "\">your game</a> shortly.";
      break;
  case 'delgame':
        $game_id = $_GET['game'];
        if (empty($game_id)) {
          echo "<h1>Error: No game selected.</h1>";
          return false;
        }
        
        $smcFunc['db_select_db']($db_name);
        $game_query = $smcFunc['db_query']('', 'SELECT * FROM edc_games WHERE id_game={int:gid}', array("gid"=>$game_id));
        $selgame = mysqli_fetch_assoc($game_query);
        if ($selgame == NULL) {
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
          echo "<h1>Error: No game selected.</h1> How did you even submit this form...?";
          return false;
        }
        
        $smcFunc['db_select_db']($db_name);
        $game_query = $smcFunc['db_query']('', 'SELECT * FROM edc_games WHERE id_game={int:gid}', array("gid"=>$game_id));
        $selgame = mysqli_fetch_assoc($game_query);
        if ($selgame == NULL) {
          echo "<h1>ERROR: Game does not exist</h1>";
          return;
        }
        if ($selgame['id_author'] != $context['user']['id']) {
          echo "<h1>ERROR: You do not have permission to edit this game!</h1>";
          return;
        }
        
        die("<h1>Sorry...</h1> As big a pain in the ass as the submit form was, "  .
            "this edit form is going to be a bigger one. I'm not doing it today. " .
            "ask me on Discord to implement it or to edit your game for you.");
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
