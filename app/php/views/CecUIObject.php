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
/* views/CecUIObject.php */
$rDir = '';
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'cec/php/models/CecSiteWrapper.php');
require_once($rDir.'cec/php/models/CecSqlStatement.php');
require_once($rDir.'cec/php/core/CecCoreWebObject.php');

class CecUIObject extends CecCoreWebObject {

  const CANVAS_WIDTH = CecSiteWrapper::CANVAS_WIDTH;

  protected $parentUIObject;
  protected $context;
  protected $pleaseWaitDiv;
  protected $responseDivId;

  public function __construct($parentUIObject=null) {
    if (CecConfig::DEBUG_LEVEL > 0) {
      CecDebugUtil::logOutput(get_class($this));
    } // if
    $this->parentUIObject = $parentUIObject;
    $this->responseDivId = CecConfig::DIV_SHOW_PROGRESS;
  } // __construct

  /* setters */
  public function setContext($ctxt) {
    $this->context = $ctxt;
  } // setContext

  /* getters */
  public function getContext() {
    if (isset($this->context)) {
      return($this->context);
    } // if
    if (isset($this->parentUIObject)) {
      if (!method_exists($this->parentUIObject, 'getContext')) {
        return(null);
      } // if
      if (!is_null($this->parentUIObject->getContext())) {
        return($this->parentUIObject->getContext());
      } // if
    } // if
    return(null);
  } // getContext

  public function getCurrentLocale() {
    $ctxt = $this->getContext();
    if (is_null($ctxt)) {
      return(null);
    } else {
      return($ctxt->getCurrentLocale());
    } // else
  } // getCurrentLocale

  public function getResponseDivId() {
    return($this->responseDivId);
  } // getResponseDivId

  public function lookUpLocale($key) {
    $locale = $this->getCurrentLocale();
    if (is_null($locale)) {
      return('STRING_'.$key);
    } // if
    return($locale[$key]);
  } // lookUpLocale

  /* convenience methods */
  protected function getDbConnect() {
    $ctxt = $this->getContext();
    if (is_null($ctxt)) return(null);
    return($ctxt->getDbConnect());
  } // getDbConnect

  protected function getDataSource() {
    if (is_null($this->getContext())) return(null);
    return($this->getContext()->getDataSource());
  } // getDataSource

  protected function getApplicationContext() {
    if (is_null($this->getContext())) return(null);
    return($this->getContext()->getApplicationContext());
  } // getApplicationContext

  protected function getEventClassParam($eventClassName) {
    return(Array(CecConfig::PARAM_EVENT_CLASS => $eventClassName));
  } // appendEventClassToParamArray

  protected function getActionClassParam($ActionClassName) {
    return(Array(CecConfig::PARAM_ACTION_CLASS => $ActionClassName));
  } // appendActionClassToParamArray

  protected function renderDatabaseDateValue($dateValue) {
    if (CecSqlStatement::isNullDate($dateValue)) {
      return(null);
    } else {
      return($dateValue);
    } // else
  } // renderDatabaseDateValue

  public function getParentCanvas() {
    $p = $this->parentUIObject;
    while (!is_null($p)) {
      if (is_subclass_of($p, 'CecCanvas')) {
        return($p);
      } // if
      $p = $p->parentUIObject;
    } // while
    return(null);
  } // getParentCanvas

  static public function renderInProgressGif() {
    return('<img src="'.CecApplicationConfig::APP_CALLBACK_URL
      .CecConfig::IMAGES_DIRECTORY.'/'.CecConfig::INPROGRESS_GIF.'">');
  } // renderInProgressGif

  protected function renderPleaseWaitDivInString($visible=0, $renderText=TRUE,
     $id=null, $htmlTag="div") {
    $str = '<'.$htmlTag.' id="';
    if (empty($id)) {
      $str .= $this->responseDivId;
    } else {
      $str .= $id;
    } // else
    $str .= '"';
    if ($visible==0) {
      $str .= ' style="display:none">';
    } else if ($visible==1) {
      $str .= ' style="display">';
    } // else if
    $str .= self::renderInProgressGif();
    if ($renderText) {
      $str .= htmlEntities($this->lookUpLocale(CecLocalization::MSG_PLEASE_WAIT));
    } // if
    $str .= '</'.$htmlTag.'>';
    $this->pleaseWaitDiv = $str;
    return($str);
  } // renderPleaseWaitDivInString

  protected function forwardHiddenPleaseWaitDiv() {
    if (!isset($this->pleaseWaitDiv)) return('');
    $fParam = new CecFormParam();
    $fParam->appendKeyValuePair($this->responseDivId, $this->pleaseWaitDiv);
    return($fParam->toString());
  } // forwardHiddenPleaseWaitDiv

} // CecUIObject
?>
