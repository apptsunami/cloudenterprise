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
/* CecSforceTableAccount.php */
require_once('cec/php/models/CecSforceTable.php');

/**
 *  Supports Sforce table 'Account'.
 */
class CecSforceTableAccount extends CecSforceTable {

  const TABLE_NAME = 'Account';
  const FIELD_NAME = 'Name';
  const FIELD_PARENT_ID = 'ParentId';
  const FIELD_BILLING_STREET = 'BillingStreet';
  const FIELD_BILLING_CITY = 'BillingCity';
  const FIELD_BILLING_STATE = 'BillingState';
  const FIELD_BILLING_POSTAL_CODE = 'BillingPostalCode';
  const FIELD_BILLING_COUNTRY = 'BillingCountry';
  const FIELD_SHIPPING_STREET = 'ShippingStreet';
  const FIELD_SHIPPING_CITY = 'ShippingCity';
  const FIELD_SHIPPING_STATE = 'ShippingState';
  const FIELD_SHIPPING_POSTAL_CODE = 'ShippingPostalCode';
  const FIELD_SHIPPING_COUNTRY = 'ShippingCountry';
  const FIELD_PHONE = 'Phone';
  const FIELD_FAX = 'Fax';
  const FIELD_ACCOUNT_NUMBER = 'AccountNumber';
  const FIELD_WEBSITE = 'Website';
  const FIELD_SIC = 'Sic';
  const FIELD_INDUSTRY = 'Industry';
  const FIELD_ANNUAL_REVENUE = 'AnnualRevenue';
  const FIELD_NUMBER_OF_EMPLOYEES = 'NumberOfEmployees';
  const FIELD_OWNERSHIP = 'Ownership';
  const FIELD_TICKER_SYMBOL = 'TickerSymbol';
  const FIELD_DESCRIPTION = 'Description';
  const FIELD_RATING = 'Rating';
  const FIELD_SITE = 'Site';
  const FIELD_OWNER_ID = 'OwnerId';
  const FIELD_CREATED_DATE = 'CreatedDate';
  const FIELD_CREATED_BY_ID = 'CreatedById';
  const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
  const FIELD_LAST_MODIFIED_BY_ID = 'LastModifiedById';
  const FIELD_LAST_ACTIVITY_DATE = 'LastActivityDate';

  const ID_PREFIX = "001";

  public function __construct($partnerClient, $tableName=self::TABLE_NAME,
      $fieldNameList=null) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    parent::__construct($partnerClient, $tableName, $fieldNameList);
  } // __construct

  public function getDefaultFieldNameList() {
    $fieldNameList = parent::getDefaultFieldNameList();
    $fieldNameList[] = self::FIELD_NAME;
    $fieldNameList[] = self::FIELD_PARENT_ID;
    $fieldNameList[] = self::FIELD_BILLING_STREET;
    $fieldNameList[] = self::FIELD_BILLING_CITY;
    $fieldNameList[] = self::FIELD_BILLING_STATE;
    $fieldNameList[] = self::FIELD_BILLING_POSTAL_CODE;
    $fieldNameList[] = self::FIELD_BILLING_COUNTRY;
    $fieldNameList[] = self::FIELD_SHIPPING_STREET;
    $fieldNameList[] = self::FIELD_SHIPPING_CITY;
    $fieldNameList[] = self::FIELD_SHIPPING_STATE;
    $fieldNameList[] = self::FIELD_SHIPPING_POSTAL_CODE;
    $fieldNameList[] = self::FIELD_SHIPPING_COUNTRY;
    $fieldNameList[] = self::FIELD_PHONE;
    $fieldNameList[] = self::FIELD_FAX;
    $fieldNameList[] = self::FIELD_ACCOUNT_NUMBER;
    $fieldNameList[] = self::FIELD_WEBSITE;
    $fieldNameList[] = self::FIELD_SIC;
    $fieldNameList[] = self::FIELD_INDUSTRY;
    $fieldNameList[] = self::FIELD_ANNUAL_REVENUE;
    $fieldNameList[] = self::FIELD_NUMBER_OF_EMPLOYEES;
    $fieldNameList[] = self::FIELD_OWNERSHIP;
    $fieldNameList[] = self::FIELD_TICKER_SYMBOL;
    $fieldNameList[] = self::FIELD_DESCRIPTION;
    $fieldNameList[] = self::FIELD_RATING;
    $fieldNameList[] = self::FIELD_SITE;
    $fieldNameList[] = self::FIELD_OWNER_ID;
    $fieldNameList[] = self::FIELD_CREATED_DATE;
    $fieldNameList[] = self::FIELD_CREATED_BY_ID;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_DATE;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_BY_ID;
    $fieldNameList[] = self::FIELD_SYSTEM_MODSTAMP;
    $fieldNameList[] = self::FIELD_LAST_ACTIVITY_DATE;
    return($fieldNameList);
  } // getDefaultFieldNameList

} // CecSforceTableAccount
?>
