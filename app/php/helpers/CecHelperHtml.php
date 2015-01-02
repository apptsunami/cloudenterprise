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
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/views/CecFrameMultiCanvas.php');

class CecHelperHtml {

  const CLASS_SUBMIT_BUTTON = 'inputbutton';
  const CLASS_CANCEL_BUTTON = 'inputbutton inputaux';
  const CLASS_BUTTON = 'inputbutton';

  const HTTP_PREFIX = 'http://';

  static public function tableHeader($width=null, $class=null) {
    return(self::table(0, 0, 0, $width, $class));
  } // tableHeader

  static public function tableHeaderPadded($width=null, $class=null) {
    return(self::table(0, 2, 4, $width, $class));
  } // tableHeader

  static public function table($border, $cellpadding, $cellspacing,
      $width, $class) {
    $str = '<table border="'.$border.'" cellpadding="'.$cellpadding.'" cellspacing="'.$cellspacing.'"';
    if (!is_null($width)) {
       $str .= ' width="'.$width.'" ';
    } // if
    if (!is_null($class)) {
      $str .= ' class="'.$class.'" ';
    } // if
    $str .= '>';
    return($str);
  } // table

  static public function pipe() {
    return('<span class="pipe">|</span>');
  } // pipe

  static public function renderPortletStart($title, $classTable, $classTitle, 
      $href=null, $locale=null, $columnLabelKeys=null) {
    $str = '';
    $columnCount = count($columnLabelKeys);
    $str .= self::tableHeader('100%', $classTable);
    $str .= '<tr><td class="'.$classTitle.'" colSpan="'
      .$columnCount.'" align="center">';
    if (!is_null($href)) {
      $str .= self::tableHeader('100%').'<tr><td nowrap="1">';
    } // if
    $str .= $title;
    if (!is_null($href)) {
      $str .= '</td><td nowrap="1" width="100%" align="right"><div style="float:right">'
        .$href.'</div></td></tr></table>';
    } // if

    $str .= '</td></tr>';

    /* guard */
    if (is_null($columnLabelKeys)) return($str);

    /* column header */
    $str .= '<tr>';
    foreach($columnLabelKeys as $labelKey) {
      $str .= '<td valign="top" align="center" class="'
        .$classTitle.'">'.$locale[$labelKey].'</td>';
    } // foreach
    $str .= '</tr>';
    return($str);
  } // renderPortletStart

  static public function submitButton($value, $id=null, $disabled=FALSE) {
    $str = '<input type="SUBMIT" class="'.self::CLASS_SUBMIT_BUTTON.'" value="'
        .htmlEntities($value).'" ';
    if (!is_null($id)) {
      $str .= ' id="'.$id.'" ';
    } // if
    if ($disabled===TRUE) {
      $str .= ' disabled="true" ';
    } // if
    $str .= ' />';
    return($str);
  } // submitButton

  static public function cancelButton($value) {
    $str = '<input type="button" class="'.self::CLASS_CANCEL_BUTTON.'" value="'
        .htmlEntities($value).'" />';
    return($str);
  } // cancelButton

  static public function button($value) {
    $str = '<input type="button" class="'.self::CLASS_BUTTON.'" value="'
        .htmlEntities($value).'" />';
    return($str);
  } // button

  static public function hiddenDiv($divId, $class=null) {
    $str = '<div style="display:none" id="'.$divId.'"';
    if (!is_null($class)) {
      $str .= ' class="'.$class.'" ';
    } // if
    $str .= '>';
    return($str);
  } // hiddenDiv

  static public function hiddenSpan($spanId) {
    $str = '<span style="display:none" id="'.$spanId.'">';
    return($str);
  } // hiddenSpan

  static public function fullImgPath($imgSrc, $imageDir) {
    if (strtolower(substr($imgSrc, 0, strlen(self::HTTP_PREFIX))) 
        == self::HTTP_PREFIX) {
      return($imgSrc);
    } // if
    return($imageDir.'/'.$imgSrc);
  } // fullImgPath

  static public function injectHtmlTagAttrArray($attrArray) {
    if (is_null($attrArray)) {
      return(null);
    } // if
    $str = null;
    foreach($attrArray as $key => $value) {
      $str .= self::injectHtmlTagAttr($key, $value);
    } // foreach
    return($str);
  } // injectHtmlTagAttrArray

  static public function injectHtmlTagAttr($key, $value) {
    if ($value === TRUE) {
      return($key.'=TRUE ');
    } else if ($value === FALSE) {
      return( $key.'=FALSE ');
    } else {
      return( $key.'="'.$value.'" ');
    }  // else
  } // injectHtmlTagAttr

  static public function colorBox($class, $html, $width=null) {
    $str = '<div class="'.$class.'" ';
    if (!is_null($width)) {
      $str .= 'style="width:'.$width.';"';
    } // if
    $str .= '>'.$html.'</div>';
    return($str);
  } // colorBox

  static public function greyBox($html, $width=null) {
    return(self::colorBox("fbgreybox", $html, $width));
  } // greyBox

  static public function blueBox($html, $width=null) {
    return(self::colorBox("fbbluebox", $html, $width));
  } // blueBox

  static public function infoBox($html, $width=null) {
    return(self::colorBox("fbinfobox", $html, $width));
  } // infoBox

  static public function errorBox($html, $width=null) {
    return(self::colorBox("fberrorbox", $html, $width));
  } // errorBox

} // CecHelperHtml
?>
