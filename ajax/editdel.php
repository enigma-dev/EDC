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
