<?php
function reURL($url) {
  $urlbits = parse_url(rawurldecode($url));
  $fullurl = empty($urlbits['scheme']) ? "http://" : rawurlencode($urlbits['scheme']) . '://';
  $fullurl .= empty($urlbits['host']) ? "enigma-dev.org/" : rawurlencode($urlbits['host']);
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
