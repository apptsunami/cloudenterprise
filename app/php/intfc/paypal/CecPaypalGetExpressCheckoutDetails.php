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
/* CecPaypalGetExpressCheckoutDetails.php */
$rDir = '';
require_once($rDir.'cec/php/intfc/paypal/CecPaypal.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'php/cec/CecLogger.php');

class CecPaypalGetExpressCheckoutDetails extends CecPaypal {

  const PARAM_GXCD_TOKEN = 'token';

  const FIELD_GXCD_TOKEN = 'TOKEN';
  const FIELD_GXCD_CHECKOUT_STATUS = 'CHECKOUTSTATUS';
  const FIELD_GXCD_TIMESTAMP = 'TIMESTAMP';
  const FIELD_GXCD_CORRELATION_ID = 'CORRELATIONID';
  const FIELD_GXCD_ACK = 'ACK';
  const FIELD_GXCD_VERSION = 'VERSION';
  const FIELD_GXCD_BUILD = 'BUILD';
  const FIELD_GXCD_EMAIL = 'EMAIL';
  const FIELD_GXCD_PAYER_ID = 'PAYERID';
  const FIELD_GXCD_PAYER_STATUS = 'PAYERSTATUS';
  const FIELD_GXCD_FIRST_NAME = 'FIRSTNAME';
  const FIELD_GXCD_LAST_NAME = 'LASTNAME';
  const FIELD_GXCD_COUNTRY_CODE = 'COUNTRYCODE';
  const FIELD_GXCD_SHIP_TO_NAME = 'SHIPTONAME';
  const FIELD_GXCD_SHIP_TO_STREET = 'SHIPTOSTREET';
  const FIELD_GXCD_SHIP_TO_CITY = 'SHIPTOCITY';
  const FIELD_GXCD_SHIP_TO_STATE = 'SHIPTOSTATE';
  const FIELD_GXCD_SHIP_TO_ZIP = 'SHIPTOZIP';
  const FIELD_GXCD_SHIP_TO_COUNTRY_CODE = 'SHIPTOCOUNTRYCODE';
  const FIELD_GXCD_SHIP_TO_COUNTRY_NAME = 'SHIPTOCOUNTRYNAME';
  const FIELD_GXCD_ADDRESS_STATUS = 'ADDRESSSTATUS';
  const FIELD_GXCD_CURRENCY_CODE = 'CURRENCYCODE';
  const FIELD_GXCD_AMT = 'AMT';
  const FIELD_GXCD_ITEM_AMT = 'ITEMAMT';
  const FIELD_GXCD_SHIPPING_AMT = 'SHIPPINGAMT';
  const FIELD_GXCD_HANDLING_AMT = 'HANDLINGAMT';
  const FIELD_GXCD_TAX_AMT = 'TAXAMT';
  const FIELD_GXCD_INSURANCE_AMT = 'INSURANCEAMT';
  const FIELD_GXCD_SHIP_DISC_AMT = 'SHIPDISCAMT';
  const FIELD_GXCD_INSURANCE_OPTION_OFFERED = 'INSURANCEOPTIONOFFERED';
  const FIELD_GXCD_SHIPPING_CALCULATION_MODE = 'SHIPPINGCALCULATIONMODE';
  const FIELD_GXCD_INSURANCE_OPTION_SELECTED = 'INSURANCEOPTIONSELECTED';
  const FIELD_GXCD_SHIPPING_OPTION_IS_DEFAULT = 'SHIPPINGOPTIONISDEFAULT';
  const FIELD_GXCD_SHIPPING_OPTION_AMOUNT = 'SHIPPINGOPTIONAMOUNT';
  const FIELD_GXCD_SHIPPING_OPTION_NAME = 'SHIPPINGOPTIONNAME';

  /* index field names, e.g. L_NAME0 */
  const FIELD_GXCD_L_NAME = 'L_NAME';
  const FIELD_GXCD_L_QTY = 'L_QTY';
  const FIELD_GXCD_L_TAXAMT = 'L_TAXAMT';
  const FIELD_GXCD_L_AMT = 'L_AMT';
  const FIELD_GXCD_L_DESC = 'L_DESC';
  const FIELD_GXCD_L_ITEM_WEIGHT_VALUE = 'L_ITEMWEIGHTVALUE';
  const FIELD_GXCD_L_ITEM_LENGTH_VALUE = 'L_ITEMLENGTHVALUE';
  const FIELD_GXCD_L_ITEM_WIDTH_VALUE = 'L_ITEMWIDTHVALUE';
  const FIELD_GXCD_L_ITEM_HEIGHT_VALUE = 'L_ITEMHEIGHTVALUE';

  /* indexed subfield name, e.g. PAYMENTREQUEST_0_CURRENCYCODE */
  const FIELD_GXCD_PAYMENT_REQUEST = 'PAYMENTREQUEST';
  const FIELD_GXCD_PAYMENT_REQUEST_CURRENCY_CODE = 'CURRENCYCODE';
  const FIELD_GXCD_PAYMENT_REQUEST_AMT = 'AMT';
  const FIELD_GXCD_PAYMENT_REQUEST_ITEM_AMT = 'ITEMAMT';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIPPING_AMT = 'SHIPPINGAMT';
  const FIELD_GXCD_PAYMENT_REQUEST_HANDLING_AMT = 'HANDLINGAMT';
  const FIELD_GXCD_PAYMENT_REQUEST_TAX_AMT = 'TAXAMT';
  const FIELD_GXCD_PAYMENT_REQUEST_INSURANCE_AMT = 'INSURANCEAMT';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_DISC_AMT = 'SHIPDISCAMT';
  const FIELD_GXCD_PAYMENT_REQUEST_INSURANCE_OPTION_OFFERED = 'INSURANCEOPTIONOFFERED';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_TO_NAME = 'SHIPTONAME';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_TO_STREET = 'SHIPTOSTREET';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_TO_CITY = 'SHIPTOCITY';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_TO_STATE = 'SHIPTOSTATE';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_TO_ZIP = 'SHIPTOZIP';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_TO_COUNTRY_CODE = 'SHIPTOCOUNTRYCODE';
  const FIELD_GXCD_PAYMENT_REQUEST_SHIP_TO_COUNTRY_NAME = 'SHIPTOCOUNTRYNAME';

  /* indexed subfield name, e.g. L_PAYMENTREQUEST_0_NAME0 */
  const FIELD_GXCD_L_PAYMENT_REQUEST = 'L_PAYMENTREQUEST';
  const FIELD_GXCD_L_PAYMENT_REQUEST_NAME = 'NAME';
  const FIELD_GXCD_L_PAYMENT_REQUEST_QTY = 'QTY';
  const FIELD_GXCD_L_PAYMENT_REQUEST_TAXAMT = 'TAXAMT';
  const FIELD_GXCD_L_PAYMENT_REQUEST_AMT = 'AMT';
  const FIELD_GXCD_L_PAYMENT_REQUEST_DESC = 'DESC';
  const FIELD_GXCD_L_PAYMENT_REQUEST_ITEM_WEIGHT_VALUE = 'ITEMWEIGHTVALUE';
  const FIELD_GXCD_L_PAYMENT_REQUEST_ITEM_LENGTH_VALUE = 'ITEMLENGTHVALUE';
  const FIELD_GXCD_L_PAYMENT_REQUEST_ITEM_WIDTH_VALUE = 'ITEMWIDTHVALUE';
  const FIELD_GXCD_L_PAYMENT_REQUEST_ITEM_HEIGHT_VALUE = 'ITEMHEIGHTVALUE';

  /* indexed subfield name, e.g. PAYMENTREQUESTINFO_0_ERRORCODE */
  const FIELD_GXCD_PAYMENT_REQUEST_INFO = 'PAYMENTREQUESTINFO';
  const FIELD_GXCD_PAYMENT_REQUEST_INFO_ERROR_CODE = 'ERRORCODE';
  const CHECK_OUT_STATUS_PAYMENT_ACTION_NOT_INITIATED = 'PaymentActionNotInitiated';
  const PAYER_STATUS_VERIFIED = 'verified';
  const SHIPPING_CALCULATION_MODE_FLAT_RATE = 'FlatRate';

  const IS_TRUE = 'true';
  const IS_FALSE = 'false';

  private static $LINE_ITEM_FIELDS = Array(
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_NAME,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_QTY,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_TAXAMT,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_AMT,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_DESC,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_ITEM_WEIGHT_VALUE,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_ITEM_LENGTH_VALUE,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_ITEM_WIDTH_VALUE,
    CecPaypalGetExpressCheckoutDetails::FIELD_GXCD_L_ITEM_HEIGHT_VALUE,
  ); // Array

  public function execute($token) {
    if (empty($token)) {
      throw new Exception("token cannot be empty");
    } // if
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair(self::PARAM_GXCD_TOKEN, $token);
    $curlErr = Array();
    return $this->hashCall(self::METHOD_GET_EXPRESS_CHECKOUT_DETAILS, $uParam,
      $curlErr);
  } // execute

  private static function parseLineItems(&$respArray) {
    $lineItemArray = Array();
    foreach($respArray as $key => $value) {
      list($subKey, $index) = self::splitKeyWithIndex($key);
      if (is_null($subKey)) continue;
      if (!in_array($subKey, self::$LINE_ITEM_FIELDS)) continue;
      if (!isset($lineItemArray[$index])) {
        $lineItemArray[$index] = Array();
      } // if
      $lineItemArray[$index][$subKey] = $value;
      unset($respArray[$key]);
    } // foreach
    return $lineItemArray;
  } // parseLineItems

  public static function parseResponse($respArray) {
    $paymentRequestArray = self::parseIndexedKey($respArray,
      self::FIELD_GXCD_PAYMENT_REQUEST, true);
    $paymentRequestInfoArray = self::parseIndexedKey($respArray,
      self::FIELD_GXCD_PAYMENT_REQUEST_INFO, true);
    $lPaymentRequestArray = self::parseIndexedKey($respArray,
      self::FIELD_GXCD_L_PAYMENT_REQUEST, false);
    $lineItemArray = self::parseLineItems($respArray);
    return Array($respArray, $lineItemArray, $paymentRequestArray,
      $lPaymentRequestArray, $paymentRequestInfoArray);
  } // parseResponse

  public static function generatePaymentRequestFieldName($index, $fieldName) {
    return self::FIELD_GXCD_PAYMENT_REQUEST.self::SEP_FIELD
      .intval($index).self::SEP_FIELD.$fieldName;
  } // generatePaymentRequestFieldName

} // GetExpressCheckoutDetails

/*
    response example:

    TOKEN=>EC-2WG31314JD575325Y
    CHECKOUTSTATUS=>PaymentActionNotInitiated
    TIMESTAMP=>2012-08-01T17:36:29Z
    CORRELATIONID=>f9c61bf84065
    ACK=>Success
    VERSION=>92.0
    BUILD=>3386080
    EMAIL=>it_1343711675_per@apptsunami.com
    PAYERID=>ASAT2497YA8YS
    PAYERSTATUS=>verified
    FIRSTNAME=>Brian
    LASTNAME=>Buyer
    COUNTRYCODE=>US
    SHIPTONAME=>Brian Buyer
    SHIPTOSTREET=>1 Main St
    SHIPTOCITY=>San Jose
    SHIPTOSTATE=>CA
    SHIPTOZIP=>95131
    SHIPTOCOUNTRYCODE=>US
    SHIPTOCOUNTRYNAME=>United States
    ADDRESSSTATUS=>Confirmed
    CURRENCYCODE=>USD
    AMT=>509.00
    ITEMAMT=>499.00
    SHIPPINGAMT=>10.00
    HANDLINGAMT=>0.00
    TAXAMT=>0.00
    INSURANCEAMT=>0.00
    SHIPDISCAMT=>0.00
    INSURANCEOPTIONOFFERED=>false
    L_NAME0=>GE Built-In Dishwasher
    L_NAME1=>Energy Efficient Air Conditioners
    L_QTY0=>1
    L_QTY1=>1
    L_TAXAMT0=>0.00
    L_TAXAMT1=>0.00
    L_AMT0=>354.00
    L_AMT1=>145.00
    L_DESC0=>GE Built-In Dishwasher
    L_DESC1=>Energy Efficient Air Conditioners
    L_ITEMWEIGHTVALUE0=> 0.00000
    L_ITEMWEIGHTVALUE1=> 0.00000
    L_ITEMLENGTHVALUE0=> 0.00000
    L_ITEMLENGTHVALUE1=> 0.00000
    L_ITEMWIDTHVALUE0=> 0.00000
    L_ITEMWIDTHVALUE1=> 0.00000
    L_ITEMHEIGHTVALUE0=> 0.00000
    L_ITEMHEIGHTVALUE1=> 0.00000
    SHIPPINGCALCULATIONMODE=>FlatRate
    INSURANCEOPTIONSELECTED=>false
    SHIPPINGOPTIONISDEFAULT=>true
    SHIPPINGOPTIONAMOUNT=>10.00
    SHIPPINGOPTIONNAME=>Standard
    PAYMENTREQUEST_0_CURRENCYCODE=>USD
    PAYMENTREQUEST_0_AMT=>509.00
    PAYMENTREQUEST_0_ITEMAMT=>499.00
    PAYMENTREQUEST_0_SHIPPINGAMT=>10.00
    PAYMENTREQUEST_0_HANDLINGAMT=>0.00
    PAYMENTREQUEST_0_TAXAMT=>0.00
    PAYMENTREQUEST_0_INSURANCEAMT=>0.00
    PAYMENTREQUEST_0_SHIPDISCAMT=>0.00
    PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED=>false
    PAYMENTREQUEST_0_SHIPTONAME=>Brian Buyer
    PAYMENTREQUEST_0_SHIPTOSTREET=>1 Main St
    PAYMENTREQUEST_0_SHIPTOCITY=>San Jose
    PAYMENTREQUEST_0_SHIPTOSTATE=>CA
    PAYMENTREQUEST_0_SHIPTOZIP=>95131
    PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=>US
    PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME=>United States
    L_PAYMENTREQUEST_0_NAME0=>GE Built-In Dishwasher
    L_PAYMENTREQUEST_0_NAME1=>Energy Efficient Air Conditioners
    L_PAYMENTREQUEST_0_QTY0=>1
    L_PAYMENTREQUEST_0_QTY1=>1
    L_PAYMENTREQUEST_0_TAXAMT0=>0.00
    L_PAYMENTREQUEST_0_TAXAMT1=>0.00
    L_PAYMENTREQUEST_0_AMT0=>354.00
    L_PAYMENTREQUEST_0_AMT1=>145.00
    L_PAYMENTREQUEST_0_DESC0=>GE Built-In Dishwasher
    L_PAYMENTREQUEST_0_DESC1=>Energy Efficient Air Conditioners
    L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE0=> 0.00000
    L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE1=> 0.00000
    L_PAYMENTREQUEST_0_ITEMLENGTHVALUE0=> 0.00000
    L_PAYMENTREQUEST_0_ITEMLENGTHVALUE1=> 0.00000
    L_PAYMENTREQUEST_0_ITEMWIDTHVALUE0=> 0.00000
    L_PAYMENTREQUEST_0_ITEMWIDTHVALUE1=> 0.00000
    L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE0=> 0.00000
    L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE1=> 0.00000
    PAYMENTREQUESTINFO_0_ERRORCODE=>0
*/
