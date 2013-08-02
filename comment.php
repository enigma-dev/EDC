<?php
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
        $smcFunc['db_select_db']("enigma_forums");
        $iq = $smcFunc['db_insert']('insert', 'edc_comments',
        	         array('id_author' => 'int',  'id_thread' => 'int', 'message' => 'string'),
        	         array($context['user']['id'], $_POST['thread_id'], $_POST['message']), 
        	         array());
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=" . htmlspecialchars($_POST['redirect']) . "\">";
        echo "<h3>Your comment has been posted.</h3>\n";
        echo "You should be redirected to <a href=\"" . htmlspecialchars($_POST['redirect']) . "\">" . htmlspecialchars($_POST['redirect']) . "</a> shortly.";
      break;
    case 'edit':
        $smcFunc['db_select_db']("enigma_forums");
      break;
    case 'delete':
        $smcFunc['db_select_db']("enigma_forums");
      break;
  }
}
?>
