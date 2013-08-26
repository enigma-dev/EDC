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

function reURL($url) {
  $urlbits = parse_url(rawurldecode($url));
  $fullurl = empty($urlbits['scheme']) ? "http://" : rawurlencode($urlbits['scheme']) . '://';
  $fullurl .= empty($urlbits['host']) ? "/" : rawurlencode($urlbits['host']);
  $fullurl .= empty($urlbits['port']) ? "" : ':' . rawurlencode($urlbits['port']) . '/';
  if (!empty($urlbits['path']))
  {
    $pth = explode('/',$urlbits['path']);
    foreach ($pth as $key => $value)
      $pth[$key] = rawurlencode($value);
    $fullurl .= implode('/', $pth);
  }
  if (!empty($urlbits['query'])) $fullurl .= '?' . rawurlencode($urlbits['query']);
  if (!empty($urlbits['fragment'])) $fullurl .= '#' . rawurlencode($urlbits['fragment']);
  return $fullurl;
}
?>
