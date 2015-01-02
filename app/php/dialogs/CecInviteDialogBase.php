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
/* views/CecInviteDialogBase.php*/
$rDir = '';

class  CecInviteDialogBase {

  const FORM_NAME_INVITE = 'inviteForm';

  protected $context;
  protected $dataSource;
  protected $urlAfterInvite;
  protected $messageToFriend;
  protected $explanationToUser;
  protected $hiddenParamArray;
  protected $hiddenParamStr;
  protected $action;
  protected $inviteModeStr;
  protected $dialogName;
  protected $dialogTitle;

  public function __construct($ctxt, $urlAfterInvite, $dialogName, 
      $dialogTitle, $action, $inviteMode=1) {
    $this->context = $ctxt;
    $this->dataSource = $ctxt->getDataSource();
    $this->urlAfterInvite = $urlAfterInvite;
    $this->dialogName = $dialogName;
    $this->dialogTitle = $dialogTitle;
    $this->action = $action;
    if ($inviteMode == 1) {
      $this->inviteModeStr = 'true';
    } else {
      $this->inviteModeStr = 'false';
    }
    $this->hiddenParamArray = array();
    $this->hiddenParamStr = null;
  } // __construct

  public function setMessageToFriend($str) {
    $this->messageToFriend = $str;
  } // setMessageToFriend

  public function setExplanationToUser($str) {
    $this->explanationToUser = $str;
  } // setExplanationToUser

  public function addHiddenParameter($key, $value) {
    $this->hiddenParamArray[$key] = $value;
  } // addHiddenParameter

  public function addHiddenParameterString($str) {
    $this->hiddenParamStr .= $str;
  } // addHiddenParameterString

  protected function renderAllHiddenParameters() {
    $str = null;
    foreach($this->hiddenParamArray as $key => $value) {
      $str .= $this->dataSource->renderApplicationHiddenParam($key, $value);
    } // foreach
    return($str);
  } // renderAllHiddenParameters

  public function renderDialogInString() {
    /* to be defined in child class */
    return(null);
  } // renderDialogInString

  public function renderPopUpButtonInString() {
    /* to be defined in child class */
    return(null);
  } // renderPopUpButtonInString

} //  CecInviteDialogBase
?>
