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
.filecrumbspan {
  margin: 6px 8px;
  display: inline-block;
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

function pathfor($p) {
  $fname = '';
  return $fname . '?path=' . $p;
}

// ============================================================================
// === Breadcrumb =============================================================
// ============================================================================

if ($prefix != '') {
  $lastslash = 0;
  while (($slash = strpos($prefix, '/', $lastslash)) !== FALSE) {
    echo '/<span class="filecrumbspan">'
       . '<a href="' . pathfor(substr($prefix, 0, $slash)) . '">'
       . substr($prefix, $lastslash, $slash - $lastslash)
       . '</a></span>';
    $lastslash = $slash + 1;
  }
  echo '<br/>';
}

// ============================================================================
// === File list ==============================================================
// ============================================================================

$files = getObjectsByPath($s,$c,$prefix,$authHdr);
echo "  <table class=\"filetable\"><tbody>\n";

echo '    <tr><th><input type="checkbox" id="file-checkall" /></th>';
echo '<th>Path/filename</th></tr>';

foreach ($files as $key => $file) {
  if (property_exists($file, 'subdir')) {
    $name = htmlspecialchars($file->subdir);
    $href = pathfor($name);
  } else {
    $name = $file->name;
    $href = $download_root . $file->name;
  }
  echo '    <tr><td class="filecheckbox">'
     . '<input type="checkbox" name="sel-' . $key . '" /></td>';
  echo '<td class="filenamecell"><a href="' . htmlspecialchars($href) . '">'
     . htmlspecialchars($name)
     . '</a>';
  echo "</td></tr>\n";
}

$lp = strrpos($prefix, '/', -2);
if ($lp !== FALSE) {
  echo '    <tr><td class="filecheckbox">&nbsp;</td><td>'
     . '<a href="' . pathfor(substr($prefix, 0, $lp)) . '">../</a>'
     . '</td></tr>';
}

echo '  </tbody></table>';

echo '<br/><br/><br/>';
echo "<pre>"; print_r($files); echo "</pre>";

?>
