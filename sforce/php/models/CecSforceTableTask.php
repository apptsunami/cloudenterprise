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
/* CecSforceTableTask.php */
require_once('cec/php/models/CecSforceTable.php');

/**
 *  Supports Sforce table 'Task'.
 */
class CecSforceTableTask extends CecSforceTable {

  const TABLE_NAME = 'Task';
  const FIELD_ACCOUNT = 'Account';
  const FIELD_ACCOUNT_ID = 'AccountId';
  const FIELD_ACTIVITY_DATE = 'ActivityDate';
  const FIELD_ATTACHMENTS = 'Attachments';
  const FIELD_CALL_DISPOSITION = 'CallDisposition';
  const FIELD_CALL_DURATION_IN_SECONDS = 'CallDurationInSeconds';
  const FIELD_CALL_OBJECT = 'CallObject';
  const FIELD_CALL_TYPE = 'CallType';
  const FIELD_CREATED_BY = 'CreatedBy';
  const FIELD_CREATED_BY_ID = 'CreatedById';
  const FIELD_CREATED_DATE = 'CreatedDate';
  const FIELD_DESCRIPTION = 'Description';
  const FIELD_IS_ARCHIVED = 'IsArchived';
  const FIELD_IS_CLOSED = 'IsClosed';
  const FIELD_IS_DELETED = 'IsDeleted';
  const FIELD_IS_REMINDER_SET = 'IsReminderSet';
  const FIELD_LAST_MODIFIED_BY = 'LastModifiedBy';
  const FIELD_LAST_MODIFIED_BY_ID = 'LastModifiedById';
  const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
  const FIELD_OWNER = 'Owner';
  const FIELD_OWNER_ID = 'OwnerId';
  const FIELD_PRIORITY = 'Priority';
  const FIELD_REMINDER_DATE_TIME = 'ReminderDateTime';
  const FIELD_STATUS = 'Status';
  const FIELD_SUBJECT = 'Subject';
  const FIELD_WHAT = 'What';
  const FIELD_WHAT_ID = 'WhatId';
  const FIELD_WHO = 'Who';
  const FIELD_WHO_ID = 'WhoId';

  const RELATION_ACCOUNT = 'Account';
  const RELATION_CREATED_BY = 'CreatedBy';
  const RELATION_WHO = 'Who';

  public function __construct($partnerClient, $tableName=self::TABLE_NAME,
      $fieldNameList=null) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    parent::__construct($partnerClient, $tableName, $fieldNameList);
  } // __construct

  public function getDefaultFieldNameList() {
    $fieldNameList = parent::getDefaultFieldNameList();
    $fieldNameList[] = self::FIELD_ACCOUNT;
    $fieldNameList[] = self::FIELD_ACCOUNT_ID;
    $fieldNameList[] = self::FIELD_ACTIVITY_DATE;
    $fieldNameList[] = self::FIELD_ATTACHMENTS;
    $fieldNameList[] = self::FIELD_CALL_DISPOSITION;
    $fieldNameList[] = self::FIELD_CALL_DURATION_IN_SECONDS;
    $fieldNameList[] = self::FIELD_CALL_OBJECT;
    $fieldNameList[] = self::FIELD_CALL_TYPE;
    $fieldNameList[] = self::FIELD_CREATED_BY;
    $fieldNameList[] = self::FIELD_CREATED_BY_ID;
    $fieldNameList[] = self::FIELD_CREATED_DATE;
    $fieldNameList[] = self::FIELD_DESCRIPTION;
    $fieldNameList[] = self::FIELD_IS_ARCHIVED;
    $fieldNameList[] = self::FIELD_IS_CLOSED;
    $fieldNameList[] = self::FIELD_IS_DELETED;
    $fieldNameList[] = self::FIELD_IS_REMINDER_SET;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_BY;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_BY_ID;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_DATE;
    $fieldNameList[] = self::FIELD_OWNER;
    $fieldNameList[] = self::FIELD_OWNER_ID;
    $fieldNameList[] = self::FIELD_PRIORITY;
    $fieldNameList[] = self::FIELD_REMINDER_DATE_TIME;
    $fieldNameList[] = self::FIELD_STATUS;
    $fieldNameList[] = self::FIELD_SUBJECT;
    $fieldNameList[] = self::FIELD_SYSTEM_MODSTAMP;
    $fieldNameList[] = self::FIELD_WHAT;
    $fieldNameList[] = self::FIELD_WHAT_ID;
    $fieldNameList[] = self::FIELD_WHO;
    $fieldNameList[] = self::FIELD_WHO_ID;
    return($fieldNameList);
  } // getDefaultFieldNameList

} // CecSforceTableTask
?>
