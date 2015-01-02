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
/* CecDialogModalSelectOneFriend.php */
$rDir = '';
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/views/CecFormParam.php');
require_once($rDir.'cec/php/dialogs/CecDialogModal.php');
require_once($rDir.'php/cec/CecApplicationConfig.php');

class CecDialogModalSelectOneFriend extends CecDialogModal {

  /* variables */
  protected $formName;
  protected $requestIdList;
  protected $formMethod;
  protected $formAction;
  protected $additionalParamArray;
  protected $dataSource;
  protected $formElementName;
  protected $formElementId;

  /* constructors */
  public function __construct($parentUIObject, $dialogName, $dialogTitle,
    $buttonLabel, $formName, $formMethod, $formAction,
    $formElementName, $formElementId,
    $includeMe, $excludeIdListStr, $includeLists,
    $additionalParamArray, $buttonSize, $dataSource) {

    parent::__construct($parentUIObject, $dialogName, $dialogTitle,
      $buttonLabel, $formName, $formAction, $buttonSize);

    $this->formName = $formName;
    $this->formMethod = $formMethod;
    $this->additionalParamArray = $additionalParamArray;
    $this->dataSource = $dataSource;
    $this->formElementName = $formElementName;
    $this->formElementId = $formElementId;
    $this->includeMe= $includeMe;
    $this->excludeIdListStr= $excludeIdListStr;
    $this->includeLists= $includeLists;
    $this->showFriendList = TRUE;
  } // __construct

  /* methods */
  protected function getFormHeader() {
    $actionPrefix = null;
    if ($this->asyncDialogButtton == 1) {
      $actionPrefix = CecApplicationConfig::APP_CALLBACK_URL.'/';
    } else {
      $actionPrefix = $this->getSyncActionPrefix();
    } // if
    $header = Array(
      'method' => $this->formMethod,
      'action' => $actionPrefix.$this->formAction,
    ); // Array
    return($header);
  } // getFormHeader

  protected function getFormParameters() {
    return($this->additionalParamArray);
  } // getFormParameters

  protected function getFormContents() {
    $str = $this->dataSource->renderFriendSelector(null, $this->formName,
      $this->formElementName, $this->formElementId, $this->includeMe,
      $this->excludeIdListStr, $this->includeLists);
    return($str);
  } // getFormContents

} // CecDialogModalSelectOneFriend
?>
