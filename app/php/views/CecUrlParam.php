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
/* CecUrlParam.php */
$rDir = '';
require_once($rDir.'cec/php/views/CecWebParam.php');

class CecUrlParam extends CecWebParam {

  const KEY_VALUE_SEPARATOR = '=';
  const PARAM_SEPARATOR = '&';

  protected $keyValueSeparator;
  protected $paramSeparator;

  public function __construct() {
    parent::__construct();
    $this->keyValueSeparator = self::KEY_VALUE_SEPARATOR;
    $this->paramSeparator = self::PARAM_SEPARATOR;
  } // __construct

  /* methods */
  private function getKvStr($key, $value) {
    if (!is_array($value)) {
      return($key.$this->keyValueSeparator.urlEncode($value));
    } else {
      $str = null;
      foreach($value as $k => $v) {
        $str .= $this->getKvStr($key.'['.$k.']', $v);
      } // foreach
      return($str);
    } // else
  } // getKvPair

  protected function renderKeyValuePair($key, $value) {
    if (!is_array($value)) {
      return($this->getKvStr($key, $value));
    } else {
      $k = $key.'[]';
      $str = null;
      foreach($value as $v) {
        if (!is_null($str)) $str .= '&';
        $str .= $this->getKvStr($k, $v);
      } // foreach
      return($str);
    } // else
  } // renderKeyValuePair

  public function concatParameter($originalParam, $additionalParam) {
    if (is_null($additionalParam) || ($additionalParam=='')) {
      return($originalParam);
    } // if
    if (!is_null($originalParam) && ($originalParam != '')) {
      return($originalParam.$this->paramSeparator.$additionalParam);
    } else {
      return($additionalParam);
    } // if
  } // concatParameter

  static public function appendUrlParam($url, $paramName, $paramValue) {
    if (strpos($url, '?')) {
      $sep = '&';
    } else {
      $sep = '?';
    } // else
    $paramName = urlEncode($paramName);
    if (is_array($paramValue) && (count($paramValue) > 0)) {
      $str = $url;
      $paramName .= '[]';
      foreach($paramValue as $v) {
        if (is_array($v)) {
          /* treat as multi-line */
          $v = implode("\n", $v);
        } // if
        $str .= $sep.$paramName.'='.urlEncode($v);
        $sep = '&';
      } // foreach
      return($str);
    } else {
      return($url.$sep.$paramName.'='.urlEncode($paramValue));
    } // else
  } // appendUrlParam

} // CecUrlParam
?>
