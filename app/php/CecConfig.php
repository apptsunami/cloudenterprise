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
/* CecConfig.php */
$rDir = '';
/**
 * It contains global configuration parameters used by Cloud Enterprise Computing Framework.
 * The application should not directly reference these constants because they
 * may change.
 *
 */
class CecConfig {

  const DEBUG_LEVEL = 0;

  const PARAM_PREFIX = 'cec_'; // url parameter prefix used by the framework
  const PARAM_UID = 'cec_uid';
  const PARAM_PLEASEWAITDIV = 'cec_pleaseWaitDiv';
  const PARAM_CMDLEVEL1 = 'cec_cmdL1';
  const PARAM_CMDLEVEL2 = 'cec_cmdL2';
  const PARAM_CMDLEVEL3 = 'cec_cmdL3';
  const PARAM_CMDLEVEL4 = 'cec_cmdL4';
  const PARAM_CALLBACK = 'cec_callback';
  const PARAM_POST_INSTALL = 'cec_postInstall';
  const PARAM_POST_UNINSTALL = 'cec_postUninstall';
  const PARAM_MODAL_DIALOG_CALLBACK = 'cec_modalDialogCallback';
  const PARAM_EVENT_CLASS = 'cec_eventClass';
  const PARAM_ACTION_CLASS = 'cec_actionClass';
  const PARAM_PUBLISH = 'cec_publish';
  const PARAM_PUBLISH_SELF = 'cec_publishSelf';

  const DIV_SHOW_PROGRESS = 'showProgressDiv';
  const URL_ROOT = '/cec/php';
  const IMAGES_DIRECTORY = '/cec/images';
  const INPROGRESS_GIF = 'inProgress.gif';

} // CecConfig
?>
