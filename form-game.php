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

function print_game_row($i, $dn, $dl, $ph) {
   if (empty($ph)) $ph = 'URL to upload, instead of file';
   echo '
      <tr>
        <td rowspan="2">
          <input type="text" id="idln_' . $i . '" name="dlname_' . $i . '" value="' . htmlspecialchars($dn) . '" class="gamecatinput" placeholder="Enter a name" />
        </td><td colspan="2">
          <input type="text" id="idll_' . $i . '" name="dllink_' . $i . '" value="' . htmlspecialchars($dl) . '" class="gameurlinput" placeholder="' . $ph . '" />
        </td>
      </tr><tr>
        <td colspan="2">
          <input type="file" id="idlf_' . $i . '" name="dlfile_' . $i . '" class="gamefileinput" />
        </td>
      </tr>';
}

function print_screenshot_row($i, $url, $ph) {
  if (empty($ph)) $ph = 'URL to upload for screenshot ' . $i . ' (instead of file)';
  echo '
      <tr>
        <td colspan="3"><input type="text" id="scru_' . $i . '" name="screenu_' . $i . '" value="' . htmlspecialchars($url) . '" placeholder="' . $ph . '" class="screenurlinput" /></td>
      </tr><tr>
        <td colspan="3"><input type="file" id="scrf_' . $i . '" name="screenf_' . $i . '" class="screenfileinput" /></td>
      </tr>';
}

  function print_game_form($gameName = "", $gameGenre = "", $gameDesc = "", $isGame = "", $isWIP = "", $gameImage = "", $dlNames = array(), $dlLinks = array(), $screens = array())
  {
    echo '
    <style type="text/css">
      #submittable td { vertical-align: middle; padding: 0 3px; }
      #submittable .info { line-height: normal; font-style: italic; font-size: smaller; vertical-align: text-top; margin-left: 3px; }
      #submittable .extinfo { line-height: normal; font-style: italic; font-size: smaller; color: #9AD; padding: 4px 3px 8px 3px; }
      #submittable .fgroup { font-size: larger; font-weight: bold; margin-top: 8px; margin-bottom: 3px; display: inline-block; }
      #submittable .sized { width: 256px; margin-bottom: 4px; }
      #submittable .bigsized { width: 448px; }
      #submittable .radiopt { font-size: small; }
      #submittable .dead { color: #AAA; }
      #submittable .gamecatinput    { width: 96px;  }
      #submittable .gameurlinput    { width: 100%; box-sizing: border-box; margin-top:    5px; }
      #submittable .gamefileinput   { width: 100%; box-sizing: border-box; margin-bottom: 2px; }
      #submittable .screenurlinput  { width: 100%; box-sizing: border-box; margin-top:    3px; }
      #submittable .screenfileinput { width: 100%; box-sizing: border-box; margin-bottom: 4px; }
      #submittable .thumbulbox {
        width: calc(100% - 32px);
        box-sizing: border-box;
        display: inline-block;
        vertical-align: middle;
        padding-left: 4px;
        margin-bottom: 8px;
      }
      #submittable .thumbulinput {
        border: 1px solid #E6EFF2;
        border-radius: 5px;
        margin-top: 1px;
        width: 100%;
        box-sizing: border-box;
      }
      #submittable #screenbox { width: 200px; }
    </style>
    <table columns="3" id="submittable">
      <tr>
        <td>Name:</td>
        <td colspan="2"><input type="text" name="name" value="' . htmlspecialchars($gameName) . '" class="sized" placeholder="A name for your game"/></td>
      </tr>
      <tr>
        <td>Genre:</td>
        <td colspan="2"><input type="text" name="genre" value="' . htmlspecialchars($gameGenre) . '" class="sized" placeholder="Action, Adventure, Puzzle..."/></td>
      </tr>
      <tr>
        <td>Type:</td>
        <td colspan="2">
          <div style="width:200px">
            <div style="float: left">
              <label><input type="radio" name="type" value="game" ' . ($isGame?"checked":"") . '/> Game</label>
            </div>
            <div style="float: right">
              <label><input type="radio" name="type" value="example" ' . ($isGame?"":"checked") . '> Example</label>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td>Status:</td>
        <td><label><input type="checkbox" name="wip" value="true" ' . ($isWIP?"checked":"") . '/> Work In Progress</label></td>
      </tr>
      <tr>
        <td colspan="3">Description:</td>
      </tr>
      <tr>
        <td colspan="3"><textarea name="description" rows="16" style="width:530px" placeholder="'
        . 'Describe your game. Be as thorough as you like. BBCode is enabled. Good descriptions contain plot/functionality overviews and controls. Screenshots are below, but video URLs go here.' .
        '">' . htmlspecialchars($gameDesc) . '</textarea></td>
      </tr>
      
      <tr><td colspan="3"><span class="fgroup">Download Versions:</span> <span class="info">(You must upload at least one game file)</span></td></tr>
      <tr><td colspan="3" class="extinfo">Enter a URL to upload from, or click "Browse" to choose a file from your computer.<br/>
          Leave the field as a filename to keep the currently uploaded file. Blank lines will be removed!</td></tr>
      <tr><td><b>Version</b></td><td colspan="2"><b>File</b> <span class="info">(Name on server, new URL, or new local file)</span></td></tr>
      ';
      $num = 5;
      $defaults = array("", "OS X", "Linux", "Windows", "Source");
      for ($i = 0; $num > 0; ++$i) {
        if (array_key_exists($i, $dlLinks)) {
          $dn = $dlNames[$i];
          $dl = $dlLinks[$i];
          $up = $dn . ' download will be deleted';
        } else {
          $dn = $defaults[--$num];
          $dl = '';
          $up = '';
        }
        print_game_row($i, $dn, $dl, $up);
      }
      $orig_num_games = $i;
      
      // Allow adding more files with JavaScript
      echo '
      <tr id="addfileinputshere" style="display:none"><td colspan="3"></td></tr>
      <tr id="addmorefiles_tr" style="display: none">
        <td colspan="3" style="text-align: right;">
          <script type="text/javascript">
            let num_files = ' . $orig_num_games . ';
            function add_file() {
              let ins = document.getElementById("addfileinputshere");
              ins.outerHTML = `';
      print_game_row('${num_files}', '', '', '');
      echo '` + ins.outerHTML;
              ++num_files;
            }
          </script>
          <button type="button" onclick="add_file()">Add another file</button>
        </td>
      </tr>';
      
      
      echo '
      <tr><td colspan="3"><span class="fgroup">Screenshots:</span> <span class="info">(Please upload at least one screenshot)</span></td></tr>';
      $num = 5;
      for ($i = 0; $num > 0; ++$i) {
        $url = '';
        $ph = '';
        if (array_key_exists($i, $screens)) {
          $url = $screens[$i];
          $ph = 'Delete screenshot ' . $i;
        } else $num--;
        print_screenshot_row($i, $url, $ph);
      }
      
      $orig_num_screens = $i;
      
      // Allow adding more files with JavaScript
      echo '
      <tr id="addscreeninputshere" style="display:none"><td colspan="3"></td></tr>
      <tr id="addmorescreens_tr" style="display: none">
        <td colspan="3" style="text-align: right;">
          <script type="text/javascript">
            let num_screens = ' . $orig_num_screens . ';
            function add_screen() {
              let ins = document.getElementById("addscreeninputshere");
              ins.outerHTML = `';
      print_screenshot_row('${num_screens}', '', '');
      echo '` + ins.outerHTML;
              ++num_screens;
            }
            function add_screens(x) {
              for (let i = 0; i < x; ++i) add_screen();
            }
          </script>
          <button type="button" onclick="add_screens(3)">More screenshots</button>
        </td>
      </tr>';
      
      
      // The thumbnail... dun dun dun
      
      echo '
      <tr>
        <td colspan="3"><span class="fgroup">Thumbnail:</td>
      </tr>
      <tr>
        <td colspan="3" class="extinfo">
        <b style="font-weight:bold">STOP!</b> The thumbnail is the little image that displays for your game in the recent game list and game search form.
        it should be a good representation of your game, and be sized <tt>154x96</tt>. If you have not prepared such an image, you can create one
        from a screenshot, but keep in mind that this image is NOT itself a screenshot!
       	</td>
      </tr>
      <tr><td rowspan="3" style="text-align: center">Source</td>
          <td colspan="2" class="radiopt"><label><input type="radio" name="thumbsrc" value="upload" />
          <div class="thumbulbox">I have a 154x96 image I would like to use!
          <input type="file" name="thumbfile" class="thumbulinput" /></div></label></td></tr>
      <tr><td colspan="2" class="radiopt"><label><input type="radio" name="thumbsrc" value="custom" id="rdes" disabled />
          <div class="thumbulbox">
          <span id="designradio" class="dead">I would like to design one here <span class="info">(Requires JavaScript)</span></span>
          <select id="screenbox" name="thumbsrc_screen" style="width: 100%; box-sizing: border-box;"></select>
          <input type="hidden" name="thumb_frameid" id="thumbframe" value="1" /></div></label></td></tr>
      <tr><td colspan="2" class="radiopt"><label><input type="radio" name="thumbsrc" value="generated" />
          <div class="thumbulbox">Generate image by resizing my first screenshot<br/>(This will be ugly and you should feel bad for picking it.)</div></label></td></tr>
      ';
      
      echo '
      <tr>
        <td style="text-align: right; padding: 4px;" colspan="3"><input type="submit" value="Submit"></td>
      </tr>
    </table>
    <input type="hidden" value="0"   id="xcrop" name="crop_x" />
    <input type="hidden" value="0"   id="ycrop" name="crop_y" />
    <input type="hidden" value="154" id="wcrop" name="crop_w" />
    <input type="hidden" value="96"  id="hcrop" name="crop_h" />';
    return array(
      'orig_num_games' => $orig_num_games,
      'orig_num_screens' => $orig_num_screens
    );
  }
?>


<script type="text/javascript">
function unblock(numgames, numscreens) {
  num_games = numgames;
  num_screens = numscreens;
  var r;
  r = document.getElementById("addmorefiles_tr");
  if (r != null) r.style.display = null;
  r = document.getElementById("addmorescreens_tr");
  if (r != null) r.style.display = null;
  r = document.getElementById("rdes");
  if (r == null) return 1;
  r.disabled = false;
  showDesigner();
  $("input[name='thumbsrc']").change(showDesigner);
  r = document.getElementById("designradio");
  if (r != null) r.className = "";
  r.innerHTML = "I would like to design one from a screenshot";
  update_combobox();
  return 0;
}

function add_generic(type, cur_num, orig_num, add_num, ids, ins) {
  if (orig_num > cur_num)
    cur_num = orig_num;
  var d = document.getElementById("add" + type + "inputshere");
  if (d == null) return;
  if (d.style.display != null)
    d.style.display = null;
  
  for (i = 0; i < add_num; ++i) {
    var ndiv = document.createElement('div');
    ndiv.innerHTML = ins(cur_num++);
    d.appendChild(ndiv);
  }
  
  return cur_num;
}

var usable_screens = [];
function showDesigner() {
  var r = document.getElementById("rdes");
  var a = document.getElementById("designer");
  a.style.display = r.checked ? null : 'none';
  find_usable_screens();
  update_combobox();
  start_designer();
}

function find_usable_screens() {
  var scrs = [];  
  for (i = 0; i < num_screens; ++i) {
    var tid = 'scrf_' + i;
    var sf = document.getElementById(tid);
    if (sf != null && sf.files.length !== 0) {
      scrs[i] = ["Screenshot " + (i + 1), true, tid];
      continue;
    }
    tid = 'scru_' + i;
    var si = document.getElementById(tid);
    if (si != null && si.value.length > 0)
      scrs[i] = ["Screenshot " + (i + 1), false, tid];
  }
  usable_screens = scrs;
}

function update_combobox() {
  var sel = document.getElementById("screenbox");
  var pselection = -1;
  for (x in sel.options)
    if (sel.options[x].selected)
      pselection = sel.options[x].value;
  while (sel.options.length > 0)
    for (x in sel.options) {
      sel.options.remove(x);
      break;
    }
  find_usable_screens();
  var opt = document.createElement("option");
  opt.text = "Choose a screenshot";
  opt.value = -1;
  sel.options.add(opt);
  for (x in usable_screens) {
    var opt = document.createElement("option");
    opt.value = x;
    opt.text = usable_screens[x][0];
    if (x == pselection)
      opt.selected = true;
    sel.options.add(opt);
  }
  sel.onclick = update_combobox;
  sel.onchange = function() { update_image(sel); };
}

var imagedisplay = null;
var oFReader = new FileReader(), rFilter = 
/^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

oFReader.onload = function (oFREvent) {
  imagedisplay.src = oFREvent.target.result;
  rebind_cropper(imagedisplay.src);
};

function loadImageFile(id_fileinput) {
  var fileinput = document.getElementById(id_fileinput);
  if (fileinput.files.length === 0) { return; }
  var oFile = fileinput.files[0];
  if (!rFilter.test(oFile.type)) { alert("You must select a valid image file!"); return; }
  oFReader.readAsDataURL(oFile);
}

function update_image(sel) {
  var n = sel.options[sel.selectedIndex].value;
  var sid = usable_screens[n][2], isfile = usable_screens[n][1];
  imagedisplay = document.getElementById("photo");
  if (isfile) {
    loadImageFile(sid);
  } else {
    imagedisplay.src = document.getElementById(sid).value;
    if (imagedisplay.src.indexOf('dropbox') >= 0) {
      alert("Dropbox Team has spent a lot of time and effort making sure "  +
            "your browser can't display images from its hosting platform. " +
            "Please choose an image from a proper image hosting platform (" +
            "or from your own computer) in order to use this functionality.");
    }
    rebind_cropper(imagedisplay.src);
  }
}

</script>
