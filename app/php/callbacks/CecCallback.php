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
/*callbacks/CecCallback.php*/
$rDir = '';
require_once($rDir.'cec/php/core/CecCoreRequest.php');
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/models/CecSiteWrapper.php');
require_once($rDir.'cec/php/models/CecDbConnect.php');
require_once($rDir.'cec/php/models/CecDataSource.php');
require_once($rDir.'cec/php/core/CecCoreWebObject.php');

class CecCallback extends CecCoreWebObject {
  const SUCCESS_MESSAGE = null;
  const MISSING_PARAMETER_MESSAGE = 'Missing parameter: ';
  const CLASS_ERROR_MESSAGE = 'errorMessage';

  public $debug;
  protected $parameterBlock;
  protected $dbConnect;
  protected $error;
  protected $successMessage;
  private $actionTitle;
  private $closeDatabaseOnExit;

  public function __construct($dbConnect, $actionTitle) {
    $this->debug=0;
    if (is_null($dbConnect)) {
      $this->dbConnect = new CecDbConnect();
      $this->dbConnect->connect();
      $this->closeDatabaseOnExit = 1;
    } else {
      $this->dbConnect = $dbConnect;
      $this->closeDatabaseOnExit = 0;
    } // else
    $this->actionTitle = $actionTitle;
    $this->parameterBlock = array();
    $this->error = null;
  } // __construct

  public function __destruct() {
    if($this->closeDatabaseOnExit==0) return;
    if (!is_null($this->dbConnect)) {
      $this->dbConnect->close();
    } // if
    $this->dbConnect = null;
  } // __destruct

  /* getters */
  protected function getFromParameterBlock($key) {
    if (!isset($this->parameterBlock[$key])) return(null);
    return($this->parameterBlock[$key]);
  } // getFromParameterBlock

  public function getDbConnect() {
    return($this->dbConnect);
  } // getDbConnect

  /* methods */
  protected function isSocialNetworkParameter($key) {
    return(CecSiteWrapper::isSocialNetworkParameter($key));
  } // isSocialNetworkParameter

  protected function checkRequiredParameters($paramNameArray) {
    if (is_null($paramNameArray)) return(null);
    $msg = null;
    foreach($paramNameArray as $paramName) {
      if (!isset($this->parameterBlock[$paramName])) {
        $msg .= self::MISSING_PARAMETER_MESSAGE.$paramName."; ";
      } // if
    } // foreach
    /* append error */
    if (!is_null($msg)) {
      $this->error .= $msg;
    }
    return($msg);
  } // checkRequiredParameters

  protected function processOneParameter($key, $value) {
      /* skip site parameters */
      if ($this->isSocialNetworkParameter($key)==1) {
        return;
      } // if
      $this->parameterBlock[$key] = $value;
  } // processOneParameter

  protected function parseParameters() {
    $paramArray = CecCoreRequest::getRequestParameterArray(FALSE, TRUE);
    foreach ($paramArray as $key => $value) {
      $this->processOneParameter($key, $value);
    } // foreach

    $paramArray = CecCoreRequest::getRequestParameterArray(TRUE, FALSE);
    foreach ($paramArray as $key => $value) {
      $this->processOneParameter($key, $value);
    } // foreach

  } // parseParameters

  protected function print_r() {
    return(CecDebugUtil::formatArrayInString('parameterBlock: '.$this->parameterBlock));
  } // print_r

  protected function getSuccessMessage() {
    /* child class may override this */
    return(self::SUCCESS_MESSAGE);
  } // getSuccessMessage

  public function renderMessage() {
    $str = null;
    if (is_null($this->error)) {
      $str .= $this->getSuccessMessage();
    } else {
      $str .= '<div class="'.self::CLASS_ERROR_MESSAGE.'">'
        .$this->error.'</div>';
    } // else
    if (isset($this->parameterBlock[CecConfig::DIV_SHOW_PROGRESS])) {
      $str .= $this->parameterBlock[CecConfig::DIV_SHOW_PROGRESS];
    } // if

    if (is_null($str) || ($str=='')) {
      return(null);
    } // if
    /* non-empty messages used need to be wrapped in <div> */
    /* return('<div>'.$str.'</div>'); */
    return($str);
  } // renderMessage

  protected function renderPostParameters() {
    $str = '<b>'.$this->actionTitle.'</b>';
    foreach ($_POST as $key => $value) {
      $str .= '<br>'.$key.'='.$value;
    } // foreach
    return($str);
  } // renderPostParameters

  public function render() {
    $str = null;
    /* return something to debug */
    if ($this->debug==1) {
      $str .= '<span>';
    } // if
    $str .= $this->renderMessage();
    if ($this->debug==1) {
      $str .= '<hr>'.$this->actionTitle
        .' input parameters: ['.$this->renderPostParameters()
        .'] Parameter Block:'.
        CecDebugUtil::formatArrayInString($this->parameterBlock)
        .'<hr>';
      $str .= '</span>';
    } // if
    if (isset($this->parameterBlock[CecConfig::PARAM_MODAL_DIALOG_CALLBACK])) {
      return(CecDataSource::renderDialogResponse($this->actionTitle, $str));
    } else {
      return($str);
    }
  } // render

} // CecCallback
?>
