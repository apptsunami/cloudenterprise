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
/* CecPaypalDoExpressCheckoutPayment.php */
$rDir = '';
require_once($rDir.'cec/php/intfc/paypal/CecPaypal.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'php/cec/CecLogger.php');

class CecPaypalDoExpressCheckoutPayment extends CecPaypal {

  const PARAM_DXCP_TOKEN = 'TOKEN';
  const PARAM_DXCP_PAYER_ID = 'PAYERID';
  const PARAM_DXCP_PAYMENT_ACTION = 'PAYMENTACTION';
  const PARAM_DXCP_AMT = 'AMT';
  const PARAM_DXCP_CURRENCY_CODE = 'CURRENCYCODE';
  const PARAM_DXCP_IP_ADDRESS = 'IPADDRESS';

  const FIELD_DXCP_TOKEN = 'TOKEN';
  const FIELD_DXCP_SUCCESS_PAGE_REDIRECT_REQUESTED = 'SUCCESSPAGEREDIRECTREQUESTED';
  const FIELD_DXCP_TIMESTAMP = 'TIMESTAMP';
  const FIELD_DXCP_CORRELATION_ID = 'CORRELATIONID';
  const FIELD_DXCP_ACK = 'ACK';
  const FIELD_DXCP_VERSION = 'VERSION';
  const FIELD_DXCP_BUILD = 'BUILD';
  const FIELD_DXCP_TRANSACTION_ID = 'TRANSACTIONID';
  const FIELD_DXCP_TRANSACTION_TYPE = 'TRANSACTIONTYPE';
  const FIELD_DXCP_PAYMENT_TYPE = 'PAYMENTTYPE';
  const FIELD_DXCP_ORDER_TIME = 'ORDERTIME';
  const FIELD_DXCP_AMT = 'AMT';
  const FIELD_DXCP_FEE_AMT = 'FEEAMT';
  const FIELD_DXCP_TAX_AMT = 'TAXAMT';
  const FIELD_DXCP_CURRENCY_CODE = 'CURRENCYCODE';
  const FIELD_DXCP_PAYMENT_STATUS = 'PAYMENTSTATUS';
  const FIELD_DXCP_PENDING_REASON = 'PENDINGREASON';
  const FIELD_DXCP_REASON_CODE = 'REASONCODE';
  const FIELD_DXCP_PROTECTION_ELIGIBILITY = 'PROTECTIONELIGIBILITY';
  const FIELD_DXCP_INSURANCE_OPTION_SELECTED = 'INSURANCEOPTIONSELECTED';
  const FIELD_DXCP_SHIPPING_OPTION_IS_DEFAULT = 'SHIPPINGOPTIONISDEFAULT';

  /* indexed keys, e.g. PAYMENTINFO_0_TRANSACTIONID */
  const FIELD_DXCP_PAYMENT_INFO = 'PAYMENTINFO';
  const FIELD_DXCP_PAYMENT_INFO_TRANSACTION_ID = 'TRANSACTIONID';
  const FIELD_DXCP_PAYMENT_INFO_TRANSACTION_TYPE = 'TRANSACTIONTYPE';
  const FIELD_DXCP_PAYMENT_INFO_PAYMENT_TYPE = 'PAYMENTTYPE';
  const FIELD_DXCP_PAYMENT_INFO_ORDER_TIME = 'ORDERTIME';
  const FIELD_DXCP_PAYMENT_INFO_AMT = 'AMT';
  const FIELD_DXCP_PAYMENT_INFO_FEE_AMT = 'FEEAMT';
  const FIELD_DXCP_PAYMENT_INFO_TAX_AMT = 'TAXAMT';
  const FIELD_DXCP_PAYMENT_INFO_CURRENCY_CODE = 'CURRENCYCODE';
  const FIELD_DXCP_PAYMENT_INFO_PAYMENT_STATUS = 'PAYMENTSTATUS';
  const FIELD_DXCP_PAYMENT_INFO_PENDING_REASON = 'PENDINGREASON';
  const FIELD_DXCP_PAYMENT_INFO_REASON_CODE = 'REASONCODE';
  const FIELD_DXCP_PAYMENT_INFO_PROTECTION_ELIGIBILITY = 'PROTECTIONELIGIBILITY';
  const FIELD_DXCP_PAYMENT_INFO_PROTECTION_ELIGIBILITY_TYPE = 'PROTECTIONELIGIBILITYTYPE';
  const FIELD_DXCP_PAYMENT_INFO_SECURE_MERCHANT_ACCOUNT_ID = 'SECUREMERCHANTACCOUNTID';
  const FIELD_DXCP_PAYMENT_INFO_ERROR_CODE = 'ERRORCODE';
  const FIELD_DXCP_PAYMENT_INFO_ACK = 'ACK';

  const PAYMENT_STATUS_COMPLETED = 'Completed';
  const PAYMENT_STATUS_PENDING = 'Pending';

  public function execute($token, $payerID, $paymentAction, $paymentAmount,
      $currencyCode, $hostName) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair(self::PARAM_DXCP_TOKEN, $token);
    $uParam->appendKeyValuePair(self::PARAM_DXCP_PAYER_ID, $payerID);
    $uParam->appendKeyValuePair(self::PARAM_DXCP_PAYMENT_ACTION, $paymentAction);
    $uParam->appendKeyValuePair(self::PARAM_DXCP_AMT, $paymentAmount);
    $uParam->appendKeyValuePair(self::PARAM_DXCP_CURRENCY_CODE, $currencyCode);
    $uParam->appendKeyValuePair(self::PARAM_DXCP_IP_ADDRESS, $hostName);
    $curlError = Array();
    return $this->hashCall(self::METHOD_DO_EXPRESS_CHECKOUT_PAYMENT,
      $uParam, $curlError);
  } // execute

  public static function parseResponse($respArray) {
    $paymentInfoArray = self::parseIndexedKey($respArray,
      self::FIELD_DXCP_PAYMENT_INFO, true);
    return Array($respArray, $paymentInfoArray);
  } // parseResponse

} // CecPaypalDoExpressCheckoutPayment
/*
response example:

TOKEN=>EC-8FY05389826111317
SUCCESSPAGEREDIRECTREQUESTED=>false
TIMESTAMP=>2012-08-01T23:28:37Z
CORRELATIONID=>fdd09d50e1ca6
ACK=>Success
VERSION=>92.0
BUILD=>3386080
TRANSACTIONID=>2XN243474L878701W
TRANSACTIONTYPE=>expresscheckout
PAYMENTTYPE=>instant
ORDERTIME=>2012-08-01T23:25:21Z
AMT=>509.00
FEEAMT=>15.06
TAXAMT=>0.00
CURRENCYCODE=>USD
PAYMENTSTATUS=>Completed
PENDINGREASON=>None
REASONCODE=>None
PROTECTIONELIGIBILITY=>Eligible
INSURANCEOPTIONSELECTED=>false
SHIPPINGOPTIONISDEFAULT=>false
PAYMENTINFO_0_TRANSACTIONID=>2XN243474L878701W
PAYMENTINFO_0_TRANSACTIONTYPE=>expresscheckout
PAYMENTINFO_0_PAYMENTTYPE=>instant
PAYMENTINFO_0_ORDERTIME=>2012-08-01T23:25:21Z
PAYMENTINFO_0_AMT=>509.00
PAYMENTINFO_0_FEEAMT=>15.06
PAYMENTINFO_0_TAXAMT=>0.00
PAYMENTINFO_0_CURRENCYCODE=>USD
PAYMENTINFO_0_PAYMENTSTATUS=>Completed
PAYMENTINFO_0_PENDINGREASON=>None
PAYMENTINFO_0_REASONCODE=>None
PAYMENTINFO_0_PROTECTIONELIGIBILITY=>Eligible
PAYMENTINFO_0_PROTECTIONELIGIBILITYTYPE=>ItemNotReceivedEligible,UnauthorizedPaymentEligible
PAYMENTINFO_0_SECUREMERCHANTACCOUNTID=>RJZ46XLD66SR8
PAYMENTINFO_0_ERRORCODE=>0
PAYMENTINFO_0_ACK=>Success
*/
/*
  ["TIMESTAMP"] => string(20) "2012-08-01T23:59:23Z"
  ["CORRELATIONID"] => string(13) "83ade13a1fa1f"
  ["ACK"] => string(7) "Failure"
  ["VERSION"] => string(4) "92.0"
  ["BUILD"] => string(7) "3386080"
  ["L_ERRORCODE0"] => string(5) "10416"
  ["L_SHORTMESSAGE0"] => string(94) "Transaction refused because of an invalid argument. See additional error messages for details."
  ["L_LONGMESSAGE0"] => string(72) "You have exceeded the maximum number of payment attempts for this token."
  ["L_SEVERITYCODE0"] => string(5) "Error"

*/
