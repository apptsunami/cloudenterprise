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
/* CecPaypal.php */
$rDir = '';
require_once($rDir.'cec/php/views/CecUrlParam.php');

class CecPaypal {
  const IMG_CHECK_OUT_WITH_PAYPAL =
    '<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;">';

  const IMG_PAYMENT_METHOD =
    '<img src="https://www.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" align="left" style="margin-right:7px;"><span style="font-size:11px; font-family:Arial,Verdana;">The safer, easier way to pay.</span>';

  const URL_SANDBOX = 'https://www.sandbox.paypal.com/webscr';
  const URL_PRODUCTION = 'https://www.paypal.com/webscr';
  const URL_CALLBACK = 'https://www.ppcallback.com/callback.pl';

  const API_ENDPOINT = 'https://api-3t.sandbox.paypal.com/nvp';

  const CMD_EXPRESS_CHECKOUT = '_express-checkout';

  /* also called payment type */
  const PAYMENT_ACTION_AUTHORIZATION = 'Authorization';
  const PAYMENT_ACTION_ORDER = 'Order';
  const PAYMENT_ACTION_SALE = 'Sale';

  /* symbol used in parameter names */
  const SEP_FIELD = '_';
  const PATTERN_INTEGER = '/[0-9]+$/';

  /* request parameters: case-sensitive */
  const PARAM_CMD = 'cmd';
  const PARAM_TOKEN = 'token';
  const PARAM_PWD = 'PWD';
  const PARAM_USER = 'USER';
  const PARAM_SIGNATURE = 'SIGNATURE';
  const PARAM_SUBJECT = 'SUBJECT';
  const PARAM_TIMESTAMP = 'TIMESTAMP';
  const PARAM_VERSION = 'VERSION';
  const PARAM_METHOD = 'METHOD';
  const PARAM_AUTH_TOKEN = 'TOKEN';
  const PARAM_USER_ACTION = 'useraction';

  /* response parameters */
  const FIELD_ACK = 'ACK';
  const FIELD_TIMESTAMP = 'TIMESTAMP';
  const FIELD_CORRELATIONID = 'CORRELATIONID';
  const FIELD_VERSION = 'VERSION';
  const FIELD_BUILD = 'BUILD';

  /* indexed keys, e.g. L_ERRORCODE0 */
  const FIELD_L_ERRORCODE = 'L_ERRORCODE';
  const FIELD_L_SHORTMESSAGE = 'L_SHORTMESSAGE';
  const FIELD_L_LONGMESSAGE = 'L_LONGMESSAGE';
  const FIELD_L_SEVERITYCODE = 'L_SEVERITYCODE';

  /* review callback data fields */
  const FIELD_REVIEW_CALLBACK_TOKEN = 'token';

  const METHOD_SET_EXPRESS_CHECKOUT = 'SetExpressCheckout';
  const METHOD_GET_EXPRESS_CHECKOUT_DETAILS = 'GetExpressCheckoutDetails';
  const METHOD_DO_EXPRESS_CHECKOUT_PAYMENT = 'DoExpressCheckoutPayment';
  const METHOD_DO_DIRECT_PAYMENT = 'doDirectPayment';

  const ACK_SUCCESS = 'SUCCESS';
  const ACK_SUCCESS_WITH_WARNING = 'SUCCESSWITHWARNING';
  const ACK_FAILURE = 'Failure';

  const USER_ACTION_COMMIT = 'commit';

  // Merchant's API 3-TOKEN Credential is required to make API Call.
  const AUTH_MODE_3TOKEN = "3TOKEN";
  // Only merchant Email is required to make EC Calls.
  const AUTH_MODE_FIRST_PARTY = "FIRSTPARTY";
  // Partner's API Credential and Merchant Email as Subject are required.
  const AUTH_MODE_THIRD_PARTY = "THIRDPARTY";
  const AUTH_MODE_PERMISSION = "PERMISSION";

  const CURL_ERRNO_SSL_CERT = 60;
  const DEFAULT_SSL_CERT_FILE_NAME = '/cacert.pem';
  const DEFAULT_API_VERSION = '92.0';

  private static $ERROR_CODE_FIELDS = Array(
    self::FIELD_L_SEVERITYCODE,
    self::FIELD_L_ERRORCODE,
    self::FIELD_L_SHORTMESSAGE,
    // self::FIELD_L_LONGMESSAGE,
   ); // Array

  /* the defaults are from sample code */
  private $apiEndpoint = self::API_ENDPOINT;
  private $apiUserName = 'platfo_1255077030_biz_api1.gmail.com';
  private $apiPassword = '1255077037';
  private $apiSignature = 'Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf';
  private $apiVersion = '92.0';
  private $useProxy = false;
  private $proxyHost = '127.0.0.1';
  private $proxyPort = '808';
  private $subject = '';

  private $authToken;
  private $authSignature;
  private $authTimestamp;
  private $authMode;
  private $caCertFilePath;
  private $lastRequestParams;

  public function __construct($apiUserName, $apiPassword, $apiSignature,
      $apiVersion=self::DEFAULT_API_VERSION, $useProxy=false, $proxyHost=null,
      $proxyPort=null, $subject='') {
    $this->apiUserName = $apiUserName;
    $this->apiPassword = $apiPassword;
    $this->apiSignature = $apiSignature;
    $this->apiVersion = $apiVersion;
    $this->useProxy = $useProxy;
    $this->proxyHost = $proxyHost;
    $this->proxyPort = $proxyPort;
    $this->subject = $subject;
    $this->caCertFilePath = dirname(__FILE__).self::DEFAULT_SSL_CERT_FILE_NAME;
    $this->lastRequestParams = null;
  } // __construct

  public function setPermssionParam($authToken, $authSignature,
      $authTimestamp) {
    $this->authToken = $authToken;
    $this->authSignature = $authSignature;
    $this->authTimestamp = $authTimestamp;
  } // setPermissionParam

  public function getLastRequestParams() {
    return $this->lastRequestParams;
  } // getLastRequestParams

  public static function generateUrl($cmd, $token, $productionMode, $checkOutOnPaypal) {
    $uParam = new CecUrlParam();
    if (!is_null($cmd))
      $uParam->appendKeyValuePair(self::PARAM_CMD, $cmd);
    if (!is_null($token))
      $uParam->appendKeyValuePair(self::PARAM_TOKEN, $token);
    if ($checkOutOnPaypal)
      $uParam->appendKeyValuePair(self::PARAM_USER_ACTION,
        self::USER_ACTION_COMMIT);
    if ($productionMode) {
      $url = self::URL_PRODUCTION;
    } else {
      $url = self::URL_SANDBOX;
    } // else
    return $url.'&'.$uParam->toString();
  } // generateUrl

  private function getAuthMode() {
    if (isset($this->authMode)) return $authMode;
    if ((!empty($this->apiUserName)) &&
       (!empty($this->apiPassword)) &&
       (!empty($this->apiSignature)) &&
       (!empty($this->subject))) {
      return self::AUTH_MODE_THIRD_PARTY;
    } // if
    if ((!empty($this->apiUserName)) &&
        (!empty($this->apiPassword)) &&
        (!empty($this->apiSignature))) {
      return self::AUTH_MODE_3TOKEN;
    } // if
    if (!empty($this->authToken) &&
        !empty($this->authSignature) &&
        !empty($this->authTimestamp)) {
      return self::AUTH_MODE_PERMISSION;
    } // if
    if(!empty($subject)) {
      return self::AUTH_MODE_FIRST_PARTY;
    } // if
  } // getAuthMode

  private function generateNvpHeader() {
    $authMode = $this->getAuthMode();
    $uParam = new CecUrlParam();
    switch($authMode) {
      case self::AUTH_MODE_3TOKEN: 
        $uParam->appendKeyValuePair(self::PARAM_PWD, $this->apiPassword);
        $uParam->appendKeyValuePair(self::PARAM_USER, $this->apiUserName);
        $uParam->appendKeyValuePair(self::PARAM_SIGNATURE, $this->apiSignature);
        break;
      case self::AUTH_MODE_FIRST_PARTY:
        $uParam->appendKeyValuePair(self::PARAM_SUBJECT, $this->subject);
        break;
      case self::AUTH_MODE_THIRD_PARTY:
        $uParam->appendKeyValuePair(self::PARAM_PWD, $this->apiPassword);
        $uParam->appendKeyValuePair(self::PARAM_USER, $this->apiUserName);
        $uParam->appendKeyValuePair(self::PARAM_SIGNATURE, $this->apiSignature);
        $uParam->appendKeyValuePair(self::PARAM_SUBJECT, $this->subject);
        break;
      case self::AUTH_MODE_PERMISSION:
        $uParam->appendKeyValuePair(self::PARAM_AUTH_TOKEN, $this->authToken);
        $uParam->appendKeyValuePair(self::PARAM_SIGNATURE, $this->authSignature);
        $uParam->appendKeyValuePair(self::PARAM_TIMESTAMP, $this->authTimestamp);
        break;
    } // switch
    return $uParam;
  } // generateNvpHeader

  private function deformatNvp($nvpstr) {
    $intial=0;
    $nvpArray = array();
    while(strlen($nvpstr)){
      //postion of Key
      $keypos= strpos($nvpstr,'=');
      //position of value
      $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
  
      /*getting the Key and Value values and storing in a Associative Array*/
      $keyval=substr($nvpstr,$intial,$keypos);
      $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
      //decoding the respose
      $nvpArray[urldecode($keyval)] =urldecode( $valval);
      $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
     } // while
    return $nvpArray;
  } // deformatNvp

  /**
    * hash_call: Function to perform the API call to PayPal using API signature
    * @methodName is name of API  method.
    * @nvpParamArray is nvp string.
    * returns an associtive array containing the response from the server.
  */
  protected function hashCall($methodName, $nvpParamArray, &$curlError) {
    $curlError = null;
    $nvpHeader = $this->generateNvpHeader();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->apiEndpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    //turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    //in case of permission APIs send headers as HTTPheders
    if(!empty($this->authToken) &&
       !empty($this->authSignature) &&
       !empty($this->authTimestamp)) {
      $headers_array[] = "X-PP-AUTHORIZATION: ".$nvpHeader->toString();
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_array);
      curl_setopt($ch, CURLOPT_HEADER, false);
    } else {
      $nvpHeader->merge($nvpParamArray);
    }
    // if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
    // Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
    if($this->useProxy)
      curl_setopt ($ch, CURLOPT_PROXY, $this->proxyHost.":".$this->proxyPort); 

    //check if version is included in $nvpHeader else include the version.
    $ver = $nvpHeader->getValue(self::PARAM_VERSION);
    if (empty($ver)) {
      $nvpHeader->appendKeyValuePair(self::PARAM_VERSION, $this->apiVersion);
    } // if
    $nvpHeader->appendKeyValuePair(self::PARAM_METHOD, $methodName);
    $nvpRequest= $nvpHeader->toString();
    $this->lastRequestParams = $nvpRequest;
CecLogger::logDebug(get_class($this)."hashCall.nvpRequest=".$nvpRequest);

    //setting the nvpRequest as POST FIELD to curl
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpRequest);

    //getting response from server
    $response = curl_exec($ch);
    if (curl_errno($ch) == self::CURL_ERRNO_SSL_CERT) { 
      curl_setopt($ch, CURLOPT_CAINFO, $this->caCertFilePath);
      $response = curl_exec($ch);
    } // if

    //convrting NVPResponse to an Associative Array
    $nvpResArray=$this->deformatNvp($response);
    if (curl_errno($ch)) {
      // moving to display page to display curl errors
      $curlError = Array(
        'curl_error_no' => curl_errno($ch),
        'curl_error_msg' => curl_error($ch),
      ); // Array
    } else {
      // closing the curl
      curl_close($ch);
    } // else
//CecLogger::logDebug($nvpResArray);
    return $nvpResArray;
  } // hashCall

  public static function isResponseSuccessful($responseArray) {
    if (empty($responseArray)) return false;
    if (empty($responseArray[self::FIELD_ACK])) return false;
    $ackResponse = strtoupper($responseArray[self::FIELD_ACK]);
    return (($ackResponse == self::ACK_SUCCESS) ||
            ($ackResponse == self::ACK_SUCCESS_WITH_WARNING));
  } // isResponseSuccessful

  public static function getResponseError($responseArray, $index='0') {
    if (empty($responseArray)) return null;
    if (self::isResponseSuccessful($responseArray)) {
      return null;
    } // if
    $parts = Array();
    foreach(self::$ERROR_CODE_FIELDS as $fieldName) {
      $key = $fieldName.$index;
      if (!empty($responseArray[$key])) {
        $value = urldecode($responseArray[$key]);
      } else {
        $value = null;
      } // if
      $parts[] = $value;
    } // foreach
    $str = vsprintf("%s %s: %s", $parts);
    $shortMsgField = self::FIELD_L_SHORTMESSAGE.$index;
    $longMsgField = self::FIELD_L_LONGMESSAGE.$index;
    if (isset($responseArray[$longMsgField])) {
      if (!isset($responseArray[$shortMsgField]) ||
          ($responseArray[$longMsgField] != $responseArray[$shortMsgField])) {
        $str .= "\n".urldecode($responseArray[$longMsgField]);
      } // if
    } // if
    return $str;
  } // getResponseError

  protected static function splitKeyWithIndex($key) {
    $subIndex = substr($key, -1);
    if (!is_numeric($subIndex)) return Array(null, null);
    $subKey = substr($key, 0, strlen($key)-1);
    return Array($subKey, intval($subIndex));
  } // splitKeyWithIndex

  /* parse FIELD_XXX_[integer] or FIELD_XXX_[integer]_YYY_[integer] */
  protected static function parseIndexedKey(&$respArray, $keyRoot, $singleLevel) {
    $objArray = Array();
    if (!is_array($respArray)) {
      return null;
    } // if
    foreach($respArray as $key => $value) {
      $keyLen = strlen($keyRoot);
      if (substr($key, 0, $keyLen) == $keyRoot) {
        $subKeyParts = explode(self::SEP_FIELD, substr($key, $keyLen));
        $indexFound = false;
        foreach($subKeyParts as $part) {
          if (is_null($part) || ($part == '')) continue;
          if (!$indexFound) {
            $index = intval($part);
            $indexFound = true;
            if (!isset($objArray[$index])) {
              $objArray[$index] = Array();
            } // if
          } else {
            if ($singleLevel) {
              $objArray[$index][$part] = $value;
            } else {
              list($subKey, $subIndex) = self::splitKeyWithIndex($part);
              if (is_null($subKey)) continue;
              if (!isset($objArray[$index][$subIndex])) {
                $objArray[$index][$subIndex] = Array();
              } // if
              $objArray[$index][$subIndex][$subKey] = $value;
            } // else
            break;
          } // else
        } // foreach
        unset($respArray[$key]);
      } // if
    } // foreach
    return $objArray;
  } // parseIndexedKey

  /* parse FIELD_XXX[integer] */
  protected static function parseSubscriptedKey(&$respArray) {
    if (empty($respArray)) return Array($respArray, null);
    if (!is_array($respArray)) {
      return null;
    } // if
    $objArray = Array();
    foreach($respArray as $key => $value) {
      $found = preg_match(self::PATTERN_INTEGER, $key, $matches,
        PREG_OFFSET_CAPTURE);
      if (!$found) continue;
      $firstMatch = current($matches);
      $subscript = $firstMatch[0];
      $offset = $firstMatch[1];
      $subkey = substr($key, 0, $offset);
      if (!isset($objArray[$subscript])) {
        $objArray[$subscript] = Array();
      } // if
      $objArray[$subscript][$subkey] = $value;
    } // foreach
    return $objArray;
  } // parseSubscriptedKey

  private static function formatPrice($price) {
    if (is_null($price)) return null;
    /* do not use thousand separator */
    return @number_format(doubleVal($price), 2, '.', '');
  } // formatPrice

  protected function appendPrice($uParam, $paramName, $var) {
    if (!isset($var)) return;
    $uParam->appendKeyValuePair($paramName, self::formatPrice($var));
  } // appendPrice

} // CecPaypal
