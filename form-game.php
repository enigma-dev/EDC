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

  function print_game_form($gameName = "", $gameGenre = "", $gameDesc = "", $isGame = "", $isWIP = "", $gameImage = "", $dlNames = array(), $dlLinks = array(), $screens = array())
  {
    echo '
    <style type="text/css">
      #submittable td { vertical-align: middle; }
      #submittable tr { line-height: 24px; }
      #submittable .info { line-height: normal; font-style: italic; font-size: smaller; }
      #submittable .extinfo { line-height: normal; font-style: italic; font-size: smaller; color: #9AD; }
      #submittable .fgroup { font-size: larger; font-weight: bold; }
      #submittable .sized { width: 200px; }
      #submittable .bigsized { width: 448px; }
      #submittable .radiopt { font-size: small; }
      #submittable .dead { color: #AAA; }
      #submittable .gamecatinput    { width: 96px;  }
      #submittable .gameurlinput    { width: 246px; }
      #submittable .gamefileinput   { width: 178px; }
      #submittable .screenurlinput  { width: 353px; }
      #submittable .screenfileinput { width: 178px; }
      #submittable .thumbulinput {
          border: 1px solid #ABC;
          border-radius: 6px 6px 6px 6px;
          width: 412px;
      }
      #submittable .thumbulbox { text-align: right; padding-right: 3px; }
      #submittable #screenbox { width: 200px; }
    </style>
    <table columns="3" id="submittable">
      <tr>
        <td>Name:</td>
        <td colspan="2"><input type="text" name="name" value="' . htmlspecialchars($game_info['name']) . '" class="sized" placeholder="A name for your game"/></td>
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
      <tr><td>Version</td><td colspan="2">File <span class="info">(Name on server, new URL, or new local file)</span></td></tr>
      ';
      $num = 5;
      $defaults = array("Web", "OS X", "Linux", "Windows", "Source");
      for ($i = 0; $num > 0; ++$i) {
        if (array_key_exists($i, $dlLinks)) {
          $ph = $dlLinks[$i];
          $dl = $dlLinks[$i];
          $up = 'Delete ' . $dlLinks[$i];
        } else {
          $ph = $defaults[--$num];
          $dl = '';
          $up = 'URL to upload, instead of file';
        }
        echo '
      <tr>
        <td><input type="text" id="idln_' . $i . '" name="dlname_' . $i . '" value="' . htmlspecialchars($dlLinks[0]) . '" class="gamecatinput" placeholder="' . $ph . '" /></td>
        <td><input type="text" id="idll_' . $i . '" name="dllink_' . $i . '" value="' . htmlspecialchars($dl) . '" class="gameurlinput" placeholder="' . $up . '" /></td>
        <td><input type="file" id="idlf_' . $i . '" name="dllink_' . $i . '" value="' . htmlspecialchars($dl) . '" class="gamefileinput" /></td>
      </tr>';
      }
      $orig_num_games = $i;
      
      // Allow adding more files with JavaScript
      echo '
      <tr id="addmorefiles_tr" style="display: none">
        <td colspan="3" style="text-align: right;">
          <div id="addfileinputshere" style="display: none"></div>
          <button type="button" onclick="add_file(' . $orig_num_games . ')">Add another file</button>
        </td>
      </tr>';
      
      
      echo '
      <tr><td colspan="3"><span class="fgroup">Screenshots:</span> <span class="info">(Please upload at least one screenshot)</span></td></tr>';
      $num = 5;
      for ($i = 0; $num > 0; ++$i) {
        $url = '';
        if (array_key_exists($i, $screens)) $url = $screens[$i];
        else $num--;
        echo '
      <tr>
        <td colspan="2"><input type="text" id="scru_' . $i . '" name="screenu_' . $i . '" value="' . htmlspecialchars($url) . '" class="screenurlinput" /></td>
        <td colspan="1"><input type="file" id="scrf_' . $i . '" name="screenf_' . $i . '" value="' . htmlspecialchars($url) . '" class="screenfileinput" /></td>
      </tr>';
      }
      
      $orig_num_screens = $i;
      
      // Allow adding more files with JavaScript
      echo '
      <tr id="addmorescreens_tr" style="display: none">
        <td colspan="3" style="text-align: right;">
          <div id="addscreeninputshere" style="display: none"></div>
          <button type="button" onclick="add_screen(' . $orig_num_screens . ')">More screenshots</button>
        </td>
      </tr>';
      
      
      // The thumbnail... dun dun dun
      
      echo '
      <tr>
        <td colspan="3"><span class="fgroup">Thumbnail:</td>
        <!-- td colspan="2"><input type="text" name="thumb" value="' . htmlspecialchars($gameImage) . '" style="width:320px" /></td -->
      </tr>
      <tr>
        <td colspan="3" class="extinfo">
        <b style="font-weight:bold">STOP!</b> The thumbnail is the little image that displays for your game in the recent game list and game search form.
        it should be a good representation of your game, and be sized <tt>154x96</tt>. If you have not prepared such an image, you can create one
        from a screenshot, but keep in mind that this image is NOT itself a screenshot!
       	</td>
      </tr>
      <tr><td rowspan="3">Source</td>
          <td colspan="2" class="radiopt"><label><input type="radio" name="thumbsrc" value="upload" />                     I have a 154x96 image I would like to use! </label>
          <div class="thumbulbox"><input type="file" name="thumburl" class="thumbulinput" /></td></tr>
      <tr><td colspan="2" class="radiopt"><label><input type="radio" name="thumbsrc" value="custom" id="rdes" disabled />  <span id="designradio" class="dead">I would like to design one here <span class="info">(Requires JavaScript)</span></span></label>
          <select id="screenbox" style="width: 200px; margin-left: 24px;"></select><input type="hidden" name="thumb_frameid" id="thumbframe" /></td></tr>
      <tr><td colspan="2" class="radiopt"><label><input type="radio" name="thumbsrc" value="generated" />                  Generate image by resizing my first screenshot</label></td></tr>
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
var num_games = 0;
var num_screens = 0;

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
  if (r.checked) showDesigner();
  $('#rdes').change(showDesigner);
  r = document.getElementById("designradio");
  if (r != null) r.className = "";
  r.innerHTML = "I would like to design one from a screenshot";
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

function add_file(orig_num) {
  function ins(id) {
    return '\
    <tr>\
      <td><input type="text" id="idln_' + id + '" name="dlname_' + id + '" class="gamecatinput"  /></td>\
      <td><input type="text" id="idll_' + id + '" name="dllink_' + id + '" class="gameurlinput"  /></td>\
      <td><input type="file" id="idlf_' + id + '" name="dllink_' + id + '" class="gamefileinput" /></td>\
    </tr>';
  }
  num_games = add_generic('file', num_games, orig_num, 1, ['idln_', 'idll_', 'idlf_'], ins);
}

function add_screen(orig_num) {
  function ins(id) {
    return '\
    <tr>\
      <td colspan="2"><input type="text" id="scru_' + id + '" name="screenu_' + id + '" class="screenurlinput"  /></td>\
      <td colspan="1"><input type="file" id="scrf_' + id + '" name="screenf_' + id + '" class="screenfileinput" /></td>\
    </tr>';
  }
  num_screens = add_generic('screen', num_screens, orig_num, 3, ['scru_', 'scrf_'], ins);
}

var usable_screens = [];
function showDesigner() {
  var a = document.getElementById("designer");
  a.style.display = null;
  find_usable_screens();
  update_combobox();
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
  }
  else
    imagedisplay.src = document.getElementById(sid).value;
}

</script>
