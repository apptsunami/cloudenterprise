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
/* index.php */
/**
 * This is the main entry point of the application.  On Facebook you should register
 * the callback URL as $APP_CALLBACK_URL/cec/php/index.php.
 *
 */
$rDir = '';
/**
 * include_once
 */
require_once($rDir.'cec/php/core/CecCoreRequest.php');
require_once($rDir.'php/cec/CecApplicationConfig.php');
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/controllers/CecController.php');
require_once($rDir.'cec/php/controllers/CecControllerCallback.php');
require_once($rDir.'cec/php/controllers/CecControllerPostInstall.php');
require_once($rDir.'cec/php/controllers/CecControllerPostUninstall.php');
require_once($rDir.'cec/php/controllers/CecControllerPublish.php');
require_once($rDir.'cec/php/controllers/CecControllerPublishSelf.php');
require_once($rDir.'cec/php/CecHtmlHelper.php');
require_once($rDir.'cec/php/CecDebugUtil.php');
/**
 * The main line is invoked immediately.  This is not a class.
 *
 */

  /* main */
  $emulatorMode = FALSE;
  $str = null;
  if (CecCoreRequest::issetRequestParameter(CecConfig::PARAM_CALLBACK)) {
    $controller = new CecControllerCallback();
    $str .= $controller->execute();
  } else if (CecCoreRequest::issetRequestParameter(CecConfig::PARAM_PUBLISH)) {
    $controller = new CecControllerPublish();
    $str .= $controller->execute();
  } else if (CecCoreRequest::issetRequestParameter(CecConfig::PARAM_PUBLISH_SELF)) {
    $controller = new CecControllerPublishSelf();
    $str .= $controller->execute();
  } else {
    $helper = new CecHtmlHelper($emulatorMode,
      CecApplicationConfig::APPLICATION_TITLE);
    $str .= $helper->renderHeader();
    if (CecCoreRequest::issetRequestParameter(CecConfig::PARAM_POST_INSTALL)) {
      $controller = new CecControllerPostInstall();
    } else if (CecCoreRequest::issetRequestParameter(CecConfig::PARAM_POST_UNINSTALL)) {
      $controller = new CecControllerPostUninstall();
    } else {
      $controller = new CecController();
    } // else
    $str .= $controller->execute();
    $str .= $helper->renderFooter();
  } // else
  echo($str);
?>
