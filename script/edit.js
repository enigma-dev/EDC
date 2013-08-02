/* Implements a couple convenience functions to handle 
** querying and comment editing, &c.
*/

last_comment = 0;

function do_edit(comment, frm)
{
  var cmnt = document.getElementById('commentBody' + comment);
  if (cmnt != null) {
    cmnt.innerHTML = 
      '<form name="editForm' + comment + '" onsubmit="return finish_edit(' + comment + ');">\
        <textarea id="editTextarea' + comment + '" style="width: 420px;" disabled="true">...</textarea><br />\
        <input type="submit" value="Finish edit" />\
      </form>';
  }
  $.get('ajax/editdel.php?action=getc&id=' + comment, {}, mk_populate_edit(comment));
}

function mk_populate_edit(comment) {
  var cid = comment;
  return function(txt,sammat) {
    var cedit = document.getElementById('editTextarea' + cid);
    if (cedit != null && cedit.value == "...") {
      cedit.value = txt;
      cedit.disabled = false;
    }
  }
}

function finish_edit(comment) {
  var cedit = document.getElementById('editTextarea' + comment);
  if (cedit != null) {
    $.post('ajax/editdel.php?action=putc', {id: comment, message: cedit.value}, mk_complete_edit(comment));
  }
  return false;
}

function mk_complete_edit(comment) {
  var cid = comment;
  return function(txt,sammat) {
    var cmnt = document.getElementById('commentBody' + comment);
    if (cmnt != null)
      cmnt.innerHTML = txt;
  }
}

function do_delete(comment) {
  var cid = comment;
  var cmnt = document.getElementById('commentBody' + comment);
  if (cmnt != null)
    cmnt.innerHTML += 
    '<div class="ajaxNotice">Delete this comment?<br />\
     <input type="button" onclick="confirm_delete(\'' + comment + '\');" value="Confirm">\
     <input type="button" onclick="cancel_delete(this);" value="Cancel"></div>';
}

function confirm_delete(comment) {
  $.get('ajax/editdel.php?action=delc&id=' + comment, {}, mk_complete_delete(comment));
}


function mk_complete_delete(comment) {
  var cid = comment;
  return function(txt,sammat) {
    var cwhole = document.getElementById('comment' + cid);
    if (cwhole != null) {
      cwhole.innerHTML = txt;
    }
  }
}

function cancel_delete(button) {
  var pane = button.parentNode;
  var comment = pane.parentNode;
  comment.removeChild(pane);
}

function confirmDelete(what) {
  return confirm("Are you sure you want to delete this " + what + "?");
}

