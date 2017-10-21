
<?php
//Functional example to rename an object.
//Copyright (C) 2013 IsmAvatar <IsmAvatar@gmail.com>
//This file is licensed under the MIT license. See LICENSE for details.

require_once('common.php');
require_once('cloud/interface.php');
require_once('cloud/tree.php');

?>
<h1>Josh's [less than] Beautiful File Explorer</h1>
<style type="text/css">
.filetable {
  min-width: 800px;
}
.filetable th {
  font-weight: bold;
  vertical-align: middle;
  text-transform: uppercase;
  background-color: #A0C0D0;
  border: 1px solid navy;
  color: white;
}
.filetable td {
  vertical-align: middle;
  border: 1px solid navy;
  margin: 1px;
}
.filecheckbox {
  width: 24px;
  padding: 2px;
}
.filenamecell {
  padding-left: 12px;
}
.fileactioncell {
  padding: 2px;
  text-align: center;
  vertical-align: middle;
  width: 24px;
  font-size: 20px;
  padding: 1px !important;
}
.filedelcell a {
  color: #EA0000;
  text-decoration: none;
}
.filemovecell a {
  color: #A5742A;
  text-decoration: none;
}
.filecrumbspan {
  margin: 6px 8px;
  display: inline-block;
}
.fileuploadbox {
  width: 600px;
  margin-top: 32px;
}
.fileuploadcaption {
  border: 1px solid navy;
  background-color: #C0D0FF;
  text-transform: uppercase;
  font-weight: bold;
  padding: 4px;
  color: navy;
}
.fileuploadform {
  border: 1px solid navy;
  border-top: none;
  overflow: auto;
  padding: 4px;
}
.fileuploadform b {
  font-weight: bold;
}
.rawoutputwindow {
  border: 4px double navy;
  width: 600px;
  max-height: 196px;
  overflow: scroll;
  box-sizing: border-box;
}
.filepagebutton, .filepagebuttondead {
  width: 18px;
  height: 18px;
  border-radius: 10px;
  text-align: center;
  vertical-align: middle;
  display: inline-block;
  margin: 8px 8px 8px 0;
}
.filepagebutton {
  border: 1px solid navy;
  background-color: #C0D0FF;
}
.filepagebuttondead {
  border: 1px solid #404040;
  background-color: #808080;
  color: #303030;
}
.filepagebutton a {
  color: navy;
  text-decoration: none;
}
.fileopsuccess, .fileopfailure {
  padding: 4px 12px;
  border-radius: 8px;
  max-width: 720px;
  box-sizing: border-box;
  margin-bottom: 16px;
}
.fileopsuccess {
  border: 2px solid #228B22;
  background-color: #70C060;
  color: #D0F8B0;
}
.fileopfailure {
  border: 2px solid #832;
  background-color: #F42;
  color: #FFE0C0;
}
</style>
<br/>
<?php

if (!$context['user']['is_logged']) {
  die("Only administrators may view raw cloud content. No guests permitted.");
}
if (!$context['user']['is_admin']) {
  die("Only administrators may view raw cloud content. That isn't you.");
}

$s = $stores['ORD'];
$c = 'files-00000';

$prefix = (isset($_GET['path']))? $_GET['path'] : "";
if ($prefix == "/") {
  $prefix = "";
} else if (strlen($prefix) > 1) {
  if (substr($prefix, -1) != '/') {
    $prefix .= '/';
  }
}

// ============================================================================
// === Helpers ================================================================
// ============================================================================

function getName() {
  return "";
}

function pageFor($p) {
  return getName() . '?path=' . $p;
}

function pageForListBefore($p, $before) {
  return getName() . '?path=' . $p . '&before=' . $before;
}

function pageForListAfter($p, $after) {
  return getName() . '?path=' . $p . '&after=' . $after;
}

function isDirectory($file) {
  return property_exists($file, 'subdir');
}

function prefixOf($file) {
  return isDirectory($file)? $file->subdir : $file->name;
}

function delLink($f) {
  return getName() . "?del=" . prefixOf($f);
}

function delOnclick($f) {
  return "return confirmDelete('" . prefixOf($f) . "', "
      . (isDirectory($f)? 'true' : 'false') . ")";
}

function moveLink($f) {
  return "#dontgoanywhere";
}

function moveOnclick($f)	{
  return "doMove('" . prefixOf($f) . "')";
}

function filePath($file) {
  $ix = strrpos($file, '/');
  if ($ix === FALSE) {
    return "";
  }
  return substr($file, 0, $ix);
}

// ============================================================================
// === Are we doing something special? ========================================
// ============================================================================

// Handle file uploads
if (isset($_POST['upload']) && isset($_FILES['uploadfile'])) {
  $file = $_FILES['uploadfile'];
  $dest = $_POST['uploaddest'];
  if (substr($dest, -1) == '/') {
    $dest .= $file['name'];
  }
  $result = putObject($s,$c,$dest,$file,$authHdr);
  if ($result['success']) {
    echo '<div class="fileopsuccess">'
       . 'Successfully uploaded "' . $file['name'] . '" as "' . $dest
       . '" (' . $result['hash'] . ')<br />on ' . $result['date']
       . "</div>\n";
    echo '<br /><a href="?path=' . filePath($dest) . '">Jump to directory</a>';
  } else {
    echo '<div class="fileopfailure">Upload failed'
       . ' (Code ' . $result['code'] . ')<br />' . "\n"
       . $result['error']
       . "</div>\n";
  }
  exit;
}

if (isset($_GET['del'])) {
  $del = $_GET['del'];
  if (substr($del, -1) == '/') {
    echo '<div class="fileopfailure">Deleting directories is not actually'
       . ' implemented, as it\'s just too damn dangerous.</div>' . "\n";
  } else {
    $res = deleteObject($s,$c,$del,$authHdr);
    if ($res) {
      echo '<div class="fileopsuccess">File "' . htmlspecialchars($del)
         . '" was deleted successfully.</div>';
    } else {
       echo '<div class="fileopfailure">File "' . htmlspecialchars($del)
         . '" could not be deleted....</div>';
    }
  }
}

// ============================================================================
// === JavaScript =============================================================
// ============================================================================

?>
<script type="text/javascript">
  function endsWithC(str, c) {
    return str[str.length - 1] == c;
  }

  function confirmDelete(file, isdir) {
    return confirm((isdir
        ? (file + " is a directory containing at least one file."
            + " Are you sure you want to delete it and all its contents?")
        : "Are you sure you want to delete the file, " + file + "?")
        + " This cannot be undone.");
  }

  function doMove(file) {
    nname = prompt("Please enter the new name of this file.", file);
    nname = nname? nname.trim() : "";
    if (nname == "") {
      return;
    }
    
    var warnings = "";
    if (endsWithC(file, '/') && !endsWithC(nname, '/')) {
      warnings += " The new name is not a directory!";
    }
    
    if (confirm("Rename \"" + file + "\" to \"" + nname + "\"?" + warnings)) {
      window.location.href = "?rename&oldname=" + file + "&newname=" + nname;
    }
  }
</script>
<?php

// ============================================================================
// === Breadcrumb =============================================================
// ============================================================================

echo '<span class="filecrumbspan" style="margin-left: 0; margin-right: 0;">'
   . '<a href="' . pageFor("") . '">'
   . '/</a></span>';

if ($prefix != '') {
  $lastslash = 0;
  while (($slash = strpos($prefix, '/', $lastslash)) !== FALSE) {
    echo '<span class="filecrumbspan">'
       . '<a href="' . pageFor(substr($prefix, 0, $slash)) . '">'
       . substr($prefix, $lastslash, $slash - $lastslash)
       . '</a></span>/';
    $lastslash = $slash + 1;
  }
  echo "<br />\n";
}

// ============================================================================
// === File list ==============================================================
// ============================================================================

$limit = isset($_GET['limit'])? intval($_GET['limit']) : 10;
$limits = Array("limit" => $limit + 1);
$limitBelow = $limitAbove = FALSE;
if (isset($_GET['after'])) {
  $limits['lower'] = $limitBelow = $_GET['after'];
}
if (isset($_GET['before'])) {
  $limits['before'] = $limitAbove = $_GET['before'];
}
$files = getObjectsByPath($s,$c,$prefix,$authHdr,$limits);
echo "<form method=\"post\">\n";
echo "  <table class=\"filetable\"><tbody>\n";

echo '    <tr><th><input type="checkbox" id="file-checkall" /></th>';
echo '<th>Path/filename</th><th></th><th></th></tr>';

$nextpage = FALSE;
$count = 0;
$lastItem = FALSE;
$firstItem = FALSE;
foreach ($files as $key => $file) {
  $isDir = isDirectory($file);
  if ($isDir) {
    $name = htmlspecialchars($file->subdir);
    $href = pageFor($name);
  } else {
    $name = $file->name;
    $href = $download_root . $file->name;
  }

  if ($firstItem === FALSE) {
    $firstItem = $name;
  }

  if (++$count > $limit) {
    $nextpage = TRUE;
    break;
  }

  $lastItem = $name;
  echo '    <tr><td class="filecheckbox">'
     . '<input type="checkbox" name="sel-' . $key . '" /></td>';
  echo '<td class="filenamecell"><a href="' . htmlspecialchars($href) . '">'
     . htmlspecialchars($name)
     . '</a></td>';
  echo '<td class="fileactioncell filemovecell"><a href="' . moveLink($file)
     . '" onclick="' . moveOnclick($file) . '">✎</td>';
  echo '<td class="fileactioncell filedelcell"><a href="' . delLink($file)
     . '" onclick="' . delOnclick($file) . '">✗</td>';
  echo "</tr>\n";
}

$lp = strrpos($prefix, '/', -2);
if ($lp === FALSE && $prefix != "") {
  $lp = 0;
}
if ($lp !== FALSE) {
  echo '    <tr><td class="filecheckbox">&nbsp;</td><td class="filenamecell">'
     . '<a href="' . pageFor(substr($prefix, 0, $lp)) . '">../</a>'
     . "</td><td></td><td></td></tr>\n";
}

echo '  </tbody></table>';
echo '</form>';

// ============================================================================
// === Page controls ==========================================================
// ============================================================================

if ($limitBelow !== FALSE || ($nextpage && $limitAbove !== FALSE)) {
  echo '<div class="filepagebutton">'
     . '<a href="' . pageFor($prefix) .'">◂◂</a>'
     . '</div>';
} else {
  echo '<div class="filepagebuttondead">◂◂</div>';
}

if ($limitBelow !== FALSE || ($nextpage && $limitAbove !== FALSE)) {
  echo '<div class="filepagebutton">'
     . '<a href="' . pageForListBefore($prefix, $firstItem) .'">◀</a>'
     . '</div>';
} else {
  echo '<div class="filepagebuttondead">◀</div>';
}

if ($nextpage && $lastItem !== FALSE) {
  echo '<div class="filepagebutton">'
     . '<a href="' . pageForListAfter($prefix, $lastItem) .'">▶</a>'
     . '</div>';
} else {
  echo '<div class="filepagebuttondead">▶</div>';
}

// ============================================================================
// === Upload form ============================================================
// ============================================================================

?>
<div class="fileuploadbox">
  <div class="fileuploadcaption">Upload File</div>
  <div class="fileuploadform">
    <form method="post" enctype="multipart/form-data">
      <div style="float:left">
        <input type="hidden" name="upload" />
        <input type="file" name="uploadfile" /><br />
        <b>Destination</b>: <input type="text" name="uploaddest" value="<?php
           echo htmlspecialchars($prefix);
        ?>" />
      </div>
      <input type="submit" value="Upload" style="float: right; height: 48px;" />
    </form>
  </div>
</div>
<?php

echo '<br/><br/><br/>';
echo "<pre class=\"rawoutputwindow\">"; print_r($files); echo "</pre>";

?>
