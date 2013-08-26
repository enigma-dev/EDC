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

  function print_game_form($gameName = "", $gameGenre = "", $gameDesc = "", $isGame = "", $isWIP = "", $gameImage = "", $dlLinks = "")
  {
    echo '
    <table columns="3">
      <tr>
        <td>Name:</td>
        <td colspan="2"><input type="text" name="name" value="' . htmlspecialchars($game_info['name']) . '"/></td>
      </tr>
      <tr>
        <td>Type:</td><td><input type="radio" name="type" value="game" ' . ($isGame?"checked":"") . '/>Game</td>
        <td><input type="radio" name="type" value="example" ' . ($isGame?"":"checked") . '> Example</td>
      </tr>
      <tr>
        <td>Work in progress:</td>
        <td><input type="checkbox" name="wip" value="true" ' . ($isWIP?"checked":"") . '/> WIP</td>
      </tr>
      <tr>
        <td>Genre:</td>
        <td colspan="2"><input type="text" name="genre" value="' . htmlspecialchars($gameGenre) . '"/></td>
      </tr>
      <tr>
        <td colspan="3">Description:</td>
      </tr>
      <tr>
        <td colspan="3"><textarea name="description" rows="16" style="width:530px">' . htmlspecialchars($gameDesc) . '</textarea></td>
      </tr>
      <tr>
        <td>Thumbnail:</td>
        <td colspan="2"><input type="text" name="thumb" value="' . htmlspecialchars($gameImage) . '" style="width:320px" /></td>
      </tr>
      <tr>
        <td>Download:</td>
        <td colspan="2">
        <input type="text" name="dllink" value="' . htmlspecialchars($dlLinks[0]) . '" style="width:320px" /></td>
      </tr>
      <tr>
        <td style="text-align: right; padding: 4px;" colspan="3"><input type="submit" value="Submit"></td>
      </tr>
    </table>';
  }
?>

