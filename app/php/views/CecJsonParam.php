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
/* CecJsonParam.php */
$rDir = '';
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/views/CecWebParam.php');

class CecJsonParam extends CecWebParam {

  public function __construct() {
    parent::__construct();
  } // __construct

  /* methods */
  private function _formatKeyValue($key, $value) {
      return($key.':\''.addslashes($value).'\'');
  } // _formatKeyValue

  protected function renderKeyValuePair($key, $value) {
    if (!is_array($value)) {
      return($this->_formatKeyValue($key, $value));
    } else {
      $str = null;
      foreach($value as $v) {
        $str .= $this->_formatKeyValue($key, $v);
      } // foreach
      return($str);
    } // else
  } // renderKeyValuePair

  public function concatParameter($originalParam, $additionalParam) {
    if (is_null($additionalParam) || ($additionalParam=='')) {
      return($originalParam);
    } // if
    return($originalParam.','.$additionalParam);
  } // concatParameter

} // CecJsonParam
?>
