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
/* CecSforceTableName.php */
require_once('cec/php/models/CecSforceTable.php');

/**
 *  Supports Sforce table 'Name'.
 */
class CecSforceTableName extends CecSforceTable {

  const TABLE_NAME = 'Name';
  const FIELD_ALIAS = 'Alias';
  const FIELD_EMAIL = 'Email';
  const FIELD_FIRST_NAME = 'FirstName';
  const FIELD_IS_ACTIVE = 'IsActive';
  const FIELD_LAST_NAME = 'LastName';
  const FIELD_NAME = 'Name';
  const FIELD_PHONE = 'Phone';
  const FIELD_PROFILE = 'Profile';
  const FIELD_PROFILE_ID = 'ProfileId';
  const FIELD_TITLE = 'Title';
  const FIELD_TYPE = 'Type';
  const FIELD_USER_NAME = 'Username';
  const FIELD_USER_ROLE = 'UserRole';
  const FIELD_USER_ROLE_ID = 'UserRoleId';

  public function __construct($partnerClient, $tableName=self::TABLE_NAME,
      $fieldNameList=null) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    parent::__construct($partnerClient, $tableName, $fieldNameList);
  } // __construct

  public function getDefaultFieldNameList() {
    $fieldNameList = parent::getDefaultFieldNameList();
    $fieldNameList[] = self::FIELD_ALIAS;
    $fieldNameList[] = self::FIELD_EMAIL;
    $fieldNameList[] = self::FIELD_FIRST_NAME;
    $fieldNameList[] = self::FIELD_IS_ACTIVE;
    $fieldNameList[] = self::FIELD_LAST_NAME;
    $fieldNameList[] = self::FIELD_NAME;
    $fieldNameList[] = self::FIELD_PHONE;
    // $fieldNameList[] = self::FIELD_PROFILE;
    $fieldNameList[] = self::FIELD_PROFILE_ID;
    $fieldNameList[] = self::FIELD_TITLE;
    $fieldNameList[] = self::FIELD_TYPE;
    $fieldNameList[] = self::FIELD_USER_NAME;
    // $fieldNameList[] = self::FIELD_USER_ROLE;
    $fieldNameList[] = self::FIELD_USER_ROLE_ID;
    return($fieldNameList);
  } // getDefaultFieldNameList

} // CecSforceTableName
?>
