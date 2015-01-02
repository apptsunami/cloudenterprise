<?php
/* Copyright (C) 2008 App Tsunami, Inc. */
/* 
 *  This program is free software: you can redistribute it and/or modify 
 *  it under the terms of the GNU General Public License as published by 
 *  the Free Software Foundation, either version 3 of the License, or 
 *  (at your option) any later version. 
 * 
 *  This program is distributed in the hope that it will be useful, 
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 *  GNU General Public License for more details. 
 * 
 *  You should have received a copy of the GNU General Public License 
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */
$rDir = '';

class CecHelperString {

  const QUOTE = '"';
  const ESC = '\\';
  const SEPARATOR_LIST = ' ,';

  static public function tokenizeString($string, $separatorList=self::SEPARATOR_LIST) {
    if (empty($string)) return(null);
    $tagList = Array();
    $pos = 0;
    $tag = null;
    $esc = FALSE;
    $quote = FALSE;
    while ($pos < strlen($string)) {
      $ch = $string[$pos];
      if ($esc) {
        /* following a esc */
        $tag .= $ch;
        $esc = FALSE;
      } else if ($quote) {
        /* inside a quote */
        if ($ch == self::QUOTE) {
          /* end quote */
          $tagList[] = $tag;
          $tag = null;
          $quote = FALSE;
        } else if ($ch == self::ESC) {
          /* start esc */
          $esc = TRUE;
        } else {
          $tag .= $ch;
        } // else
      } else {
        if ($ch == self::QUOTE) {
          /* start quote */
          $quote = TRUE;
        } else if ($ch == self::ESC) {
          /* start esc */
          $esc = TRUE;
        } else if (strpos($separatorList, $ch) !== FALSE) {
          if (!empty($tag)) {
            $tagList[] = $tag;
            $tag = null;
          } // if
        } else {
          $tag .= $ch;
        } // else
      } // else

      $pos++;
    } // while
    if (!empty($tag)) {
      $tagList[] = $tag;
    } // if
    return($tagList);
  } // tokenizeString

  static public function convertTokenArrayToPreg($tokenArray, $caseInsensitive=FALSE) {
    if (is_null($tokenArray)) return(null);
    $pattern = null;
    foreach($tokenArray as $token) {
      if (!is_null($pattern)) {
        $pattern .= '|'; // PECL
      } // if 
      if ($caseInsensitive) {
        $pattern .= '(?i)'; // PECL
      } // if
      $pattern .= addslashes($token);
    } // foreach
    return('('.$pattern.')'); // PECL
  } // convertTokenArrayToPreg

  static public function tokenizeStringToPreg($string, $caseInsensitive=FALSE) {
    $tokenArray = self::tokenizeString($string);
    return(self::convertTokenArrayToPreg($tokenArray, $caseInsensitive));
  } // tokenizeStringToPreg

} // CecHelperString
?>
