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
/* CecSforceCustomObject.php */
require_once("sforce/soapclient/SforceMetaObject.php");

/**
 *  Supports Sforce CustomObject
 */

class CecSforceCustomObject extends SforceCustomObject {

  /* these const can also be found in SforceFieldTypes.php */
  const SHARING_MODEL_PRIVATE = 'Private';
  const SHARING_MODEL_READ = 'Read';
  const SHARING_MODEL_READ_WRITE = 'ReadWrite';
/*
  const SHARING_MODEL_READ_WRITE_TRANSFER = 'ReadWriteTransfer';
  const SHARING_MODEL_FULL_ACCESS = 'FullAccess';
  const SHARING_MODEL_CONTROLLED_BY_PARENT = 'ControlledByParent';
*/
  const DEPLOYMENT_STATUS_INDEVELOPMENT = 'InDevelopment';
  const DEPLOYMENT_STATUS_DEPLOYED = 'Deployed';

} // CecSforceCustomObject
?>
