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
/* CecHelperJsCommon.php */
$rDir = '';
class CecHelperJsCommon {

  const KEY_CODE_BACKSPACE = 8;
  const KEY_CODE_TAB = 9;
  const KEY_CODE_RETURN = 13;
  const KEY_CODE_UP_ARROW = 38;
  const KEY_CODE_DOWN_ARROW = 40;

  public static function boolToString($bool) {
    if ($bool){
      return('true');
    } else {
      return('false');
    } // else
  } // boolToString

  public static function getElementById($elementId) {
    return('document.getElementById(\''.$elementId.'\')');
  } // getElementById

  private static function _setStyleDisplay($elementId, $visib,
      $checkExists=false) {
    $str = null;
    if ($checkExists) {
      $str .='var c=';
    } // if
    $str .= self::getElementById($elementId);
    if ($checkExists) {
      $str .=';if(c!=null){c';
    } // if
    $str .= '.style.display=\''.$visib.'\';';
    if ($checkExists) {
      $str .='}';
    } // if
    return($str);
  } // _setStyleDisplay

  public static function show($elementId, $checkExists=false) {
    return self::_setStyleDisplay($elementId, '', $checkExists);
  } // show

  public static function hide($elementId, $checkExists=false) {
    return self::_setStyleDisplay($elementId, 'none', $checkExists);
  } // hide

  public static function setDisplayBlock($elementId) {
    $str = self::getElementById($elementId).'.style.display=\'block\';';
    return($str);
  } // setDisplayBlock

  public static function enable($elementId) {
    $str = self::getElementById($elementId).'.disabled=\'false\';';
    return($str);
  } // enable

  public static function disable($elementId) {
    $str = self::getElementById($elementId).'.disabled=\'true\';';
    return($str);
  } // disable

  public static function submitById($elementId) {
    $str = self::getElementById($elementId).'.submit();';
    return($str);
  } // submitById

  public static function setInnerHtmlById($elementId,$newHtml,
      $textOnly=false) {
    $wrappedHtml = addslashes($newHtml);
    $str = self::getElementById($elementId).'.innerHTML="'.$wrappedHtml.'";';
    return($str);
  } // setInnerHtmlById

  public static function fbSetInnerHTMLFunction() {
    return('function setInner(node, content) {node.innerXHTML=content;}');
  } // fbSetInnerHTMLFunction

  public static function setClassName($elementId, $className,
      $addSlashes=false) {
    $str = self::getElementById($elementId).".className='".$className."';";
    if ($addSlashes) {
      return(addslashes($str));
    } else {
      return($str);
    }
  } // setClassName

  public static function getChecked($elementId) {
    $str = '('.self::getElementById($elementId).".checked)";
    return($str);
  } // getChecked

  public static function setChecked($elementId, $checked) {
    $str = self::getElementById($elementId).".checked="
      .self::boolToString($checked).";";
    return($str);
  } // setChecked

  public static function getValue($elementId) {
    $str = '('.self::getElementById($elementId).".value)";
    return($str);
  } // getValue

  public static function setValueById($elementId, $newValue) {
    $wrappedValue = addslashes($newValue);
    $str = self::getElementById($elementId).".value='".$wrappedValue."';";
    return($str);
  } // setValueById

  public static function jsFunctionToggleElement($elementId) {
    return("var d=document.getElementById('".$elementId
      ."');if(d.style.display=='none')"
      ."{d.style.display='';}else{d.style.display='none';};");
  } // jsFunctionToggleElement

} // CecHelperJsCommon
?>
