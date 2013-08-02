<?php
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
  switch ($_POST['submittype'] . $_GET['action'])
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
        $smcFunc['db_select_db']("enigma_forums");
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
        
        $smcFunc['db_select_db']("enigma_forums");
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
        $smcFunc['db_select_db']("enigma_forums");
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
        
        $smcFunc['db_select_db']("enigma_forums");
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
        
        $smcFunc['db_select_db']("enigma_forums");
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
        
        $smcFunc['db_select_db']("enigma_forums");
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
        
        $smcFunc['db_select_db']("enigma_forums");
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