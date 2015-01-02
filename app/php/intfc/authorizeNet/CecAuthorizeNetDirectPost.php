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
$rDir = '';
require_once($rDir.'cec/php/helpers/CecHelperHttp.php');
require_once($rDir.'cec/php/intfc/authorizeNet/CecAuthorizeNet.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'php/cec/CecLogger.php');

class CecAuthorizeNetDirectPost extends CecAuthorizeNet {

  const PARAM_X_AMOUNT = 'x_amount';
  const PARAM_X_DESCRIPTION = 'x_description';
  const PARAM_X_DUTY = 'x_duty';
  const PARAM_X_FREIGHT = 'x_freight';
  const PARAM_X_TAX_EXEMPT = 'x_tax_exempt';
  const PARAM_X_TAX = 'x_tax';
  const PARAM_X_FP_SEQUENCE = 'x_fp_sequence';
  const PARAM_X_FP_HASH = 'x_fp_hash';
  const PARAM_X_FP_TIMESTAMP = 'x_fp_timestamp';
  const PARAM_X_RELAY_RESPONSE = 'x_relay_response';
  const PARAM_X_RELAY_URL = 'x_relay_url';
  const PARAM_X_LOGIN = 'x_login';
  const PARAM_X_DELIM_DATA = 'x_delim_data';
  const PARAM_X_DELIM_CHAR = 'x_delim_char';
  const PARAM_X_ENCAP_CHAR = 'x_encap_char';
  const PARAM_X_VERSION = 'x_version';
  const PARAM_X_METHOD = 'x_method';
  const PARAM_X_DUPLICATE_WINDOW = 'x_duplicate_window';

  /* credit card and billing */
  const PARAM_X_CARD_TYPE = 'x_card_type';
  const PARAM_X_CARD_NUM = 'x_card_num';
  const PARAM_X_EXP_DATE = 'x_exp_date';
  const PARAM_X_CARD_CODE = 'x_card_code';
  const PARAM_X_FIRST_NAME = 'x_first_name';
  const PARAM_X_LAST_NAME = 'x_last_name';
  const PARAM_X_COMPANY = 'x_company';
  const PARAM_X_ADDRESS = 'x_address';
  const PARAM_X_CITY = 'x_city';
  const PARAM_X_STATE = 'x_state';
  const PARAM_X_ZIP = 'x_zip';
  const PARAM_X_COUNTRY = 'x_country';
  const PARAM_X_PHONE = 'x_phone';
  const PARAM_X_FAX = 'x_fax';
  const PARAM_X_EMAIL = 'x_email';
  const PARAM_X_PO_NUM = 'x_po_num';
  const PARAM_X_INVOICE_NUM = 'x_invoice_num';
  const PARAM_X_CUST_ID = 'x_cust_id';
  const PARAM_X_CUSTOMER_IP = 'x_customer_ip';

  /* shipping */
  const PARAM_X_SHIP_TO_FIRST_NAME = 'x_ship_to_first_name';
  const PARAM_X_SHIP_TO_LAST_NAME = 'x_ship_to_last_name';
  const PARAM_X_SHIP_TO_COMPANY = 'x_ship_to_company';
  const PARAM_X_SHIP_TO_ADDRESS = 'x_ship_to_address';
  const PARAM_X_SHIP_TO_CITY = 'x_ship_to_city';
  const PARAM_X_SHIP_TO_STATE = 'x_ship_to_state';
  const PARAM_X_SHIP_TO_ZIP = 'x_ship_to_zip';
  const PARAM_X_SHIP_TO_COUNTRY = 'x_ship_to_country';
  const PARAM_X_RESPONSE_FORMAT = 'x_response_format';

  const FIELD_X_RESPONSE_CODE = 'x_response_code';
  const FIELD_X_RESPONSE_REASON_CODE = 'x_response_reason_code';
  const FIELD_X_RESPONSE_REASON_TEXT = 'x_response_reason_text';
  const FIELD_X_AVS_CODE = 'x_avs_code';
  const FIELD_X_AUTH_CODE = 'x_auth_code';
  const FIELD_X_TRANS_ID = 'x_trans_id';
  const FIELD_X_METHOD = 'x_method';
  const FIELD_X_CARD_TYPE = 'x_card_type';
  const FIELD_X_ACCOUNT_NUMBER = 'x_account_number';
  const FIELD_X_FIRST_NAME = 'x_first_name';
  const FIELD_X_LAST_NAME = 'x_last_name';
  const FIELD_X_COMPANY = 'x_company';
  const FIELD_X_ADDRESS = 'x_address';
  const FIELD_X_CITY = 'x_city';
  const FIELD_X_STATE = 'x_state';
  const FIELD_X_ZIP = 'x_zip';
  const FIELD_X_COUNTRY = 'x_country';
  const FIELD_X_PHONE = 'x_phone';
  const FIELD_X_FAX = 'x_fax';
  const FIELD_X_EMAIL = 'x_email';
  const FIELD_X_INVOICE_NUM = 'x_invoice_num';
  const FIELD_X_DESCRIPTION = 'x_description';
  const FIELD_X_TYPE = 'x_type';
  const FIELD_X_CUST_ID = 'x_cust_id';
  const FIELD_X_SHIP_TO_FIRST_NAME = 'x_ship_to_first_name';
  const FIELD_X_SHIP_TO_LAST_NAME = 'x_ship_to_last_name';
  const FIELD_X_SHIP_TO_COMPANY = 'x_ship_to_company';
  const FIELD_X_SHIP_TO_ADDRESS = 'x_ship_to_address';
  const FIELD_X_SHIP_TO_CITY = 'x_ship_to_city';
  const FIELD_X_SHIP_TO_STATE = 'x_ship_to_state';
  const FIELD_X_SHIP_TO_ZIP = 'x_ship_to_zip';
  const FIELD_X_SHIP_TO_COUNTRY = 'x_ship_to_country';
  const FIELD_X_AMOUNT = 'x_amount';
  const FIELD_X_TAX = 'x_tax';
  const FIELD_X_DUTY = 'x_duty';
  const FIELD_X_FREIGHT = 'x_freight';
  const FIELD_X_TAX_EXEMPT = 'x_tax_exempt';
  const FIELD_X_PO_NUM = 'x_po_num';
  const FIELD_X_MD5_HASH = 'x_MD5_Hash';
  const FIELD_X_CVV_2_RESP_CODE = 'x_cvv2_resp_code';
  const FIELD_X_CAVV_RESPONSE = 'x_cavv_response';
  const FIELD_X_TEST_REQUEST = 'x_test_request';

  const FIELD_APPROVED = 'approved';
  const FIELD_DECLINED = 'declined';
  const FIELD_ERROR = 'error';
  const FIELD_HELD = 'held';
  const FIELD_RESPONSE_CODE = 'response_code';
  const FIELD_RESPONSE_SUBCODE = 'response_subcode';
  const FIELD_RESPONSE_REASON_CODE = 'response_reason_code';
  const FIELD_RESPONSE_REASON_TEXT = 'response_reason_text';
  const FIELD_AUTHORIZATION_CODE = 'authorization_code';
  const FIELD_AVS_RESPONSE = 'avs_response';
  const FIELD_TRANSACTION_ID = 'transaction_id';
  const FIELD_INVOICE_NUMBER = 'invoice_number';
  const FIELD_DESCRIPTION = 'description';
  const FIELD_AMOUNT = 'amount';
  const FIELD_METHOD = 'method';
  const FIELD_TRANSACTION_TYPE = 'transaction_type';
  const FIELD_CUSTOMER_ID = 'customer_id';
  const FIELD_FIRST_NAME = 'first_name';
  const FIELD_LAST_NAME = 'last_name';
  const FIELD_COMPANY = 'company';
  const FIELD_ADDRESS = 'address';
  const FIELD_CITY = 'city';
  const FIELD_STATE = 'state';
  const FIELD_ZIP_CODE = 'zip_code';
  const FIELD_COUNTRY = 'country';
  const FIELD_PHONE = 'phone';
  const FIELD_FAX = 'fax';
  const FIELD_EMAIL_ADDRESS = 'email_address';
  const FIELD_SHIP_TO_FIRST_NAME = 'ship_to_first_name';
  const FIELD_SHIP_TO_LAST_NAME = 'ship_to_last_name';
  const FIELD_SHIP_TO_COMPANY = 'ship_to_company';
  const FIELD_SHIP_TO_ADDRESS = 'ship_to_address';
  const FIELD_SHIP_TO_CITY = 'ship_to_city';
  const FIELD_SHIP_TO_STATE = 'ship_to_state';
  const FIELD_SHIP_TO_ZIP_CODE = 'ship_to_zip_code';
  const FIELD_SHIP_TO_COUNTRY = 'ship_to_country';
  const FIELD_TAX = 'tax';
  const FIELD_DUTY = 'duty';
  const FIELD_FREIGHT = 'freight';
  const FIELD_TAX_EXEMPT = 'tax_exempt';
  const FIELD_PURCHASE_ORDER_NUMBER = 'purchase_order_number';
  const FIELD_MD_5_HASH = 'md5_hash';
  const FIELD_CARD_CODE_RESPONSE = 'card_code_response';
  const FIELD_CAVV_RESPONSE = 'cavv_response';
  const FIELD_ACCOUNT_NUMBER = 'account_number';
  const FIELD_CARD_TYPE = 'card_type';
  const FIELD_SPLIT_TENDER_ID = 'split_tender_id';
  const FIELD_REQUESTED_AMOUNT = 'requested_amount';
  const FIELD_BALANCE_ON_CARD = 'balance_on_card';
  const FIELD_RESPONSE = 'response';
  const FIELD_ERROR_MESSAGE = 'error_message';

  const X_CARD_TYPE_VISA = 'Visa';
  const X_CARD_TYPE_MASTER_CARD = 'MasterCard';
  const X_CARD_TYPE_AMEX = 'American Express';
  const X_CARD_TYPE_DISCOVER = 'Discover';
  const X_CARD_TYPE_JCB = 'JCB';
  const X_CARD_TYPE_DINERS_CLUB = 'Diners Club';

  const X_METHOD_CC = 'CC';
  const X_METHOD_ECHECK = 'ECHECK';

  const X_TYPE_AUTH_CAPTURE = 'auth_capture';
  const X_TYPE_AUTH_ONLY = 'auth_only';

  const X_RESPONSE_FORMAT_XML = 0;
  const X_RESPONSE_FORMAT_CSV = 1;

  const DEFAULT_DUPLICATE_WINDOW = 3600; // one hour
  const SPRINTF_EXP_DATE = '%02d/%02d';
  const TMP_DIRECTORY = '/tmp';
  const TMP_FILE_COOKIE_PREFIX = 'anc';
  const TMP_FILE_ERRFILE_PREFIX = 'anf';
  const TIME_OUT = 10;
  const CONNECTION_TIME_OUT = 60;
  const DELIM_CHAR = '|';
  const ENCAP_CHAR = '"';

  const ANONYMOUS_CLEAR_TEXT_LEN = 4;
  const ANONYMOUS_CHAR = 'x';

  private static $defaultAnonymizePolicy = Array(
    self::PARAM_X_EXP_DATE => true,
    self::PARAM_X_CARD_CODE => true,
    self::PARAM_X_CARD_NUM => false,
  );

  private static $fieldLabelMap = Array(
    self::FIELD_X_RESPONSE_CODE => "response code",
    self::FIELD_X_RESPONSE_REASON_CODE => "response reason code",
    self::FIELD_X_RESPONSE_REASON_TEXT => "response reason",
    self::FIELD_X_AVS_CODE => "AVS code",
    self::FIELD_X_AUTH_CODE => "authorization code",
    self::FIELD_X_TRANS_ID => "transaction ID",
    self::FIELD_X_METHOD => "method",
    self::FIELD_X_CARD_TYPE => "card type",
    self::FIELD_X_ACCOUNT_NUMBER => "account number",
    self::FIELD_X_FIRST_NAME => "first name",
    self::FIELD_X_LAST_NAME => "last name",
    self::FIELD_X_COMPANY => "company",
    self::FIELD_X_ADDRESS => "address",
    self::FIELD_X_CITY => "city",
    self::FIELD_X_STATE => "state",
    self::FIELD_X_ZIP => "zip",
    self::FIELD_X_COUNTRY => "country",
    self::FIELD_X_PHONE => "phone",
    self::FIELD_X_FAX => "fax",
    self::FIELD_X_EMAIL => "email",
    self::FIELD_X_INVOICE_NUM => "invoice number",
    self::FIELD_X_DESCRIPTION => "description",
    self::FIELD_X_TYPE => "type",
    self::FIELD_X_CUST_ID => "customer id",
    self::FIELD_X_SHIP_TO_FIRST_NAME => "ship to first name",
    self::FIELD_X_SHIP_TO_LAST_NAME => "ship to last name",
    self::FIELD_X_SHIP_TO_COMPANY => "ship to company",
    self::FIELD_X_SHIP_TO_ADDRESS => "ship to address",
    self::FIELD_X_SHIP_TO_CITY => "ship to city",
    self::FIELD_X_SHIP_TO_STATE => "ship to state",
    self::FIELD_X_SHIP_TO_ZIP => "ship to zip",
    self::FIELD_X_SHIP_TO_COUNTRY => "ship to country",
    self::FIELD_X_AMOUNT => "amount",
    self::FIELD_X_TAX => "tax",
    self::FIELD_X_DUTY => "duty",
    self::FIELD_X_FREIGHT => "freight",
    self::FIELD_X_TAX_EXEMPT => "tax exempt",
    self::FIELD_X_PO_NUM => "purchase order num",
    self::FIELD_X_MD5_HASH => "MD5 Hash",
    self::FIELD_X_CVV_2_RESP_CODE => "CVV2 respose code",
    self::FIELD_X_CAVV_RESPONSE => "CAVV response",
    self::FIELD_X_TEST_REQUEST => "test request",
  ); // Array

  private static $responseCodeLabel = Array(
    AuthorizeNetSIM::APPROVED => "approved",
    AuthorizeNetSIM::DECLINED => "declined",
    AuthorizeNetSIM::ERROR => "error",
    AuthorizeNetSIM::HELD => "held",
  ); // Array

  private static $cardCodeResponseLabel = Array(
    'N' => "Card code value does not match",
    'P' => "Card code value not processed",
    'S' => "Card code value should be on card but not indicated",
    'U' => "Card issuer (not cardholder) is not certified and/or has not provided encryption key"
  );

  private static $transactionStatusFields = Array(
    self::FIELD_APPROVED => "approved",
    self::FIELD_DECLINED => "declined",
    self::FIELD_ERROR => "error",
    self::FIELD_HELD => "held",
  ); // Array

  private $lastRequestParams;
  private $lastAnonymizedRequestParams;
  private $anonymizePolicy;

  public function __construct($loginId, $transactionKey, $md5Setting,
      $productionMode, $anonymizePolicy=null) {
    parent::__construct($loginId, $transactionKey, $md5Setting, $productionMode);
    $this->anonymizePolicy = self::$defaultAnonymizePolicy;
    if (!empty($anonymizePolicy)) {
      foreach($anonymizePolicy as $key => $value) {
        $this->anonymizePolicy[$key] = $value;
      } // foreach
    } // else
  } // __construct

  public static function formatExpirationDate($month, $year) {
    return sprintf(self::SPRINTF_EXP_DATE, intval($month), (intval($year)%100));
  } // formatExpirationDate

  private static function cleanseCardNum(&$paramBlock) {
    if (!isset($paramBlock[self::PARAM_X_CARD_NUM])) return;
    $cardNum = $paramBlock[self::PARAM_X_CARD_NUM];
    if (ctype_digit($cardNum)) return;
    $newNum = "";
    $cardNumLen = strlen($cardNum);
    for($i=0; $i<$cardNumLen; $i++) {
      $ch = $cardNum[$i];
      if (ctype_digit($ch)) {
        $newNum .= $ch;
      } // if
    } // foreach
    $paramBlock[self::PARAM_X_CARD_NUM] = $newNum;
  } // cleanseCardNum

  public function execute($paramBlock, $amount, $freight, $taxExempt, $tax, $duty,
      $fp_sequence, $relayUrl, $xType=self::X_TYPE_AUTH_CAPTURE,
      $duplicateWindow=self::DEFAULT_DUPLICATE_WINDOW) {
    $amount = self::formatPrice($amount);
    $tax = self::formatPrice($tax);
    $duty = self::formatPrice($duty);
    $freight = self::formatPrice($freight);
    $timeNow = time();
    $fp = AuthorizeNetDPM::getFingerprint($this->loginId,
      $this->transactionKey, $amount, $fp_sequence, $timeNow);

    $paramBlock[self::PARAM_X_AMOUNT] = $amount;
    $paramBlock[self::PARAM_X_FREIGHT] = $freight;
    $paramBlock[self::PARAM_X_TAX_EXEMPT] = $taxExempt;
    $paramBlock[self::PARAM_X_TAX] = $tax;
    $paramBlock[self::PARAM_X_DUTY] = $duty;
    $paramBlock[self::PARAM_X_FP_SEQUENCE] = $fp_sequence;
    $paramBlock[self::PARAM_X_FP_HASH] = $fp;
    $paramBlock[self::PARAM_X_FP_TIMESTAMP] = $timeNow;
    $paramBlock[self::PARAM_X_DUPLICATE_WINDOW] = $duplicateWindow;
    $paramBlock[self::PARAM_X_LOGIN] = $this->loginId;
    $paramBlock[self::PARAM_X_VERSION] = self::API_VERSION;
    $paramBlock[self::PARAM_X_RESPONSE_FORMAT] = self::X_RESPONSE_FORMAT_CSV;

    if (!empty($relayUrl)) {
      $relayResponse = true;
      $paramBlock[self::PARAM_X_RELAY_RESPONSE] = 'true';
      $paramBlock[self::PARAM_X_DELIM_DATA] = 'false';
      $paramBlock[self::PARAM_X_RELAY_URL] = $relayUrl;
    } else {
      $relayResponse = false;
      $paramBlock[self::PARAM_X_RELAY_RESPONSE] = 'false';
      $paramBlock[self::PARAM_X_DELIM_DATA] = 'true';
      $paramBlock[self::PARAM_X_DELIM_CHAR] = self::DELIM_CHAR;
      $paramBlock[self::PARAM_X_ENCAP_CHAR] = self::ENCAP_CHAR;
    } // else

    if ($this->productionMode) {
      $url = AuthorizeNetDPM::LIVE_URL;
    } else {
      $url = AuthorizeNetDPM::SANDBOX_URL;
    } // else
    return $this->_executePost($url, $paramBlock, $relayResponse);
  } // execute

  static private function formatHeaderAttributes($attributeValueArray) {
    $header2 = Array();
    foreach($attributeValueArray as $attr => $value) {
      if (is_integer($attr)) {
        $header2[] = $value;
      } else {
        if (is_array($value)) {
          $value = implode(', ',$value);
        } // if
        $header2[] = $attr.": ".$value;
      } // else
    } // foreach
    return($header2);
  } // formatHeaderAttributes

  static private function setHeaderAttributes($ch, $attributeValueArray) {
    $header = self::formatHeaderAttributes($attributeValueArray);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_HEADER, true);
  } // setHeaderAttributes

  private function copyOverErrorFile($errorFileHandle) {
      if ($errorFileHandle) {
        fseek($errorFileHandle, 0);
        $fstats = fstat($errorFileHandle);
        if ($fstats['size'] > 0) {
          $logDetails = trim(fread($errorFileHandle, $fstats['size']), "\0");
          /* empty the file */
          ftruncate($errorFileHandle, 0);
        } // if
      } // if
  } // copyOverErrorFile

  private function _execCurl($ch, $errorFileHandle) {
    $curlResult = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (!in_array($responseCode, Array(
        CecHelperHttp::HTTP_STATUS_OK,
       ))) {
      throw new Exception("Authorize.net failed with response code "
        .$responseCode);
    } // if

    $errorDetails = null;
    if ($errorFileHandle) {
        fseek($errorFileHandle, 0);
        $fstats = fstat($errorFileHandle);
        if ($fstats['size'] > 0) {
          $errorDetails = trim(fread($errorFileHandle, $fstats['size']), "\0");
          /* empty the file */
          ftruncate($errorFileHandle, 0);
        } // if
    } // if
    if ($curlResult === false) {
      $msg = "Authorize.net failed with error code ".curl_error($ch);
      if (!empty($errorDetails)) {
        $msg .= "\n".$errorDetails;
      } // if
      throw new Exception ($msg);
    } // if
    return $curlResult;
  } // _execCurl

  public function getLastRequestParams() {
    return $this->lastRequestParams;
  } // getLastRequestParams

  public function getLastAnonymizedRequestParams() {
    return $this->lastAnonymizedRequestParams;
  } // getLastAnonymizedRequestParams

  private static function anonymizeString($str,
      $clearCount=self::ANONYMOUS_CLEAR_TEXT_LEN) {
    if (empty($str)) return $str;
    $strLen = strlen($str);
    if ($strLen <= $clearCount) return $str;
    $xCount = $strLen - $clearCount;
    for ($i=0; $i<$xCount; $i++) {
      $str[$i] = self::ANONYMOUS_CHAR;
    } // for
    return $str;
  } // anonymizeString

  private function anonymizeRequestParams($uParam) {
    foreach($this->anonymizePolicy as $key => $action) {
      if ($action === true) {
        $uParam->removeKey($key);
      } else {
        $value = $uParam->getValue($key);
        if (!empty($value)) {
          if (is_numeric($action)) {
            $newValue = $this->anonymizeString($value, intval($action));
          } else {
            $newValue = $this->anonymizeString($value);
          } // else
          $uParam->appendKeyValuePair($key, $newValue);
        } // if
      } // else
    } // foreach
    return $uParam->toString();
  } // anonymizeRequestParams

  private function _executePost($url, $paramBlock, $relayResponse) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValueArray($paramBlock);
    $this->lastRequestParams = $uParam->toString();
    $this->lastAnonymizedRequestParams = $this->anonymizeRequestParams($uParam);

    $ch = curl_init();
    $header = Array();
    $header[CecHelperHttp::HTTP_HEADER_ATTR_CONTENT_TYPE] =
      CecHelperHttp::HTTP_HEADER_VALUE_FORM_URLENCODED;
    $header[CecHelperHttp::HTTP_HEADER_ATTR_ACCEPT] =
      CecHelperHttp::HTTP_HEADER_VALUE_ACCEPT_DEFAULT;
    $header[CecHelperHttp::HTTP_HEADER_ATTR_ACCEPT_LANGUAGE]=
      CecHelperHttp::HTTP_HEADER_VALUE_ACCEPT_LANGUAGE_EN_US;
    $header[CecHelperHttp::HTTP_HEADER_ATTR_ACCEPT_ENCODING] =
      CecHelperHttp::HTTP_HEADER_VALUE_ACCEPT_ENCODING_DEFLATE;
    $header[CecHelperHttp::HTTP_HEADER_ATTR_CONTENT_LENGTH] =
      strlen($this->lastRequestParams);

    self::setHeaderAttributes($ch, $header);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->lastRequestParams);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_USERAGENT, CecHelperHttp::HTTP_AGENT_MOZILLA_5);

    $errorFile = tempnam(self::TMP_DIRECTORY,
      self::TMP_FILE_ERRFILE_PREFIX);
    $errorFileHandle = fopen($errorFile, 'w');
    curl_setopt($ch, CURLOPT_STDERR, $errorFileHandle);

    $curlResult = $this->_execCurl($ch, $errorFileHandle);
    if (!$curlResult) {
      return $curlResult;
    } // if
    $pos = strpos($curlResult, "\r\n\r\n");
    if ($pos === false) return $curlResult;
    $responseBody = trim(substr($curlResult, $pos));
    if (!$relayResponse) {
      $responseArray = null;
      $responseArray = (Array) new AuthorizeNetAIM_Response($responseBody,
        $paramBlock[self::PARAM_X_DELIM_CHAR],
        $paramBlock[self::PARAM_X_ENCAP_CHAR],
        Array());
/*
      $responseArray = @simplexml_load_string($responseBody);
      $delimChar = $paramBlock[self::PARAM_X_DELIM_CHAR];
      $responseArray = explode($delimChar, $responseBody);
      return self::print_r_reverse($responseBody);
*/
      return $responseArray;
    } // if
  } // _executePost

  private static function print_r_reverse($in) {
    /* this function is from php.net */
    $lines = explode("\n", trim($in));
    if (trim($lines[0]) != 'Array') {
      // bottomed out to something that isn't an array
      return $in;
    } else {
      // this is an array, lets parse it
      if (preg_match("/(\s{5,})\(/", $lines[1], $match)) {
        // this is a tested array/recursive call to this function
        // take a set of spaces off the beginning
        $spaces = $match[1];
        $spaces_length = strlen($spaces);
        $lines_total = count($lines);
        for ($i = 0; $i < $lines_total; $i++) {
          if (substr($lines[$i], 0, $spaces_length) == $spaces) {
            $lines[$i] = substr($lines[$i], $spaces_length);
          } // if
        } // for
      } // if
      array_shift($lines); // Array
      array_shift($lines); // (
      array_pop($lines); // )
      $in = implode("\n", $lines);
      // make sure we only match stuff with 4 preceding spaces
      // (stuff for this array and not a nested one)
      preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches,
        PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
      $pos = array();
      $previous_key = '';
      $in_length = strlen($in);
      // store the following in $pos:
      // array with key = key of the parsed array's item
      // value = array(start position in $in, $end position in $in)
      foreach ($matches as $match) {
        $key = $match[1][0];
        $start = $match[0][1] + strlen($match[0][0]);
        $pos[$key] = array($start, $in_length);
        if ($previous_key != '') $pos[$previous_key][1] = $match[0][1] - 1;
        $previous_key = $key;
      } // foreach
      $ret = array();
      foreach ($pos as $key => $where) {
        // recursively see if the parsed out value is an array too
        $ret[$key] = self::print_r_reverse(
        substr($in, $where[0], $where[1] - $where[0]));
      } // foreach
      return $ret;
    } // else
  } // print_r_reverse

  public function relayResponse() {
    /* the parameters must be in the $_POST variable */
    $response = new AuthorizeNetSIM($this->loginId, $this->md5Setting);
    if ($response->isAuthorizeNet()) {
      $this->recordResponse($response);
      return $response;
    } // if
    return null;
  } // relayResponse

  public static function isResponseSuccessful($resp) {
    if (empty($resp)) return false;
    if (isset($resp[self::FIELD_ERROR_MESSAGE])) return false;
    return true;
  } // isResponseSuccessful

  public static function getResponseError($resp) {
    if (empty($resp)) return null;
    if (!isset($resp[self::FIELD_ERROR_MESSAGE])) return null;
    return $resp[self::FIELD_ERROR_MESSAGE];
  } // getResponseError

  public static function getCardCodeResponseLabel($ccr) {
    if (!isset(self::$cardCodeResponseLabel[$ccr])) return $ccr;
    return self::$cardCodeResponseLabel[$ccr];
  } // getCardCodeResponseLabel

  public static function getTransactionStatusLabel($resp) {
    if (!is_array($resp)) return null;
    $stsArray = Array();
    foreach(self::$transactionStatusFields as $key => $label) {
      if (!isset($resp[$key])) continue;
      if ($resp[$key]==1) {
        $stsArray[] = $label;
      } // if
    } // foreach
    return implode(" ", $stsArray);
  } // getTransactionStatusLabel

} // CecAuthorizeNetDirectPost
