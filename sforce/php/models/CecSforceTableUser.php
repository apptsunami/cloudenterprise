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
/* CecSforceTableUser.php */
require_once('cec/php/models/CecSforceTable.php');

/**
 *  Supports Sforce table 'User'.
 */
class CecSforceTableUser extends CecSforceTable {

  const TABLE_NAME = 'User';

  const FIELD_USERNAME = 'Username';
  const FIELD_LAST_NAME = 'LastName';
  const FIELD_FIRST_NAME = 'FirstName';
  const FIELD_NAME = 'Name';
  const FIELD_COMPANY_NAME = 'CompanyName';
  const FIELD_DIVISION = 'Division';
  const FIELD_DEPARTMENT = 'Department';
  const FIELD_TITLE = 'Title';
  const FIELD_STREET = 'Street';
  const FIELD_CITY = 'City';
  const FIELD_STATE = 'State';
  const FIELD_POSTAL_CODE = 'PostalCode';
  const FIELD_COUNTRY = 'Country';
  const FIELD_EMAIL = 'Email';
  const FIELD_PHONE = 'Phone';
  const FIELD_FAX = 'Fax';
  const FIELD_MOBILE_PHONE = 'MobilePhone';
  const FIELD_ALIAS = 'Alias';
  const FIELD_IS_ACTIVE = 'IsActive';
  const FIELD_TIME_ZONE_SID_KEY = 'TimeZoneSidKey';
  const FIELD_USER_ROLE_ID = 'UserRoleId';
  const FIELD_LOCALE_SID_KEY = 'LocaleSidKey';
  const FIELD_RECEIVES_INFO_EMAILS = 'ReceivesInfoEmails';
  const FIELD_RECEIVES_ADMIN_INFO_EMAILS = 'ReceivesAdminInfoEmails';
  const FIELD_EMAIL_ENCODING_KEY = 'EmailEncodingKey';
  const FIELD_PROFILE_ID = 'ProfileId';
  const FIELD_USER_TYPE = 'UserType';
  const FIELD_LANGUAGE_LOCALE_KEY = 'LanguageLocaleKey';
  const FIELD_EMPLOYEE_NUMBER = 'EmployeeNumber';
  const FIELD_DELEGATED_APPROVER_ID = 'DelegatedApproverId';
  const FIELD_MANAGER_ID = 'ManagerId';
  const FIELD_LAST_LOGIN_DATE = 'LastLoginDate';
  const FIELD_CREATED_DATE = 'CreatedDate';
  const FIELD_CREATED_BY_ID = 'CreatedById';
  const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
  const FIELD_LAST_MODIFIED_BY_ID = 'LastModifiedById';
  const FIELD_OFFLINE_TRIAL_EXPIRATION_DATE = 'OfflineTrialExpirationDate';
  const FIELD_OFFLINE_PDA_TRIAL_EXPIRATION_DATE = 'OfflinePdaTrialExpirationDate';
  const FIELD_USER_PERMISSIONS_MARKETING_USER = 'UserPermissionsMarketingUser';
  const FIELD_USER_PERMISSIONS_OFFLINE_USER = 'UserPermissionsOfflineUser';
  const FIELD_USER_PERMISSIONS_CALL_CENTER_AUTO_LOGIN = 'UserPermissionsCallCenterAutoLogin';
  const FIELD_USER_PERMISSIONS_MOBILE_USER = 'UserPermissionsMobileUser';
  const FIELD_FORECAST_ENABLED = 'ForecastEnabled';
  const FIELD_USER_PREFERENCES_ACTIVITY_REMINDERS_POPUP = 'UserPreferencesActivityRemindersPopup';
  const FIELD_USER_PREFERENCES_EVENT_REMINDERS_CHECKBOX_DEFAULT = 'UserPreferencesEventRemindersCheckboxDefault';
  const FIELD_USER_PREFERENCES_TASK_REMINDERS_CHECKBOX_DEFAULT = 'UserPreferencesTaskRemindersCheckboxDefault';
  const FIELD_USER_PREFERENCES_REMINDER_SOUND_OFF = 'UserPreferencesReminderSoundOff';
  const FIELD_USER_PREFERENCES_APEX_PAGES_DEVELOPER_MODE = 'UserPreferencesApexPagesDeveloperMode';
  const FIELD_CONTACT_ID = 'ContactId';
  const FIELD_CALL_CENTER_ID = 'CallCenterId';
  const FIELD_EXTENSION = 'Extension';

  public function __construct($partnerClient, $tableName=self::TABLE_NAME,
      $fieldNameList=null) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    parent::__construct($partnerClient, $tableName, $fieldNameList);
  } // __construct

  public function getDefaultFieldNameList() {
    $fieldNameList = parent::getDefaultFieldNameList();
    $fieldNameList[] = self::FIELD_USERNAME;
    $fieldNameList[] = self::FIELD_LAST_NAME;
    $fieldNameList[] = self::FIELD_FIRST_NAME;
    $fieldNameList[] = self::FIELD_NAME;
    $fieldNameList[] = self::FIELD_COMPANY_NAME;
    $fieldNameList[] = self::FIELD_DIVISION;
    $fieldNameList[] = self::FIELD_DEPARTMENT;
    $fieldNameList[] = self::FIELD_TITLE;
    $fieldNameList[] = self::FIELD_STREET;
    $fieldNameList[] = self::FIELD_CITY;
    $fieldNameList[] = self::FIELD_STATE;
    $fieldNameList[] = self::FIELD_POSTAL_CODE;
    $fieldNameList[] = self::FIELD_COUNTRY;
    $fieldNameList[] = self::FIELD_EMAIL;
    $fieldNameList[] = self::FIELD_PHONE;
    $fieldNameList[] = self::FIELD_FAX;
    $fieldNameList[] = self::FIELD_MOBILE_PHONE;
    $fieldNameList[] = self::FIELD_ALIAS;
    $fieldNameList[] = self::FIELD_IS_ACTIVE;
    $fieldNameList[] = self::FIELD_TIME_ZONE_SID_KEY;
    $fieldNameList[] = self::FIELD_USER_ROLE_ID;
    $fieldNameList[] = self::FIELD_LOCALE_SID_KEY;
    $fieldNameList[] = self::FIELD_RECEIVES_INFO_EMAILS;
    $fieldNameList[] = self::FIELD_RECEIVES_ADMIN_INFO_EMAILS;
    $fieldNameList[] = self::FIELD_EMAIL_ENCODING_KEY;
    $fieldNameList[] = self::FIELD_PROFILE_ID;
    $fieldNameList[] = self::FIELD_USER_TYPE;
    $fieldNameList[] = self::FIELD_LANGUAGE_LOCALE_KEY;
    $fieldNameList[] = self::FIELD_EMPLOYEE_NUMBER;
    $fieldNameList[] = self::FIELD_DELEGATED_APPROVER_ID;
    $fieldNameList[] = self::FIELD_MANAGER_ID;
    $fieldNameList[] = self::FIELD_LAST_LOGIN_DATE;
    $fieldNameList[] = self::FIELD_CREATED_DATE;
    $fieldNameList[] = self::FIELD_CREATED_BY_ID;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_DATE;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_BY_ID;
    $fieldNameList[] = self::FIELD_SYSTEM_MODSTAMP;
    $fieldNameList[] = self::FIELD_OFFLINE_TRIAL_EXPIRATION_DATE;
    $fieldNameList[] = self::FIELD_OFFLINE_PDA_TRIAL_EXPIRATION_DATE;
    $fieldNameList[] = self::FIELD_USER_PERMISSIONS_MARKETING_USER;
    $fieldNameList[] = self::FIELD_USER_PERMISSIONS_OFFLINE_USER;
    $fieldNameList[] = self::FIELD_USER_PERMISSIONS_CALL_CENTER_AUTO_LOGIN;
    $fieldNameList[] = self::FIELD_USER_PERMISSIONS_MOBILE_USER;
    $fieldNameList[] = self::FIELD_FORECAST_ENABLED;
    $fieldNameList[] = self::FIELD_USER_PREFERENCES_ACTIVITY_REMINDERS_POPUP;
    $fieldNameList[] = self::FIELD_USER_PREFERENCES_EVENT_REMINDERS_CHECKBOX_DEFAULT;
    $fieldNameList[] = self::FIELD_USER_PREFERENCES_TASK_REMINDERS_CHECKBOX_DEFAULT;
    $fieldNameList[] = self::FIELD_USER_PREFERENCES_REMINDER_SOUND_OFF;
    $fieldNameList[] = self::FIELD_USER_PREFERENCES_APEX_PAGES_DEVELOPER_MODE;
    $fieldNameList[] = self::FIELD_CONTACT_ID;
    $fieldNameList[] = self::FIELD_CALL_CENTER_ID;
    $fieldNameList[] = self::FIELD_EXTENSION;
    return($fieldNameList);
  } // getDefaultFieldNameList

  public function selectByEmailAddress($emailAddress, $fieldNameList=null) {
    if (is_null($emailAddress)) return(null);
    $emailAddress = trim($emailAddress);
    if (is_null($emailAddress)) return(null);
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    $filter = self::FIELD_EMAIL .'='.self::quoteSoqlString($emailAddress);
    return($this->selectSomeAsArray($filter, $fieldNameList));
  } // selectByEmailAddress

} // CecSforceTableUser
?>
