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
/* CecPaypalSetExpressCheckout.php */
$rDir = '';
require_once($rDir.'cec/php/intfc/paypal/CecPaypal.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'php/cec/CecLogger.php');

class CecPaypalSetExpressCheckout extends CecPaypal {

  /* control parameters */
  const PARAM_SXC_ADDR_OVERRIDE = 'ADDROVERRIDE'; // 1
  const PARAM_SXC_AMT = 'AMT'; // double
  const PARAM_SXC_CALLBACK = 'CALLBACK'; // URL
  const PARAM_SXC_CALLBACK_TIMEOUT = 'CALLBACKTIMEOUT'; // int: 4
  const PARAM_SXC_CANCEL_URL = 'CANCELURL';
  const PARAM_SXC_RETURN_URL = 'RETURNURL'; // url
  const PARAM_SXC_CURRENCY_CODE = 'CURRENCYCODE'; // USD
  const PARAM_SXC_INSURANCE_AMT = 'INSURANCEAMT'; // double
  const PARAM_SXC_INSURANCE_OPTION_OFFERED = 'INSURANCEOPTIONOFFERED';
  const PARAM_SXC_PAYMENT_ACTION = 'PAYMENTACTION'; // PAYMENT_ACTION_*

  const PARAM_SXC_ITEM_AMT = 'ITEMAMT'; // double: before shipping and tax
  const PARAM_SXC_MAX_AMT = 'MAXAMT'; // double: overall
  const PARAM_SXC_SHIP_DISC_AMT = 'SHIPDISCAMT'; // double(negative)
  const PARAM_SXC_SHIPPING_AMT = 'SHIPPINGAMT'; // double
  const PARAM_SXC_HANDLING_AMT = 'HANDLINGAMT'; // double
  const PARAM_SXC_TAX_AMT = 'TAXAMT'; // double

  const PARAM_SXC_SHIP_TO_NAME = 'SHIPTONAME';
  const PARAM_SXC_SHIP_TO_STREET = 'SHIPTOSTREET';
  const PARAM_SXC_SHIP_TO_CITY = 'SHIPTOCITY';
  const PARAM_SXC_SHIP_TO_STATE = 'SHIPTOSTATE';
  const PARAM_SXC_SHIP_TO_COUNTRY_CODE = 'SHIPTOCOUNTRYCODE';
  const PARAM_SXC_SHIP_TO_ZIP = 'SHIPTOZIP';

  /* line item parameters */
  const PARAM_SXC_L_AMT = 'L_AMT'; // double: line amount
  const PARAM_SXC_L_TAX_AMT = 'L_TAX_AMT'; // double: line tax
  const PARAM_SXC_L_DESC = 'L_DESC'; // string
  const PARAM_SXC_L_ITEM_WEIGHT_UNIT = 'L_ITEMWEIGHTUNIT'; // lbs
  const PARAM_SXC_L_ITEM_WEIGHT_VALUE = 'L_ITEMWEIGHTVALUE'; // decimal
  const PARAM_SXC_L_ITEM_LENGTH_VALUE = 'L_ITEMLENGTHVALUE'; // decimal
  const PARAM_SXC_L_ITEM_HEIGHT_VALUE = 'L_ITEMHEIGHTVALUE'; // decimal
  const PARAM_SXC_L_ITEM_WIDTH_VALUE = 'L_ITEMWIDTHVALUE'; // decimal
  const PARAM_SXC_L_NAME = 'L_NAME'; // string
  const PARAM_SXC_L_NUMBER = 'L_NUMBER'; // int
  const PARAM_SXC_L_QTY = 'L_QTY'; // double

  /* shipping option parameters */
  const PARAM_SXC_L_SHIPPING_OPTION_AMOUNT = 'L_SHIPPINGOPTIONAMOUNT'; // double
  const PARAM_SXC_L_SHIPPING_OPTION_IS_DEFAULT = 'L_SHIPPINGOPTIONISDEFAULT'; // true or false
  const PARAM_SXC_L_SHIPPING_OPTION_LABEL = 'L_SHIPPINGOPTIONLABEL'; // long description, e.g UPS Ground 7 Days
  const PARAM_SXC_L_SHIPPING_OPTION_NAME = 'L_SHIPPINGOPTIONNAME'; // short name: e.g. Ground

  /* indexed keys, e.g. PAYMENTREQUEST_0_SHIPTONAME */
  const PARAM_SXC_PAYMENT_REQUEST = 'PAYMENTREQUEST';
  const PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_NAME = 'SHIPTONAME';
  const PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_STREET = 'SHIPTOSTREET';
  const PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_STREET_2 = 'SHIPTOSTREET2'; // optional
  const PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_CITY = 'SHIPTOCITY';
  const PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_STATE = 'SHIPTOSTATE'; // optional
  const PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_ZIP = 'SHIPTOZIP';
  const PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_COUNTRY_CODE = 'SHIPTOCOUNTRYCODE';

  const IS_DEFAULT = 'true';

  const ADDROVERRIDE_TRUE = 1;
  const ADDROVERRIDE_FALSE = 0;

  /* setExpressCheckout response data fields */
  const FIELD_SXC_TOKEN = 'TOKEN';

  private static $priceFieldNames = Array(
    self::PARAM_SXC_AMT,
    self::PARAM_SXC_INSURANCE_AMT,
    self::PARAM_SXC_ITEM_AMT,
    self::PARAM_SXC_MAX_AMT,
    self::PARAM_SXC_SHIP_DISC_AMT,
    self::PARAM_SXC_SHIPPING_AMT,
    self::PARAM_SXC_HANDLING_AMT,
    self::PARAM_SXC_TAX_AMT,
    self::PARAM_SXC_L_AMT,
    self::PARAM_SXC_L_TAX_AMT,
    self::PARAM_SXC_L_SHIPPING_OPTION_AMOUNT,
  );

  public $amt; // double
  public $callback; // URL
  public $callbackTimeout; // int: 4
  public $cancelUrl;
  public $currencyCode; // USD
  public $insuranceAmt; // double
  public $insuranceOptionOffered;
  public $itemAmt; // double: before shipping and tax
  public $maxAmt; // double: overall
  public $paymentAction; // PAYMENT_ACTION_*
  public $returnUrl; // url
  public $shipDiscAmt; // double(negative)
  public $shippingAmt; // double
  public $taxAmt; // double
  public $addressOverride = self::ADDROVERRIDE_TRUE;

  public function newLineItem() {
    return Array(
      self::PARAM_SXC_L_AMT => null,
      self::PARAM_SXC_L_DESC => null,
      self::PARAM_SXC_L_ITEM_WEIGHT_UNIT => null,
      self::PARAM_SXC_L_ITEM_WEIGHT_VALUE => null,
      self::PARAM_SXC_L_NAME => null,
      self::PARAM_SXC_L_NUMBER => null,
      self::PARAM_SXC_L_QTY => null,
    ); // Array
  } // newLineItem

  public function newShippingOption() {
    return Array(
      self::PARAM_SXC_L_SHIPPING_OPTION_AMOUNT => null,
      self::PARAM_SXC_L_SHIPPING_OPTION_IS_DEFAULT => null,
      self::PARAM_SXC_L_SHIPPING_OPTION_LABEL => null,
      self::PARAM_SXC_L_SHIPPING_OPTION_NAME => null,
    ); // Array
  } // newShippingOption

  public function newShipToAddress() {
    return Array(
      self::PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_NAME => null,
      self::PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_STREET => null,
      self::PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_STREET_2 => null,
      self::PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_CITY => null,
      self::PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_STATE => null,
      self::PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_COUNTRY_CODE => null,
      self::PARAM_SXC_PAYMENT_REQUEST_SHIP_TO_ZIP => null,
    ); // Array
  } // newShipToAddress

  private function _calculateItemAmount($lineItemArray) {
    $total = 0.0;
    if (!empty($lineItemArray)) {
      foreach($lineItemArray as $index => $lineItem) {
        if (isset($lineItem[self::PARAM_SXC_L_AMT]) &&
            isset($lineItem[self::PARAM_SXC_L_QTY]))
          $total += $lineItem[self::PARAM_SXC_L_AMT]
            *$lineItem[self::PARAM_SXC_L_QTY];
      } // foreach
    } // if
    return $total;
  } // _calculateItemAmount

  private function _calculateAmount() {
    $amt = $this->itemAmt;
    if (isset($this->insuranceAmt)) $amt += $this->insuranceAmt;
    if (isset($this->shipDiscAmt)) $amt += $this->shipDiscAmt;
    if (isset($this->shippingAmt)) $amt += $this->shippingAmt;
    if (isset($this->taxAmt)) $amt += $this->taxAmt;
    return $amt;
  } // _calculateAmount

  private function _appendParam($uParam, $key, $value, $realKey=null) {
    if (is_null($realKey)) $realKey = $key;
    if (in_array($key, self::$priceFieldNames)) {
      $this->appendPrice($uParam, $realKey, $value);
    } else {
      $uParam->appendKeyValuePair($realKey, $value);
    } // else
  } // _appendParam

  private function _appendRequiredUrl($uParam, $paramName, $var) {
    if (!empty($var)) {
      $this->_appendParam($uParam, $paramName, $var);
    } else {
      throw new Exception($paramName." cannot be empty");
    } // else
  } // _appendRequiredUrl

  private function _appendArrayWithIndexedFields($uParam, $objArray) {
    if (is_array($objArray)) {
      foreach($objArray as $index => $obj) {
        foreach($obj as $key => $value) {
          $this->_appendParam($uParam, $key, $value, $key.$index);
        } // foreach
      } // foreach
    } // if
  } // _appendArrayWithIndexedFields

  private static function generatePaymentRequestField($paymentRequestIndex, $fieldName) {
    return self::PARAM_SXC_PAYMENT_REQUEST.self::SEP_FIELD
      .$paymentRequestIndex.self::SEP_FIELD.$fieldName;
  } // generatePaymentRequestField

  private function _buildNvpParamArray($shipToAddress, $lineItemArray,
      $shippingOptionArray) {
    $paymentRequestIndex = 0;
    $uParam = new CecUrlParam();
    /* payment action */
    if (isset($this->paymentAction)) {
      $this->_appendParam($uParam, self::PARAM_SXC_PAYMENT_ACTION, $this->paymentAction);
    } else {
      throw new Exception(self::PARAM_SXC_PAYMENT_ACTION." cannot be empty");
    } // else

    $this->_appendRequiredUrl($uParam, self::PARAM_SXC_RETURN_URL, $this->returnUrl);
    $this->_appendRequiredUrl($uParam, self::PARAM_SXC_CANCEL_URL, $this->cancelUrl);
    $this->_appendRequiredUrl($uParam, self::PARAM_SXC_CALLBACK, $this->callback);

    if (isset($this->callbackTimeout))
      $this->_appendParam($uParam, self::PARAM_SXC_CALLBACK_TIMEOUT,
        $this->callbackTimeout);

    if (isset($this->currencyCode))
      $this->_appendParam($uParam, self::PARAM_SXC_CURRENCY_CODE,
        $this->currencyCode);

    if (isset($this->insuranceOptionOffered))
      $this->_appendParam($uParam, self::PARAM_SXC_INSURANCE_OPTION_OFFERED,
        $this->insuranceOptionOffered);

    if (!isset($this->itemAmt)) {
      $this->itemAmt = $this->_calculateItemAmount($lineItemArray);
    } // if

    if (!isset($this->amt)) {
      $this->amt = $this->_calculateAmount();
    } // if

    if (!isset($this->maxAmt)) {
      $this->maxAmt = $this->amt;
    } // if

    $this->appendPrice($uParam, self::PARAM_SXC_AMT, $this->amt);
    $this->appendPrice($uParam, self::PARAM_SXC_ITEM_AMT, $this->itemAmt);
    $this->appendPrice($uParam, self::PARAM_SXC_INSURANCE_AMT, $this->insuranceAmt);
    $this->appendPrice($uParam, self::PARAM_SXC_SHIP_DISC_AMT, $this->shipDiscAmt);
    $this->appendPrice($uParam, self::PARAM_SXC_SHIPPING_AMT, $this->shippingAmt);
    $this->appendPrice($uParam, self::PARAM_SXC_TAX_AMT, $this->taxAmt);
    $this->appendPrice($uParam, self::PARAM_SXC_MAX_AMT, $this->maxAmt);

    /* ship to address */
    if (!is_null($shipToAddress)) {
      $this->_appendParam($uParam, self::PARAM_SXC_ADDR_OVERRIDE,
        $this->addressOverride);
      $uParam->appendKeyValueArray($shipToAddress);
      foreach($shipToAddress as $key => $value) {
        $newKey = self::generatePaymentRequestField($paymentRequestIndex, $key);
        $this->_appendParam($uParam, $newKey, $value);
      } // foreach
    } // if

    /* line items */
    $this->_appendArrayWithIndexedFields($uParam, $lineItemArray);

    /* shipping options */
    $this->_appendArrayWithIndexedFields($uParam, $shippingOptionArray);

    return $uParam;
  } // _buildNvpParamArray

  public function execute($shipToAddress, $lineItemArray, $shippingOptionArray) {
    $nvpParamArray = $this->_buildNvpParamArray($shipToAddress, $lineItemArray,
      $shippingOptionArray);
    $curlError = Array();
    return $this->hashCall(self::METHOD_SET_EXPRESS_CHECKOUT,
      $nvpParamArray, $curlError);
  } // execute

  public static function getResponseToken($responseArray) {
    if (empty($responseArray)) return null;
    if (empty($responseArray[self::FIELD_SXC_TOKEN])) return null;
    return urldecode($responseArray[self::FIELD_SXC_TOKEN]);
  } // getResponseToken

} // CecPaypalSetExpressCheckout
