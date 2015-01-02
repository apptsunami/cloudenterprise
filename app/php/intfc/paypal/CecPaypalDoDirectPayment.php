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
/* CecPaypalDoDirectPayment.php */
$rDir = '';
require_once($rDir.'cec/php/intfc/paypal/CecPaypal.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'php/cec/CecLogger.php');

class CecPaypalDoDirectPayment extends CecPaypal {

  const PARAM_DDP_PAYMENT_ACTION = 'PAYMENTACTION';
  const PARAM_DDP_AMT = 'AMT';
  const PARAM_DDP_CREDIT_CARD_TYPE = 'CREDITCARDTYPE';
  const PARAM_DDP_ACCT = 'ACCT';
  const PARAM_DDP_EXP_DATE = 'EXPDATE';
  const PARAM_DDP_CVV2 = 'CVV2';
  const PARAM_DDP_FIRST_NAME = 'FIRSTNAME';
  const PARAM_DDP_LAST_NAME = 'LASTNAME';
  const PARAM_DDP_STREET = 'STREET';
  const PARAM_DDP_CITY = 'CITY';
  const PARAM_DDP_STATE = 'STATE';
  const PARAM_DDP_ZIP = 'ZIP';
  const PARAM_DDP_COUNTRY_CODE = 'COUNTRYCODE';
  const PARAM_DDP_CURRENCY_CODE = 'CURRENCYCODE';

  const FIELD_DDP_TIMESTAMP = 'TIMESTAMP';
  const FIELD_DDP_CORRELATION_ID = 'CORRELATIONID';
  const FIELD_DDP_ACK = 'ACK';
  const FIELD_DDP_VERSION = 'VERSION';
  const FIELD_DDP_BUILD = 'BUILD';
  const FIELD_DDP_AMT = 'AMT';
  const FIELD_DDP_CURRENCY_CODE = 'CURRENCYCODE';
  const FIELD_DDP_AVS_CODE = 'AVSCODE';
  const FIELD_DDP_CVV2_MATCH = 'CVV2MATCH';
  const FIELD_DDP_TRANSACTION_ID = 'TRANSACTIONID';
  /* subscripted e.g. L_ERRORCODE0
  const FIELD_L_ERROR_CODE = 'L_ERRORCODE';
  const FIELD_L_LONG_MESSAGE = 'L_LONGMESSAGE';
  const FIELD_L_SEVERITY_CODE = 'L_SEVERITYCODE';
  const FIELD_L_SHORT_MESSAGE = 'L_SHORTMESSAGE';
   */

  const AVS_CODE_X = 'X';
  const CVV2_MATCH_M = 'M';

  public function execute($paymentType, $amount, $creditCardType,
      $creditCardNumber, $expDateMonth, $expDateYear, $cvv2Number,
      $firstName, $lastName, $address1, $city, $state, $zip,
      $countryCode, $currencyCode) {
    if (strlen($expDateMonth) == 1) {
      $expDateMonth = '0'.$expDateMonth;
    } // if
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair(self::PARAM_DDP_PAYMENT_ACTION, $paymentType);
    $this->appendPrice($uParam, self::PARAM_DDP_AMT, $amount);
    $uParam->appendKeyValuePair(self::PARAM_DDP_CREDIT_CARD_TYPE, $creditCardType);
    $uParam->appendKeyValuePair(self::PARAM_DDP_ACCT, $creditCardNumber);
    $uParam->appendKeyValuePair(self::PARAM_DDP_EXP_DATE, $expDateMonth.$expDateYear);
    $uParam->appendKeyValuePair(self::PARAM_DDP_CVV2, $cvv2Number);
    $uParam->appendKeyValuePair(self::PARAM_DDP_FIRST_NAME, $firstName);
    $uParam->appendKeyValuePair(self::PARAM_DDP_LAST_NAME, $lastName);
    $uParam->appendKeyValuePair(self::PARAM_DDP_STREET, $address1);
    $uParam->appendKeyValuePair(self::PARAM_DDP_CITY, $city);
    $uParam->appendKeyValuePair(self::PARAM_DDP_STATE, $state);
    $uParam->appendKeyValuePair(self::PARAM_DDP_ZIP, $zip);
    $uParam->appendKeyValuePair(self::PARAM_DDP_COUNTRY_CODE, $countryCode);
    $uParam->appendKeyValuePair(self::PARAM_DDP_CURRENCY_CODE, $currencyCode);
    $curlError = Array();
    return $this->hashCall(self::METHOD_DO_DIRECT_PAYMENT, $uParam, $curlError);
  } // execute

  public static function parseResponse($respArray) {
    $errorMessageArray = self::parseSubscriptedKey($respArray);
    return Array($respArray, $errorMessageArray);
  } // parseResponse

} // CecPaypalDoDirectPayment
/*
-- success

"TIMESTAMP":"2012-08-06T20:32:46Z"
"CORRELATIONID":"432bb76dcee94"
"ACK":"Success"
"VERSION":"92.0"
"BUILD":"3288089"
"AMT":"674.00"
"CURRENCYCODE":"USD"
"AVSCODE":"X"
"CVV2MATCH":"M"
"TRANSACTIONID":"3BP422194R783910C"

-- error

"TIMESTAMP":"2012-08-06T19:41:30Z"
"CORRELATIONID":"dff6356ed32ad"
"ACK":"Failure"
"VERSION":"92.0"
"BUILD":"3288089"
"L_ERRORCODE0":"10527"
"L_ERRORCODE1":"10563"
"L_ERRORCODE2":"10562"
"L_ERRORCODE3":"81234"
"L_SHORTMESSAGE0":"Invalid Data"
"L_SHORTMESSAGE1":"Invalid Data"
"L_SHORTMESSAGE2":"Invalid Data"
"L_SHORTMESSAGE3":"Invalid Parameter"
"L_LONGMESSAGE0":"This transaction cannot be processed. Please enter a valid credit card number and type."
"L_LONGMESSAGE1":"This transaction cannot be processed. Please enter a valid credit card expiration month."
"L_LONGMESSAGE2":"This transaction cannot be processed. Please enter a valid credit card expiration year."
"L_LONGMESSAGE3":"ExpDate : Invalid Parameter"
"L_SEVERITYCODE0":"Error"
"L_SEVERITYCODE1":"Error"
"L_SEVERITYCODE2":"Error"
"L_SEVERITYCODE3":"Error"

*/
