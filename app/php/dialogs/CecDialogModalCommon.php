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
/* CecDialogModalCommon.php */
$rDir = '';
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/views/CecFormParam.php');
require_once($rDir.'cec/php/views/CecUIObject.php');

class CecDialogModalCommon extends CecUIObject {
  const DIALOG_BUTTON_VALUE = 'Yes';
  const METHOD_GET = 'GET';
  const METHOD_POST = 'POST';

  const BUTTON_SIZE_BIG = 'big';
  const BUTTON_SIZE_SMALL = 'small';
  const BUTTON_SIZE_SHARE = 'share';

  /* variables */
  protected $dialogName;
  protected $dialogTitle;
  protected $buttonLabel;
  protected $formName;
  protected $formAction;
  protected $requestIdList;
  protected $asyncDialogButtton;
  protected $showDivOnClick;
  protected $buttonSize;
  protected $dialogButtonLabel;
  protected $showFriendList;

  /* constructors */
  public function __construct($parentUIObject, $dialogName, $dialogTitle, 
    $buttonLabel, $formName, $formAction, $buttonSize) {

    parent::__construct($parentUIObject);

    $this->asyncDialogButtton = 1;
    $this->showDivOnClick = null;
    $this->showDivOnComplete = null;
    $this->dialogButtonLabel = self::DIALOG_BUTTON_VALUE;

    $this->dialogName = $dialogName;
    $this->dialogTitle = $dialogTitle;
    $this->buttonLabel = $buttonLabel;
    $this->formName = $formName;
    $this->formAction = $formAction;
    $this->buttonSize = $buttonSize;
    $this->showFriendList = FALSE;
  } // __construct

  /* methods */
  protected function renderFormDetails() {
    /* to be defined in child class */
    return(null);
  } // renderFormDetails

  protected function getFormHeader() {
    /* to be defined in child class */
    return(null);
  } // getFormHeader

  protected function getFormParameters() {
    /* to be defined in child class */
    return(null);
  } // getFormParameters

  protected function getFormContents() {
    /* to be defined in child class */
    return(null);
  } // getFormContents

  protected function renderForm() {
    $formHeader = $this->getFormHeader();
    $formHeader['id'] = $this->formName;

    $formParam = $this->getFormParameters();
    $formContents = $this->getFormContents();

    $fParam = new CecFormParam();
    $fParam->appendAllCurrentParameters();
    $fParam->appendKeyValueArray($formParam);
    if($this->asyncDialogButtton == 1) {
      $fParam->appendKeyValuePair(CecConfig::PARAM_MODAL_DIALOG_CALLBACK, 1);
    } // if

    $str = '<form';
    if (!empty($formHeader)) {
      foreach($formHeader as $key => $value) {
        $str .= ' '.$key.'="'.addslashes($value).'" ';
      } // foreach
    } // if
    $str .='>'
      .$fParam->toString().$formContents.$this->renderFormButtons().'</form>';
    return($str);
  } // renderForm

  /* to be defined in child */
  protected function getSyncActionPrefix() {
    return(null);
  } // getSyncActionPrefix

  /* to be defined in child */
  public function render() {
    return(null);
  } // render

  protected function renderFormButtons() {
    return(null);
  } // renderFormButtons
} // CecDialogModal
?>
