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

function print_designer_form($game_form_result) {
  $orig_num_games = $game_form_result['orig_num_games'];
  $orig_num_screens = $game_form_result['orig_num_screens'];
  echo '
  <div id="designer" class="edcpane" style="display: none">
    <div class="edctitlebar">Thumbnail Editor</div>
    <br />
    ';
    include('imgareaselect.php');
    echo '
    <br/>
    <style type="text/css">
      .frameradio { float: left; }
      .frameradio img { vertical-align: top; }
    </style>
    <script type="text/javascript">function updateselframe(x) { document.getElementById("thumbframe").value = x.value; }</script>
    Select a frame for your screenshot: <span style="width: 50px"></span>
    <br />
    <label><input type="radio" name="frameradio" value="none" onchange="updateselframe(this)" /> None (Do not add a frame to my thumbnail)</label>
    <br/>
    ';
    for ($i = 0; $i < 3; ++$i) echo '
    <div class="frameradio"><label><input type="radio" name="frameradio" value="' . $i . '" ' . ($i == 1? 'selected' : '') . ' onchange="updateselframe(this)" /><img src="images/frames/frame' . $i . '.png" alt="Frame ' . ($i + 1) . '" /></label></div>';
    echo '
  </div>
  <script type="text/javascript">unblock(' . $orig_num_games . ', ' . $orig_num_screens . ');</script>';
}

?>
