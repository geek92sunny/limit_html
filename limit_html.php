<?php

/**
 * Crops HTML text ensuring all tags are closed
 * 
 * @param string    HTML string
 * @param int       The length up to which HTML string is to be limited
 */
protected function limitHtml($html, $length)
{
  // Ignoring style tags for displayable string length
  preg_match_all('/<style>(.*?)<\/style>/s', $html, $cssMatches);
  $html = preg_replace('/<style>(.*?)<\/style>/s', '', $html);

  // css
  $css = '';
  if ( isset($cssMatches[1]) ) {
    foreach ( $cssMatches[1] as $cmatch ) {
      $css .= "<style>$cmatch</style>";
    }
  }      

  $truncatedText = substr($html, 0, $length);
  $pos = strpos($truncatedText, ">");
  if($pos !== false)
  {
      $html = substr($html, 0,$length + $pos + 1);
  }
  else
  {
      $html = substr($html, 0,$length);
  }

  // Relace The Broken Opened Tag From The the end of String
  $lastCorruptopnArrow = strrpos($html, "<");
  $lastCloseArrow = strrpos($html, ">");

  if ( $lastCloseArrow < $lastCorruptopnArrow ) {
    $corruptHTmlString = substr($html, $lastCorruptopnArrow, strlen($html) - $lastCorruptopnArrow);
    $html = preg_replace('/'. preg_quote($corruptHTmlString, '/') . '$/', '', $html);
  }

  preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
  
  $openedtags = $result[1];

  preg_match_all('#</([a-z]+)>#iU', $html, $result);
  $closedtags = $result[1];

  $len_opened = count($openedtags);

  if (count($closedtags) == $len_opened)
  {
      return $css . $html;
  }

  $openedtags = array_reverse($openedtags);
  for ($i=0; $i < $len_opened; $i++)
  {
      if (!in_array($openedtags[$i], $closedtags))
      {
          $html .= '</'.$openedtags[$i].'>';
      }
      else
      {
          unset($closedtags[array_search($openedtags[$i], $closedtags)]);
      }
  }

  return $css . $html;
}    