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
/* CecPortletHtml.php */
$rDir = '';
require_once($rDir.'cec/php/helpers/CecHelperHtml.php');
require_once($rDir.'cec/php/helpers/CecHelperJs.php');
require_once($rDir.'cec/php/portlets/CecPortlet.php');
require_once($rDir.'cec/php/utils/CecUtil.php');

class CecPortletHtml extends CecPortlet {

  const FORM_BEHAVIOR_DEFAULT = 0;
  const FORM_BEHAVIOR_MODAL = 1;
  const FORM_BEHAVIOR_WIZARD = 2;
  const FORM_BEHAVIOR_FORM_ACTION = 3;

  const FORM_METHOD_POST = 'post';
  const FORM_METHOD_GET = 'get';

  const INPUT_TYPE_SINGLE_LINE = 1;
  const INPUT_TYPE_MULTI_LINE = 2;
  const INPUT_TYPE_INTEGER = 3;
  const INPUT_TYPE_SECRET = 4;
  const INPUT_TYPE_FILE = 5;
  const INPUT_TYPE_STRING = 6;

  const DEFAULT_DATE_TIME_COLUMNS = 40;
  const DEFAULT_FILE_PATH_MAXLENGTH = 256;
  const DEFAULT_INTEGER_COLUMNS = 16;
  const DEFAULT_STRING_COLUMNS = 60;
  const DEFAULT_TEXTAREA_COLUMNS = 80;
  const DEFAULT_TEXTAREA_ROWS = 4;

  const SHORT_STRING_COLUMNS = 30;

  const SHOW_SECRET_VALUE = true;
  const SHOW_SECRET_CLEAR = false;
  const ENABLE_AUTOCOMPLETE = false;

  const DEFAULT_CURRENCY_SYMBOL = '$';
  const DEFAULT_BUTTON_LABEL = "submit";

  const URL_CURRENT_PAGE = '#';

  const PARAM_PREVIOUS_URL = 'prevUrl';
  const PARAM_NEXT_URL = 'nextUrl';

  protected $data;

  protected $currentUrl;
  protected $previousUrl;
  protected $nextUrl;
  protected $requestParams;
  protected $personId;
  protected $errorMessage;

  public function __construct($parentUIObject) {
    parent::__construct($parentUIObject);
    if (isset($parentUIObject->currentUrl)) {
      $this->currentUrl = $parentUIObject->currentUrl;
    } // if
    if (isset($parentUIObject->previousUrl)) {
      $this->previousUrl = $parentUIObject->previousUrl;
    } // if
    if (isset($parentUIObject->nextUrl)) {
      $this->nextUrl = $parentUIObject->nextUrl;
    } // if
    if (isset($parentUIObject->requestParams)) {
      $this->requestParams = $parentUIObject->requestParams;
    } // if
    if (isset($parentUIObject->personId)) {
      $this->personId = $parentUIObject->personId;
    } // if
    if (isset($parentUIObject->errorMessage)) {
      $this->errorMessage = $parentUIObject->errorMessage;
    } // if
  } // __construct

  public function getContext() {
    return(null);
  } // getContext

  public function setData($data) {
    $this->data = $data;
  } // setData

  protected function isOutputStatic() {
    return false;
  } // isOutputStatic

  public function setCurrentUrl($currentUrl) {
    $this->currentUrl = $currentUrl;
  } // setCurrentUrl

  protected function getDataMember($fieldName) {
    if (empty($this->data)) return null;
    if (!isset($this->data[$fieldName]))
      return null;
    return $this->data[$fieldName];
  } // getDataMember

  protected function getPreviousUrlParamName() {
    return self::PARAM_PREVIOUS_URL;
  } // getPreviousUrlParamName

  protected function getNextUrlParamName() {
    return self::PARAM_NEXT_URL;
  } // getNextUrlParamName

  protected function appendPreviousUrlParam($url) {
    return(CecUrlParam::appendUrlParam($url,
      $this->getPreviousUrlParamName(), $this->currentUrl));
  } // appendPreviousUrlParam

  protected function appendReturnUrlParam($url, $returnUrl=null) {
    if (is_null($returnUrl)) {
      $returnUrl = $this->currentUrl;
    } // if
    return(CecUrlParam::appendUrlParam($url,
      $this->getNextUrlParamName(), $returnUrl));
  } // appendReturnUrlParam

  protected static function htmlUcwords($str) {
    if (empty($str)) return $str;
    return htmlentities(ucwords($str));
  } // htmlUcwords

  protected function renderHiddenPreviousUrl($url=null) {
    if (is_null($url)) {
      return(self::renderHiddenInput($this->getPreviousUrlParamName(),
        $this->currentUrl));
    } else {
      return(self::renderHiddenInput($this->getPreviousUrlParamName(), $url));
    } // else
  } // renderHiddenPreviousUrl

  protected function renderHiddenNextUrl($returnUrl) {
    return(self::renderHiddenInput($this->getNextUrlParamName(),
      $returnUrl));
  } // renderHiddenNextUrl

  static public function renderHiddenInput($name, $value, $id=null) {
    $str = '<input type="hidden" name="'.$name
      .'" value="'.htmlentities($value).'"';
    if (!empty($id)) {
      $str .= ' id="'.$id.'"';
    } // if
    $str .= '>';
    return($str);
  } // renderHiddenInput

  static public function renderFormSubmitButton($label=null, $img=null,
      $width=null, $height=null, $name=null,
      $additionalAttributeArray=null) {
    $str = '<input ';
    if (!is_null($name) && ($name !== false)) {
      $str .= 'name="'.$name.'" ';
    } // if
    if (is_null($img)) {
      $str .= 'type="submit" ';
    } else {
      $str .= 'type="image" src="'.$img.'" style="border:0pt none;" ';
      if (!is_null($width)) {
        $str .= 'width="'.$width.'" ';
      } // if
      if (!is_null($height)) {
        $str .= 'height="'.$height.'" ';
      } // if
    } // else
    $str .= 'value="';
    if (is_null($label)) {
      $str .= self::DEFAULT_BUTTON_LABEL;
    } else {
      $str .= $label;
    } // else
    $str .= '"';
    if (!empty($additionalAttributeArray)) {
      $str .= CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    return($str);
  } // renderFormSubmitButton

  static public function renderButtonWithJs($label, $onClickJs=null,
      $additionalAttributeArray=null) {
    $str = '<input type="button" value="'.$label.'" onClick="'.$onClickJs.'"';
    if (!empty($additionalAttributeArray)) {
      $str .= CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    return($str);
  } // renderButtonWithJs

  static public function renderHrefButton($label, $url) {
    $js = "window.location='".$url."'";
    return(self::renderButtonWithJs($label, $js));
  } // renderHrefButton

  static public function renderInput($name, $value, $size=null, $maxLength=null,
      $additionalAttributeArray=null) {
    $str = '<input type="text" name="'.$name.'"';
    if (!is_null($value)) {
      $str .= ' value="'.htmlentities($value).'"';
    } // if
    if (!is_null($size)) {
      $str .= ' size="'.$size.'"';
    } // if
    if (!is_null($maxLength)) {
      $str .= ' maxlength="'.$maxLength.'"';
    } // if
    if (!empty($additionalAttributeArray)) {
      $str .= CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    return($str);
  } // renderInput

  static public function renderInputPassword($name, $value, $size=null,
      $maxLength=null, $additionalAttributeArray=null) {
    $str = '<input type="password" name="'.$name.'"';
    if (!self::ENABLE_AUTOCOMPLETE) {
      $str .= ' autocomplete=off ';
    } // if
    if (!is_null($value)) {
      $str .= ' value="'.htmlentities($value).'"';
    } // if
    if (!is_null($size)) {
      $str .= ' size="'.$size.'"';
    } // if
    if (!is_null($maxLength)) {
      $str .= ' maxlength="'.$maxLength.'"';
    } // if
    if (!empty($additionalAttributeArray)) {
      $str .= CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    return($str);
  } // renderInputPassword

  static protected function renderInputSecret($name, $value, $size=null, $maxLength=null) {
    if (!self::SHOW_SECRET_VALUE) {
      $value = null;
    } // if
    if (self::SHOW_SECRET_CLEAR) {
      return(self::renderInput($name, $value, $size, $maxLength));
    } else {
      return(self::renderInputPassword($name, $value, $size, $maxLength));
    } // else
  } // renderInputSecret

  static public function renderInputFile($name,
      $additionalAttributeArray=null) {
    $str = '<input type="file" name="'.$name.'"';
    if (!empty($additionalAttributeArray)) {
      $str .= CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    return ($str);
  } // renderInputFile

  static public function renderTextArea($name, $rows, $columns, $value,
      $class=null, $additionalAttributeArray=null) {
    $str = '<textarea';
    if (!is_null($class)) {
      $str .= ' class="'.$class.'"';
    } // if
    $str .= ' name="'.$name.'"';
    if (!is_null($rows)) {
      $str .= ' rows="'.$rows.'"';
    } // if
    if (!is_null($columns)) {
      $str .= ' cols="'.$columns.'"';
    } // if
    if (!empty($additionalAttributeArray)) {
      $str .= CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    if (!is_null($value)) {
      if (is_array($value)) {
        $value = implode("\n", $value);
      } // if
      $str .= htmlentities($value);
    } // if
    $str .= '</textarea>';
    return($str);
  } // renderTextArea

  static protected function renderInputOfType($fieldType, $fieldName, $value=null,
      $width=null, $rows=null) {
    switch($fieldType) {
    case self::INPUT_TYPE_SINGLE_LINE:
      if (is_null($width)) $width = self::DEFAULT_TEXTAREA_COLUMNS;
      return(self::renderInput($fieldName, $value, $width));
    case self::INPUT_TYPE_MULTI_LINE:
      if (is_null($width)) $width = self::DEFAULT_TEXTAREA_COLUMNS;
      if (is_null($rows)) $rows = self::DEFAULT_TEXTAREA_ROWS;
      return(self::renderTextArea($fieldName, $rows, $width, $value));
    case self::INPUT_TYPE_INTEGER:
      if (is_null($width)) $width = self::DEFAULT_INTEGER_COLUMNS;
      return(self::renderInput($fieldName, $value, $width));
    case self::INPUT_TYPE_SECRET:
      if (is_null($width)) $width = self::DEFAULT_TEXTAREA_COLUMNS;
      return(self::renderInputSecret($fieldName, $value, $width));
    case self::INPUT_TYPE_FILE:
      return(self::renderInputFile($fieldName));
    case self::INPUT_TYPE_STRING:
      return(htmlentities($value));
    default:
      return(null);
    } // switch
  } // renderInputOfType

  static public function renderRadio($name, $value, $checked, $onclick, $label,
      $disabled=false, $additionalAttributeArray=null) {
    $str = '<input type="radio" name="'.$name
      .'" value="'.$value.'"';
    if (!is_null($onclick)) {
      $str .= ' onclick="'.$onclick.'"';
    } // if
    if ($checked) {
      $str .= " checked";
    } // if
    if ($disabled) {
      $str .= " disabled";
    } // if
    if (!empty($additionalAttributeArray)) {
      $str .= ' '.CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>'.htmlentities($label);
    return($str);
  } // renderRadio

  static public function renderCheckBox($name, $value, $checked, $onclick,
      $label, $id=null, $class=null) {
    $str = '<input type="checkbox" name="'.$name
      .'" value="'.$value.'"';
    if (!empty($onclick)) {
      $str .= ' onclick="'.$onclick.'"';
    } // if
    if (!empty($id)) {
      $str .= ' id="'.$id.'"';
    } // if
    if ($checked) {
      $str .= " checked";
    } // if
    if (!is_null($class)) {
      $str .= ' class="'.$class.'"';
    } // if
    $str .= '>';
    if (!is_null($label)) {
      $str .= htmlentities($label);
    } // if
    return($str);
  } // renderCheckBox

  static public function renderSelectOption($value, $label, $selected) {
    $str = '<option value="'.$value.'"';
    if ($selected) {
        $str .= ' selected';
    } // if
    $str .= '>'.htmlentities($label).'</option>';
    return($str);
  } // renderSelectOption

  static public function renderSelectList($optionArray, $selectedValue=null,
      $controlName=null, $className=null, $onChangeJS=null,
      $additionalAttributeArray=null) {
    $str = '<select';
    if (!empty($controlName)) {
      $str .= ' name="'.$controlName.'"';
    } // if
    if (!empty($className)) {
      $str .= ' class="'.$className.'"';
    } // if
    if (!empty($onChangeJS)) {
      $str .= ' onChange="'.$onChangeJS.'"';
    } // if
    if (!empty($additionalAttributeArray)) {
      $str .= " ".CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    foreach($optionArray as $label => $value) {
      if (!is_null($selectedValue) && ($value == $selectedValue)) {
        $selected = true;
      } else {
        $selected = false;
      } // else
      $str .= self::renderSelectOption($value, $label, $selected);
    } // foreach
    $str .= '</select>';
    return($str);
  } // renderSelectList

  static public function renderFormTag($action, $attachFile=false,
      $method=self::FORM_METHOD_POST, $id=null, $onSubmit=null, $name=null) {
    $str = '<form method="'.$method.'"';
    if (!empty($id)) {
      $str .= ' id="'.$id.'"';
    } // if
    if (!empty($name)) {
      $str .= ' name="'.$name.'"';
    } // if
    if ($attachFile) {
      $str .= ' enctype="multipart/form-data"';
    } // if
    if (!empty($onSubmit)) {
      $str .= ' onsubmit="'.$onSubmit.'"';
    } // if
    $str .= ' action="'.$action.'">';
    return($str);
  } // renderFormTag

  public function renderFormStart($action,
      $formBehavior=self::FORM_BEHAVIOR_DEFAULT, $attachFile=false,
      $method=self::FORM_METHOD_POST, $id=null, $onSubmit=null, $name=null) {
    /* form tag */
    $str = self::renderFormTag($action, $attachFile, $method, $id, $onSubmit, $name);
    /* hidden input */
    switch($formBehavior) {
    case self::FORM_BEHAVIOR_MODAL:
      $str .= $this->renderHiddenPreviousUrl($this->previousUrl);
      $str .= $this->renderHiddenNextUrl($this->previousUrl);
      break;
    case self::FORM_BEHAVIOR_FORM_ACTION:
      $str .= $this->renderHiddenPreviousUrl($this->previousUrl);
      break;
    case self::FORM_BEHAVIOR_WIZARD:
      if (!empty($this->nextUrl)) {
        $str .= $this->renderHiddenPreviousUrl($this->currentUrl);
        $str .= $this->renderHiddenNextUrl($this->nextUrl);
        break;
      } // if
      /* empty nextUrl means default */
    case self::FORM_BEHAVIOR_DEFAULT:
    default:
      $str .= $this->renderHiddenPreviousUrl($this->currentUrl);
      $str .= $this->renderHiddenNextUrl($this->currentUrl);
    } // switch
    return($str);
  } // renderFormStart

  static public function renderFormEnd() {
    return('</form>');
  } // renderFormEnd

  static public function renderJsGoBack() {
    return("history.back();");
  } // renderJsGoBack

  static public function renderRefreshMetaTag($refreshInterval, $url,
      $anchor=null) {
    $str ='<META HTTP-EQUIV="Refresh" CONTENT="'.$refreshInterval
        .'; URL='.$url;
    if (!is_null($anchor)) {
      $str .= '#'.$anchor;
    } // if
    $str .= '">';
    return($str);
  } // renderRefreshMetaTag

  static private function _renderAnchor($url, $target, $label=null, $img=null,
      $class=null, $additionalAttributeArray=null,
      $imgAdditionalAttributeArray=null, $useSpan=false, $innerHtml=null) {
    $str = '<a href="'.$url.'"';
    if (!is_null($target)) {
      $str .= ' target="'.$target.'"';
    } // if
    if (!is_null($class)) {
      $str .= ' class="'.$class.'"';
    } // if
    if (!empty($additionalAttributeArray)) {
      $str .= CecHelperHtml::injectHtmlTagAttrArray($additionalAttributeArray);
    } // if
    $str .= '>';
    if (!is_null($label)) {
      if ($useSpan) {
        $str .= '<span>';
      } // if
      $str .= htmlentities($label);
      if ($useSpan) {
        $str .= '</span>';
      } // if
    } // if
    if (!is_null($img)) {
      $str .= '<img border="0" src="'.$img.'"';
      if (!empty($imgAdditionalAttributeArray)) {
        $str .= CecHelperHtml::injectHtmlTagAttrArray(
          $imgAdditionalAttributeArray);
      } // if
      $str .='>';
    } // if
    if (!is_null($innerHtml)) {
      $str .= $innerHtml;
    } // if
    $str .= '</a>';
    return($str);
  } // _renderAnchor

  static protected function renderSameWindowUrl($url, $label, $img=null,
      $class=null, $additionalAttributeArray=null,
      $imgAdditionalAttributeArray=null, $useSpan=false, $innerHtml=null) {
    return(self::_renderAnchor($url, null, $label, $img, $class,
      $additionalAttributeArray, $imgAdditionalAttributeArray, $useSpan,
      $innerHtml));
  } // renderSameWindowUrl

  static protected function renderNewWindowUrl($url, $label, $img=null,
      $class=null, $additionalAttributeArray=null,
      $imgAdditionalAttributeArray=null, $useSpan=false, $innerHtml=null) {
    return(self::_renderAnchor($url, '_blank', $label, $img, $class,
      $additionalAttributeArray, $imgAdditionalAttributeArray, $useSpan,
      $innerHtml));
  } // renderNewWindowUrl

  static protected function renderEmailUrl($emailAddress, $label, $img=null,
      $class=null, $additionalAttributeArray=null,
      $imgAdditionalAttributeArray=null, $useSpan=false, $innerHtml=null) {
    $url = 'mailto:'.$emailAddress;
    return(self::_renderAnchor($url, null, $label, $img, $class,
      $additionalAttributeArray, $imgAdditionalAttributeArray,
      $useSpan, $innerHtml));
  } // renderEmailUrl

  static protected function renderToggleDisplayJs($id) {
    return CecHelperJs::jsFunctionToggleElement($id);
  } // renderToggleDisplayJs

  static private function _renderSetDisplayJsStmt($id, $visible) {
    if ($visible) {
      return CecHelperJs::show($id);
    } else {
      return CecHelperJs::hide($id);
    } // else
  } // _renderSetDisplayJsStmt

  static private function _renderShowHideJs($contentControlID, $showControlID,
      $hideControlID, $visible) {
    $str = null;
    if (!is_null($contentControlID)) {
      $str .= self::_renderSetDisplayJsStmt($contentControlID, $visible);
    } // if
    if (!is_null($hideControlID)) {
      $str .= self::_renderSetDisplayJsStmt($hideControlID, $visible);
    } // if
    if (!is_null($showControlID)) {
      $str .= self::_renderSetDisplayJsStmt($showControlID, !$visible);
    } // if
    return($str);
  } // _renderShowHideJs

  static public function renderLink($label, $id, $initiallyVisible, $onclickJs,
      $img=null, $class=null, $additionalAttributeArray=null,
      $imgAdditionalAttributeArray=null, $useSpan=false) {
    $str = '<a href="javascript:void(0)"';
    if (!empty($id)) {
      $str .= ' id="'.$id.'"';
    } // if
    if (!empty($class)) {
      $str .= ' class="'.$class.'"';
    } // if
    if (!empty($additionalAttributeArray)) {
      foreach ($additionalAttributeArray as $key => $value) {
        $str .= ' '.$key.'="'.$value.'"';
      } // foreach
    } // if
    if (!$initiallyVisible) {
      $str .= ' style="display:none"';
    } // if
    if (!empty($onclickJs)) {
      $str .= ' onclick="'.$onclickJs.'"';
    } // if
    $str .= '>';
    if (!empty($label)) {
      if ($useSpan) {
        $str .= '<span>';
      } // if
      $str .= htmlentities($label);
      if ($useSpan) {
        $str .= '</span>';
      } // if
    } // if
    if (!empty($img)) {
      $str .= '<img border="0" src="'.$img.'"';
      if (!empty($imgAdditionalAttributeArray)) {
        foreach ($imgAdditionalAttributeArray as $key => $value) {
          $str .= ' '.$key.'="'.$value.'"';
        } // foreach
      } // if
      $str .= '>';
    } // if
    $str .= '</a>';
    return($str);
  } // renderLink

  static protected function renderShowHideControlPair($contentControlID,
     $showControlID, $showControlLabel, 
     $hideControlID, $hideControlLabel, $initiallyVisible=false) {
    if ($initiallyVisible) {
      $showControlVisibility = false;
      $hideControlVisibility = true;
    } else {
      $showControlVisibility = true;
      $hideControlVisibility = false;
    } // else

    $str = self::renderLink($showControlLabel, $showControlID, $showControlVisibility,
        self::_renderShowHideJs($contentControlID, $showControlID, $hideControlID, true))
      .self::renderLink($hideControlLabel, $hideControlID, $hideControlVisibility,
        self::_renderShowHideJs($contentControlID, $showControlID, $hideControlID, false));
    return($str);
  } // renderShowHideControlPair

  static protected function formatInteger($i) {
    return(CecUtil::formatInteger($i));
  } // formatInteger

  static protected function jsWindowOpen($url, $windowName, $features, $focus=false) {
    $paramArray = Array(
      CecUtil::quoteString($url, true,
        CecUtil::SINGLE_QUOTE),
      CecUtil::quoteString(htmlentities($windowName), true,
        CecUtil::SINGLE_QUOTE),
      CecUtil::quoteString($features, true,
        CecUtil::SINGLE_QUOTE),
    ); // Array
    $str = 'window.open('.implode(',',$paramArray).')';
    if ($focus) {
      $str = $str.'.focus()';
    } // if
    return($str);
  } // jsWindowOpen

  static protected function _renderToggleAllJs($controlName, $divID, $value, $siblingID) {
    $str ="var iList=";
    if (!is_null($divID)) {
      $str .= "document.getElementById('".$divID."').";
    } // if
    $str .= "getElementsByTagName('input');
var iCount=iList.length;for(i=0;i<iCount;i++){";
    if (!is_null($controlName)) {
      $str .= "if (iList[i].name == '".$controlName."'){";
    } // if
    $str .= "iList[i].checked=".$value.";";
    if (!is_null($controlName)) {
      $str .= "}";
    } // if
    $str .= "};
document.getElementById('".$siblingID."').style.display='';
this.style.display='none';";
    return($str);
  } // _renderToggleAllJs

  static protected function renderFieldSet($contents, $legend, 
      $doNotconvertContents=true, $class=null) {
    $str = '<fieldset';
    if (!is_null($class)) {
      $str .= ' class="'.$class.'"';
    } // if
    $str .= '>';
    if (!empty($legend)) {
      $str .= '<legend>'.htmlentities($legend).'</legend>';
    } // if
    if ($doNotconvertContents) {
      $str .= $contents;
    } else {
      $str .= htmlentities($contents);
    } // else
    $str .= '</fieldset>';
    return($str);
  } // renderFieldSet

  protected static function wrapInScriptTag($str) {
    return("\n<script>\n".$str."\n</script>\n");
  } // wrapInScriptTag

  protected static function wrapInStyleTag($str) {
    return("\n<style>\n".$str."\n</style>\n");
  } // wrapInScriptTag

  protected static function renderLabel($forId, $label, $innerHtml=null) {
    $str = '<label';
    if (!is_null($forId)) {
      $str .= ' for="'.$forId.'"';
    } // if
    $str .= '>'.htmlentities($label);
    if (!empty($innerHtml)) {
      $str .= $innerHtml;
    } // if
    $str .= '</label>';
    return $str;
  } // renderLabel

  protected static function formatPrice($price) {
    return self::DEFAULT_CURRENCY_SYMBOL.@number_format($price, 2);
  } // formatPrice

  protected static function generateArrayFieldName($fieldName) {
    return $fieldName.'[]';
  } // generateArrayFieldName

  protected static function generateSubscriptedFieldName($parentObjectName,
      $subscripts) {
    $str = $parentObjectName;
    if (is_array($subscripts)) {
      foreach($subscripts as $s) {
        $str .= '['.$s.']';
      } // foreach
    } else {
      $str .= '['.$subscripts.']';
    } // else
    return $str;
  } // generateSubscriptedFieldName

  protected static function renderJavascriptTag($src) {
    return '<script type="text/javascript" language="javascript" src="'.$src
      .'"></script>';
  } // renderJavascriptTag

  protected static function renderStylesheetLinkTag($href) {
    return '<link rel="stylesheet" type="text/css" href="'.$href.'" />';
  } // renderStylesheetLinkTag

} // CecPortletHtml
