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
/* controllers/CecControllerPublish.php */
$rDir = '';
require_once($rDir.'cec/php/CecGlobals.php');
require_once($rDir.'cec/php/CecContext.php');
require_once($rDir.'cec/php/controllers/CecController.php');
require_once($rDir.'cec/php/views/CecCanvas.php');
require_once($rDir.'php/handlers/PublishHandler.php');

class CecControllerPublish extends CecController {

  protected function executeHandler($mainFrame) {
    /* render form in getInterface mode */
    $handler = new PublishHandler($mainFrame);
    return($handler->execute());
  } // executeHandler

  protected function executePublish() {
    $mainFrame = $this->initMainFrame();
    $mainFrame->setCallbackMode(TRUE);
    /* execute action based on PARAM_ACTION_CLASS */
    /* this happens during getFeedStory */
    $mainFrame->doAction();
    return($this->executeHandler($mainFrame));
  } // executePublish

  public function execute() {
    return($this->executePublish());
  } // execute

} // CecControllerPublish
?>
