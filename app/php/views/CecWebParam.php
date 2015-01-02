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
/* CecWebParam.php */
$rDir = '';
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/models/CecSiteWrapper.php');

class CecWebParam {

  protected $str;
  protected $paramList;

  public function __construct() {
    $this->paramList = Array();
    $this->str = null;
  } // __construct

  /* getters */
  public function getKeyValueArray() {
    return($this->paramList);
  } // getKeyValueArray

  public function getKeyValueCount() {
    return(count($this->paramList));
  } // getKeyValueCount

  public function sortKeyValueArray() {
    ksort($this->paramList);
  } // sortKeyValueArray

  public function toString() {
    $str = null;
    foreach($this->paramList as $key => $value) {
      $pStr = $this->renderKeyValuePair($key, $value);
      $str = $this->concatParameter($str, $pStr);
    } // foreach
    return($str);
  } // toString

  /* methods */
  protected function renderKeyValuePair($key, $value) {
    /* to be defined in child class */
    return('');
  } // renderKeyValuePair

  public function concatParameter($returnStr, $str) {
    /* to be defined in child class */
    return(null);
  } // concatParameter

  protected function renderHiddenParameterInString($key, $value, $fbPrefixLen=0,
      $cecPrefixLen=0) {
    if ($fbPrefixLen > 0) {
      /* skip the Facebook parameters */
      if (substr($key, 0, $fbPrefixLen) == CecSiteWrapper::PARAMETER_PREFIX) {
        return(null);
      } // if
    } // if

    if ($cecPrefixLen > 0) {
      /* skip the non-cec parameters */
      if (substr($key, 0, $cecPrefixLen) != CecConfig::PARAM_PREFIX) {
        return(null);
      } // if
    } // if

    if (!is_array($value)) {
      return($this->renderKeyValuePair($key, $value));
    } else {
      $k = $key.'[]';
      $str = null;
      foreach($value as $v) {
        $str = $this->renderKeyValuePair($k, $v);
      } // foreach
      return($str);
    } // else
  } // renderHiddenParameterInString

  public function appendEventClass($eventClassName) {
    $this->_appendKeyValuePair(CecConfig::PARAM_EVENT_CLASS, $eventClassName);
  } // appendEventClass

  public function appendActionClass($actionClassName) {
    $this->_appendKeyValuePair(CecConfig::PARAM_ACTION_CLASS, $actionClassName);
  } // appendActionClass

  public function appendCallback($callbackName) {
    $this->appendKeyValuePair(CecConfig::PARAM_CALLBACK, 1);
    $this->appendActionClass($callbackName);
  } // appendCallback

  static public function appendCanvasTagWebParam(&$array, $canvasTag) {
    $array[CecConfig::PARAM_CMDLEVEL1] = $canvasTag;
  } // appendCanvasTagWebParam

  public function appendCanvasTag($canvasTag) {
    $this->appendKeyValuePair(CecConfig::PARAM_CMDLEVEL1, $canvasTag);
  } // appendCanvasTag

  public function appendPanelTag($canvasTag, $panelTag) {
    $this->appendCanvasTag($canvasTag);
    $this->appendKeyValuePair(CecConfig::PARAM_CMDLEVEL2, $panelTag);
  } // appendPanelTag

  public function appendKeyValuePair($key, $value) {
    $this->_appendKeyValuePair($key, $value);
  } // appendKeyValuePair

  private function _appendKeyValuePair($key, $value, $fbPrefixLen=0,
      $cecPrefixLen=0) {
    if ($fbPrefixLen > 0) {
      /* skip the Site parameters */
      if (substr($key, 0, $fbPrefixLen) == CecSiteWrapper::PARAMETER_PREFIX) {
        return(null);
      } // if
    } // if

    if ($cecPrefixLen > 0) {
      /* skip the non-cec parameters */
      if (substr($key, 0, $cecPrefixLen) != CecConfig::PARAM_PREFIX) {
        return(null);
      } // if
    } // if

    $this->paramList[$key] = $value;
  } // _appendKeyValuePair

  public function appendKeyValueArray($kvArray) {
    if (is_null($kvArray)) return;
    foreach($kvArray as $key => $value) {
      $this->_appendKeyValuePair($key, $value);
    } // foreach
  } // appendKeyValueArray

  public function removeKey($key) {
    if (isset($this->paramList[$key])) {
      unset($this->paramList[$key]);
    } // if
  } // removeKey

  private function appendRequestParameters($req, $fbPrefixLen, $cecPrefixLen) {
    foreach ($req as $key => $value) {
      /* some parameters are always skipped */
      if (($key == CecConfig::PARAM_EVENT_CLASS) ||
          ($key == CecConfig::PARAM_ACTION_CLASS) ||
          ($key == CecConfig::PARAM_POST_INSTALL) ||
          ($key == CecConfig::PARAM_POST_UNINSTALL)) {
        continue;
      } // if
      $this->_appendKeyValuePair($key, $value, $fbPrefixLen, $cecPrefixLen);
    } // foreach
  } // appendRequestParameters

  /**
   *  appendAllCurrentParameters: if $forwardFBParam == 0 then
   *   the site parameters will not be returned.
   */
  public function appendAllCurrentParameters($forwardFBParam=0, 
      $forwardNonCecParam=0) {
    $fbPrefixLen = 0; /* everything goes */
    if ($forwardFBParam==0) {
      $fbPrefixLen = strlen(CecSiteWrapper::PARAMETER_PREFIX);
    } // if
    $cecPrefixLen = 0;
    if ($forwardNonCecParam==0) {
      $cecPrefixLen = strlen(CecConfig::PARAM_PREFIX);
    } // if
    $this->appendRequestParameters(
      CecCoreRequest::getRequestParameterArray(TRUE, FALSE),
      $fbPrefixLen, $cecPrefixLen);
    $this->appendRequestParameters(
      CecCoreRequest::getRequestParameterArray(FALSE, TRUE),
      $fbPrefixLen, $cecPrefixLen);
  } // appendAllCurrentParameters

  public function merge($webParam) {
    if (is_null($webParam)) return;
    $this->appendKeyValueArray($webParam->getKeyValueArray());
  } // merge

  public function appendKeyValueArrayFromUrlString($url) {
    if (empty($url)) return;
    $qPos = strpos($url, '?');
    if ($qPos === FALSE) return;
    $params = substr($url, $qPos+1);
    $a = explode('&', $params);
    $i = 0;
    while ($i < count($a)) {
      $b = split('=', $a[$i]);
      if (isset($b[0])) {
        if (isset($b[1])) {
          $value = urldecode($b[1]);
        } else {
          $value = null;
        } // else
        $this->appendKeyValuePair(urldecode($b[0]), $value);
      } // if
      $i++;
    } // while
  } // appendKeyValueArrayFromUrlString

  public function getValue($key) {
    if (!isset($this->paramList[$key])) return(null);
    return($this->paramList[$key]);
  } // getValue

} // CecWebParam
?>
