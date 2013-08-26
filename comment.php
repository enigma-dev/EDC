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
  echo "<meta http-equiv=\"REFRESH\" content=\"3;url=" . htmlspecialchars($_POST['redirect']) . "\">";
  echo "<p>In case you're having a bad browser day, here's the message you posted:</p>";
  echo "<div style=\"border: 1px solid; padding: 16px\">" . htmlSpecialChars($_POST['message']) . "</div>";
}
else
{
  $action = isset($_GET['action']) ? $_GET['action'] : 'comment';
  switch ($action)
  {
    case 'comment':
        if (empty($_POST['message']))
        {
          echo "<meta http-equiv=\"REFRESH\" content=\"2;url=" . htmlspecialchars($_POST['redirect']) . "\">";
          echo "<h1>Error</h1>\nPost contained no text. You will be redirected shortly.";
          return;
        }
        $smcFunc['db_select_db']($db_name);
        $iq = $smcFunc['db_insert']('insert', 'edc_comments',
        	         array('id_author' => 'int',  'id_thread' => 'int', 'message' => 'string'),
        	         array($context['user']['id'], $_POST['thread_id'], $_POST['message']), 
        	         array());
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=" . htmlspecialchars($_POST['redirect']) . "\">";
        echo "<h3>Your comment has been posted.</h3>\n";
        echo "You should be redirected to <a href=\"" . htmlspecialchars($_POST['redirect']) . "\">" . htmlspecialchars($_POST['redirect']) . "</a> shortly.";
      break;
    case 'edit':
        $smcFunc['db_select_db']($db_name);
      break;
    case 'delete':
        $smcFunc['db_select_db']($db_name);
      break;
  }
}
?>
