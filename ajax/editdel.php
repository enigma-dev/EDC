<?php
require_once('../config.php');
switch ($_GET['action'])
{
  case 'getc':
      $smcFunc['db_select_db']($db_name);
      $comment_query = $smcFunc['db_query']('', 'SELECT * FROM edc_comments WHERE id_comment={int:cid}', array("cid"=>$_GET['id']));
      $cmnt = mysql_fetch_assoc($comment_query);
      if ($cmnt !== false)
        echo $cmnt['message']; // With HTML special chars: JavaScript will take care of that part.
    break;
  case 'putc':
      $smcFunc['db_select_db']($db_name);
      $comment_query = $smcFunc['db_query']('', 'SELECT * FROM edc_comments WHERE id_comment={int:cid}', array("cid"=>$_POST['id']));
      $cmnt = mysql_fetch_assoc($comment_query);
      if ($cmnt === false) {
        echo "<h1>Error.</h1>";
        break;
      }
      if ($cmnt['id_author'] !== $context['user']['id'])
      {
        if (($lmd = loadMemberData(array($cmnt['id_author']))) === false)
          echo "<h1>YOU</h1><h5>ARE NOT</h5><h2>THE AUTHOR</h2><h6>OF THIS COMMENT</h6><h4>YOU SHIT</h4>";
        else {
          $lmc = loadMemberContext($lmd[0]);
          $author_info = $memberContext[$lmd[0]];
          echo "<h1>YOU</h1><h5>ARE NOT</h5><h2>" . $author_info['name'] . "</h2><h4>YOU SHIT</h4>";
        }
        break;
      }
      $smcFunc['db_query']('', 'UPDATE edc_comments SET message={string:msg} WHERE id_comment={int:cid}', array("cid"=>$_POST['id'],"msg"=>$_POST['message']));
      echo parse_bbc(htmlspecialchars($_POST['message']));
    break;
  case 'delc':
      $smcFunc['db_select_db']($db_name);
      $comment_query = $smcFunc['db_query']('', 'SELECT * FROM edc_comments WHERE id_comment={int:cid}', array("cid"=>$_GET['id']));
      $cmnt = mysql_fetch_assoc($comment_query);
      if ($cmnt === false) {
        echo "<h1>Error.</h1>";
        break;
      }
      if ($cmnt['id_author'] !== $context['user']['id'])
      {
        if (($lmd = loadMemberData(array($cmnt['id_author']))) === false)
          echo "<h1>YOU</h1><h5>ARE NOT</h5><h2>THE AUTHOR</h2><h6>OF THIS COMMENT</h6><h4>YOU SHIT</h4>";
        else {
          $lmc = loadMemberContext($lmd[0]);
          $author_info = $memberContext[$lmd[0]];
          echo "<h1>YOU</h1><h5>ARE NOT</h5><h2>" . $author_info['name'] . "</h2><h4>YOU SHIT</h4>";
        }
        break;
      }
      $smcFunc['db_query']('', 'DELETE FROM edc_comments WHERE id_comment={int:cid}', array("cid"=>$_GET['id']));
      echo "<div class=\"ajaxNotice\">Comment deleted</div>";
    break;
  default:
    echo "<h1>ERROR</h1>";
}
?>
