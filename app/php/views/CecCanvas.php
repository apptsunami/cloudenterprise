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
/* views/CecCanvas.php */
$rDir = '';
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'cec/php/views/CecUIObject.php');

class CecCanvas extends CecUIObject {

  public function __construct($parentUIObject) {
    parent::__construct($parentUIObject);
  } // __construct

  /* getters */
  public function getIcon() {
  /* to be defined in child */
    return(null);
  } // getIcon

  public function getTag() {
  /* to be defined in child */
    return(null);
  } // getTag

  public function getLabel() {
  /* to be defined in child */
    return('');
  } // getLabel

  /* methods */
  /* to be defined in child */
  public function render() {
    return('');
  } // render

  public function getWebParametersToMe() {
    return($this->getWebParametersToCanvas($this->getTag()));
  } // getWebParametersToMe

  public function getWebParametersToCanvas($canvasTag) {
    $uParam = new CecUrlParam();
    $uParam->appendCanvasTag($canvasTag);
    return($uParam->getKeyValueArray());
  } // getWebParametersToCanvas

  public function forwardToAnotherCanvas() {
    return(null);
  } // forwardToAnotherCanvas

} // CecCanvas
?>
