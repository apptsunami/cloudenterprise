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
/* CecSforceTableOpportunity.php */
require_once('cec/php/models/CecSforceTable.php');

/**
 *  Supports Sforce table 'Opportunity'.
 */
class CecSforceTableOpportunity extends CecSforceTable {

  const TABLE_NAME = 'Opportunity';
  const FIELD_ACCOUNT_ID = 'AccountId';
  const FIELD_IS_PRIVATE = 'IsPrivate';
  const FIELD_NAME = 'Name';
  const FIELD_DESCRIPTION = 'Description';
  const FIELD_STAGE_NAME = 'StageName';
  const FIELD_AMOUNT = 'Amount';
  const FIELD_PROBABILITY = 'Probability';
  const FIELD_EXPECTED_REVENUE = 'ExpectedRevenue';
  const FIELD_TOTAL_OPPORTUNITY_QUANTITY = 'TotalOpportunityQuantity';
  const FIELD_CLOSE_DATE = 'CloseDate';
  const FIELD_TYPE = 'Type';
  const FIELD_NEXT_STEP = 'NextStep';
  const FIELD_LEAD_SOURCE = 'LeadSource';
  const FIELD_IS_CLOSED = 'IsClosed';
  const FIELD_IS_WON = 'IsWon';
  const FIELD_FORECAST_CATEGORY = 'ForecastCategory';
  const FIELD_CAMPAIGN_ID = 'CampaignId';
  const FIELD_HAS_OPPORTUNITY_LINE_ITEM = 'HasOpportunityLineItem';
  const FIELD_PRICEBOOK_ID = 'PricebookId';
  const FIELD_PRICEBOOK2_ID = 'Pricebook2Id';
  const FIELD_OWNER_ID = 'OwnerId';
  const FIELD_CREATED_DATE = 'CreatedDate';
  const FIELD_CREATED_BY_ID = 'CreatedById';
  const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
  const FIELD_LAST_MODIFIED_BY_ID = 'LastModifiedById';
  const FIELD_LAST_ACTIVITY_DATE = 'LastActivityDate';
  const FIELD_FISCAL_QUARTER = 'FiscalQuarter';
  const FIELD_FISCAL_YEAR = 'FiscalYear';
  const FIELD_FISCAL = 'Fiscal';

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
    $fieldNameList[] = self::FIELD_IS_PRIVATE;
    $fieldNameList[] = self::FIELD_NAME;
    $fieldNameList[] = self::FIELD_DESCRIPTION;
    $fieldNameList[] = self::FIELD_STAGE_NAME;
    $fieldNameList[] = self::FIELD_AMOUNT;
    $fieldNameList[] = self::FIELD_PROBABILITY;
    $fieldNameList[] = self::FIELD_EXPECTED_REVENUE;
    $fieldNameList[] = self::FIELD_TOTAL_OPPORTUNITY_QUANTITY;
    $fieldNameList[] = self::FIELD_CLOSE_DATE;
    $fieldNameList[] = self::FIELD_TYPE;
    $fieldNameList[] = self::FIELD_NEXT_STEP;
    $fieldNameList[] = self::FIELD_LEAD_SOURCE;
    $fieldNameList[] = self::FIELD_IS_CLOSED;
    $fieldNameList[] = self::FIELD_IS_WON;
    $fieldNameList[] = self::FIELD_FORECAST_CATEGORY;
    $fieldNameList[] = self::FIELD_CAMPAIGN_ID;
    $fieldNameList[] = self::FIELD_HAS_OPPORTUNITY_LINE_ITEM;
    $fieldNameList[] = self::FIELD_PRICEBOOK_ID;
    $fieldNameList[] = self::FIELD_PRICEBOOK2_ID;
    $fieldNameList[] = self::FIELD_OWNER_ID;
    $fieldNameList[] = self::FIELD_CREATED_DATE;
    $fieldNameList[] = self::FIELD_CREATED_BY_ID;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_DATE;
    $fieldNameList[] = self::FIELD_LAST_MODIFIED_BY_ID;
    $fieldNameList[] = self::FIELD_SYSTEM_MODSTAMP;
    $fieldNameList[] = self::FIELD_LAST_ACTIVITY_DATE;
    $fieldNameList[] = self::FIELD_FISCAL_QUARTER;
    $fieldNameList[] = self::FIELD_FISCAL_YEAR;
    $fieldNameList[] = self::FIELD_FISCAL;
    return($fieldNameList);
  } // getDefaultFieldNameList

} // CecSforceTableOpportunity
?>
