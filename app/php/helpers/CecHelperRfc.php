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
/* CecHelperRfc.php */
$rDir = '';

class CecHelperRfc {

  const TAG_STATUS = 'status';
  const TAG_VALUE_SEP = ": ";

  static private function _splitStringIntoArray($str) {
    $strArray = explode("\n",$str);
    $headers = Array();
    $prevStr = null;
    foreach($strArray as $str) {
      $str = str_replace("\r", "", $str);
      if (is_null($str) || ($str == "")) {
        continue;
      } // if
      if (ctype_space($str[0])) {
        /* first char is space: continuation of previous line */
        $prevStr .= $str;
        continue;
      } // if
      if (!is_null($prevStr)) {
        $headers[] = $prevStr;
      } // if
      $prevStr = $str;
    } // foreach
    if (!is_null($prevStr)) {
      $headers[] = $prevStr;
    } // if
    return($headers);
  } // _splitStringIntoArray

  static protected function parseResponseHeader($headers=false,
      $convertKeysToLowerCase=false) {
    if($headers === false){
      return false;
    } // if
    $headerData = Array();
    if (is_null($headers) || ($headers == '')) {
      return($headerData);
    } // if
    $headers = self::_splitStringIntoArray($headers);
    $sepLen = strlen(self::TAG_VALUE_SEP);
    foreach($headers as $value){
      /* we only want to split value once */
      $pos = strpos($value, self::TAG_VALUE_SEP);
      if ($pos === false) {
        /* a line without the separator sequence is a status line */
        $headerData[self::TAG_STATUS] = $value;
      } else {
        $header = Array(
          substr($value, 0, $pos),
          substr($value, $pos+$sepLen),
        ); // Array
        if($header[0] && $header[1]){
          $key = $header[0];
          if ($convertKeysToLowerCase){
            $key = strtolower($key);
          } // if
          if (!isset($headerData[$key])) {
            /* first value of this key: store as singular */
            $headerData[$key] = $header[1];
          } else {
            /* multiple values of this key: store as array */
            $currentValue = $headerData[$key];
            if (!is_array($currentValue)) {
              /* move the singular value into an array */
              $valueArray = Array();
              $valueArray[] = $currentValue;
              $headerData[$key] = $valueArray;
            } // if
            /* add the new value to the array of values */
            $headerData[$key][] = $header[1];
          } // else
        } // if
      } // else
    } // foreach
    return($headerData);
  } // parseResponseHeader

} // CecHelperRfc
?>
