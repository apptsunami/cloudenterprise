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
/* CecSforceTableLead.php */
require_once('cec/php/models/CecSforceTable.php');

/**
 *  Supports Sforce table 'Lead'.
 */
class CecSforceTableLead extends CecSforceTable {

  const TABLE_NAME = 'Lead';
  const FIELD_ACCOUNT_ID = 'AccountId';
  const FIELD_ASSISTANT_NAME = 'AssistantName';
  const FIELD_ASSISTANT_PHONE = 'AssistantPhone';
  const FIELD_BIRTHDATE = 'Birthdate';
  const FIELD_CREATED_BY_ID = 'CreatedById';
  const FIELD_CREATED_DATE = 'CreatedDate';
  const FIELD_DEPARTMENT = 'Department';
  const FIELD_DESCRIPTION = 'Description';
  const FIELD_EMAIL = 'Email';
  const FIELD_FAX = 'Fax';
  const FIELD_FIRST_NAME = 'FirstName';
  const FIELD_HOME_PHONE = 'HomePhone';
  const FIELD_LAST_ACTIVITY_DATE = 'LastActivityDate';
  const FIELD_LAST_CUREQUEST_DATE = 'LastCURequestDate';
  const FIELD_LAST_CUUPDATE_DATE = 'LastCUUpdateDate';
  const FIELD_LAST_MODIFIED_BY_ID = 'LastModifiedById';
  const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
  const FIELD_LAST_NAME = 'LastName';
  const FIELD_LEAD_SOURCE = 'LeadSource';
  const FIELD_MAILING_CITY = 'MailingCity';
  const FIELD_MAILING_COUNTRY = 'MailingCountry';
  const FIELD_MAILING_POSTAL_CODE = 'MailingPostalCode';
  const FIELD_MAILING_STATE = 'MailingState';
  const FIELD_MAILING_STREET = 'MailingStreet';
  const FIELD_MOBILE_PHONE = 'MobilePhone';
  const FIELD_NAME = 'Name';
  const FIELD_OTHER_CITY = 'OtherCity';
  const FIELD_OTHER_COUNTRY = 'OtherCountry';
  const FIELD_OTHER_PHONE = 'OtherPhone';
  const FIELD_OTHER_POSTAL_CODE = 'OtherPostalCode';
  const FIELD_OTHER_STATE = 'OtherState';
  const FIELD_OTHER_STREET = 'OtherStreet';
  const FIELD_OWNER_ID = 'OwnerId';
  const FIELD_PHONE = 'Phone';
  const FIELD_PORTLAND = 'Portland';
  const FIELD_REPORTS_TO_ID = 'ReportsToId';
  const FIELD_SALUTATION = 'Salutation';
  const FIELD_TITLE = 'Title';
  const FIELD_UNITE = 'Unite';

  public function __construct($partnerClient, $tableName=self::TABLE_NAME,
      $fieldNameList=null) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    parent::__construct($partnerClient, $tableName, $fieldNameList);
  } // __construct

  public function getDefaultFieldNameList() {
    $fieldNameList = parent::getDefaultFieldNameList();
    $fieldNameList[] = self::FIELD_ACCOUNT_ID;
    $fieldNameList[] = self::FIELD_ASSISTANT_NAME;
    $fieldNameList[] = self::FIELD_ASSISTANT_PHONE;
    $fieldNameList[] = self::FIELD_BIRTHDATE;
    $fieldNameList[] = self::FIELD_CREATED_BY_ID;
    $fieldNameList[] = self::FIELD_CREATED_DATE;
    $fieldNameList[] = self::FIELD_DEPARTMENT;
    $fieldNameList[] = self::FIELD_DESCRIPTION;
    $fieldNameList[] = self::FIELD_EMAIL;
    $fieldNameList[] = self::FIELD_FAX;
    $fieldNameList[] = self::FIELD_FIRST_NAME;
    $fieldNameList[] = self::FIELD_HOME_PHONE;
    $fieldNameList[] = self::FIELD_LAST_ACTIVITY_DATE;
    $fieldNameList[] = self::FIELD_LAST_CUREQUEST_DATE;
    $fieldNameList[] = self::FIELD_LAST_CUUPDATE_DATE;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_BY_ID;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_DATE;
    $fieldNameList[] = self::FIELD_LAST_NAME;
    $fieldNameList[] = self::FIELD_LEAD_SOURCE;
    $fieldNameList[] = self::FIELD_MAILING_CITY;
    $fieldNameList[] = self::FIELD_MAILING_COUNTRY;
    $fieldNameList[] = self::FIELD_MAILING_POSTAL_CODE;
    $fieldNameList[] = self::FIELD_MAILING_STATE;
    $fieldNameList[] = self::FIELD_MAILING_STREET;
    $fieldNameList[] = self::FIELD_MOBILE_PHONE;
    $fieldNameList[] = self::FIELD_NAME;
    $fieldNameList[] = self::FIELD_OTHER_CITY;
    $fieldNameList[] = self::FIELD_OTHER_COUNTRY;
    $fieldNameList[] = self::FIELD_OTHER_PHONE;
    $fieldNameList[] = self::FIELD_OTHER_POSTAL_CODE;
    $fieldNameList[] = self::FIELD_OTHER_STATE;
    $fieldNameList[] = self::FIELD_OTHER_STREET;
    $fieldNameList[] = self::FIELD_OWNER_ID;
    $fieldNameList[] = self::FIELD_PHONE;
    $fieldNameList[] = self::FIELD_PORTLAND;
    $fieldNameList[] = self::FIELD_REPORTS_TO_ID;
    $fieldNameList[] = self::FIELD_SALUTATION;
    $fieldNameList[] = self::FIELD_SYSTEM_MODSTAMP;
    $fieldNameList[] = self::FIELD_TITLE;
    $fieldNameList[] = self::FIELD_UNITE;
    return($fieldNameList);
  } // getDefaultFieldNameList

} // CecSforceTableLead
?>
