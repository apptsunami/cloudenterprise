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
/* CecHelperJson.php */
$rDir = '';

class CecHelperJson {

  private static function _jsonEncode($data) {
    if (is_null($data)) return null;
    if ($data=='') return '';
    /* local implementation for php < v5.2 */
    switch ($type = gettype($data)) {
      case 'NULL':
        return 'null';
      case 'boolean':
        return ($data?'true':'false');
      case 'integer':
      case 'double':
      case 'float':
        return $data;
      case 'string':
        return '"'.addslashes($data).'"';
      case 'object':
        $data = get_object_vars($data);
      case 'array':
        $output_index_count = 0;
        $output_indexed = array();
        $output_associative = array();
        foreach ($data as $key => $value) {
            $output_indexed[] = self::_jsonEncode($value);
            $output_associative[] = self::_jsonEncode($key).':'.self::_jsonEncode($value);
            if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                $output_index_count = NULL;
            } // if
        } // foreach
        if ($output_index_count !== NULL) {
            return '['.implode(',', $output_indexed).']';
        } else {
            return '{'.implode(',', $output_associative).'}';
        } // else
      default:
        return ''; // Not supported
    } // switch
  } // _jsonEncode

  public static function jsonEncode($data) {
    if (function_exists('json_encode')) {
      return json_encode($data);
    } // if
    return self::_jsonEncode($data);
  } // jsonEncode

  private static function _jsonDecode($json) {
    $comment = false;
    $out = '$x=';
    for ($i=0; $i<strlen($json); $i++) {
      if (!$comment) {
        if (($json[$i] == '{') || ($json[$i] == '[')) {
          $out .= ' array(';
        } else if (($json[$i] == '}') || ($json[$i] == ']')) {
          $out .= ')';
        } else if ($json[$i] == ':') {
          $out .= '=>';
        } else {
          $out .= $json[$i];         
        } // else
      } else {
        $out .= $json[$i];
      } // else
      if ($json[$i] == '"' && $json[($i-1)]!="\\") {
        $comment = !$comment;
      } // if
    } // for
    eval($out . ';');
    return $x;
  } // _jsonDecode

  public static function jsonDecode($data) {
/*
    if (function_exists('json_decode')){
      return json_decode($data);
    } // if
*/
    return self::_jsonDecode($data);
  } // jsonDecode

} // CecHelperJson
?>
