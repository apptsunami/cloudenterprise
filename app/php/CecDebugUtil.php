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
/*CecDebugUtil.php*/
$rDir = '';
require_once($rDir.'cec/php/CecGlobals.php');

/**
 * It contains some debugging methods.  The application may call these methods.
 * NOTE: The author considers this module 'can be improved' and will enhance it in the future.
 */
class CecDebugUtil {

  const WRAP_WIDTH=60;
  const WRAP_STR = '<br/>';
  /* these match Zend */
  const MESSAGE_LEVEL_DEBUG = 7;
  const MESSAGE_LEVEL_INFO = 6;
  const MESSAGE_LEVEL_WARN = 4;
  const MESSAGE_LEVEL_ERROR = 3;
  const MESSAGE_LEVEL_FATAL = 2;

  const SAPI_CLI = 'cli';

  static function logOutput($str, $level=self::MESSAGE_LEVEL_DEBUG) {
    if (php_sapi_name() == self::SAPI_CLI) {
      CecGlobals::logInfo($str);
      return;
    } // if
    $color = "#000000"; /* black */
    $bgColor = "#ffffff"; /* white */
    switch($level) {
    case self::MESSAGE_LEVEL_DEBUG:
      $bgColor = "#dddddd";  /* gray-ish */
      break;
    case self::MESSAGE_LEVEL_INFO:
      $bgColor = "#00ff00";  /* green */
      break;
    case self::MESSAGE_LEVEL_WARN:
      $bgColor = "#888800";  /* yellowish */
      break;
    case self::MESSAGE_LEVEL_ERROR:
      $bgColor = "#ff8800";  /* orange */
      break;
    case self::MESSAGE_LEVEL_FATAL:
      $bgColor = "#ff0000";  /* red */
      break;
    } // switch
    /* wrap a long string and write to the output stream */
    CecGlobals::logInfo('<span color="'.$color
      .'" style="background-color:'.$bgColor.'">'
      .wordwrap($str, self::WRAP_WIDTH, self::WRAP_STR, true)
      .'</span><br>');
  } // logOutput

  static function formatArrayInString($arr) {
    $str = '[ ';
    if (isset($arr)) {
      if (!is_null($arr) && is_array($arr)) {
        foreach($arr as $key => $value) {
          $str .= ' ['.$key.'=';
          if (is_array($value)) {
            /* recursion */
            $str .= self::formatArrayInString($value);
          } else {
            $str .= $value;
          } // else
          $str .= '] ';
        } // foreach
      } // if
    } // if
    $str .= ' ]';
    return($str);
  } // formatArrayInString

  static function echoPrintArray($label, $arr) {
    $str = self::formatArrayInString($arr);
    self::logOutput($label.' : '.$str);
  } // echoPrintArray

  static function echoPrintObjectVariables($label, $rv) {
    $str = $label.' {';
    $r = get_object_vars($rv);
    $str .= self::formatVarArrayInString($r);
    $str .= '<br>}';
    self::logOutput($str);
  } // echoPrintObjectVariables

  static function echoPrintStdClass($label, $rv) {
    $str = $label.' {';
    $r = $rv;
    if (get_class($rv)=='stdClass') {
      $r = get_object_vars($rv);
    } // if
    $str .= self::formatVarArrayInString($r);
    $str .= '<br>}';
    self::logOutput($str);
  } //

  static function formatVarArrayInString($r) {
    if (is_null($r)) return(null);
    if (!is_array($r)) {
      return(print_r($r));
    } // if
    $str = null;
    foreach($r as $key=>$value) {
      $str .= '<br>["'.$key.'"=>';
      if (get_class($value)=='stdClass') {
        /* recursion */
        $str .= self::echoPrintStdClass(null, $value);
      } else {
        $str .= '"'.$value.'"';
      } // if
      $str .= ']';
    } // foreach
    return($str);
  } // formatVarArrayInString

  static function print_r($obj) {
    self::logOutput(print_r($obj));
  } // print_r

  static function var_dump($obj) {
    self::logOutput(var_dump($obj));
  } // print_r

  static function echoIntrospection($obj) {
    $str = "\nCLASS=".get_class($obj)
      ."\nPARENT CLASS=".get_parent_class($obj);
    $arr = get_class_methods(get_class($obj));
    if (!is_null($arr)) {
      foreach ($arr as $method) {
        $str .="\nFUNCTION ".$method;
      } // foreach
    } // if
    $str .= "\nVARIABLES:\n".self::formatVarArrayInString($obj)."\n";
    self::logOutput($str);
  } // echoIntrospection

} // CecDebugUtil
?>
