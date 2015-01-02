<?PHP
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
/* CecOpensslUtil.php */
$rDir = '';
require_once($rDir.'cec/php/CecAppLogger.php');
require_once($rDir.'cec/php/utils/CecCliUtil.php');
require_once($rDir.'cec/php/utils/CecSystemUtil.php');
require_once($rDir.'cec/php/utils/CecUtil.php');
require_once($rDir.'cec/php/utils/CecOpensslConfig.php');

class CecOpensslUtil {

  const SIGNED_CERT_VALIDITY_DAYS = 3650; /* 10 years */
  const CLIENT_CERT_SECTION_PREFIX_SUBJECT = 'SSL_CLIENT_S_DN';
  const CLIENT_CERT_SECTION_PREFIX_ISSUER = 'SSL_CLIENT_I_DN';
  const DN_PATH_SECTION_SPRINTF = '/%s=%s';
  const APACHE_CLIENT_CERT_VAR_NAME_SPRINTF = '%s_%s';
  const OPENSSL_SECTION_SUBJECT = 'subject';
  const OPENSSL_SECTION_ISSUER = 'issuer';

  const CMD_CA_SIGN_CSR_SPRINTF = '%s ca -batch -notext -config %s -policy policy_anything -cert %s -keyfile %s -key %s -in %s -out %s 2>/tmp/openssl.err';
  const CMD_PKCS7_PRINT_CERTS_SPRINTF = '%s pkcs7 -in %s -print_certs -out %s';
  const CMD_CRL2PKCS7_SPRINTF = '%s crl2pkcs7 -nocrl -certfile %s -certfile %s -out %s';
  const CMD_X509_INFORM_SPRINTF = '%s x509 -inform PEM -text -in %s -out %s';
  const CMD_REQ_TEXT_SPRINTF = '%s req -text -in %s -out %s';
  const TEMP_FILE_PREFIX = "op";

  const SPRINTF_SERIAL_NUMBER = '%x';
  const FIRST_SERIAL_NUMBER = '01';

  static private function getClientCertSubfieldNames() {
    /* order is important */
    return(Array(
      'C' => 'C',
      'ST' => 'ST',
      'L' => 'L',
      'O' => 'O',
      'OU' => 'OU',
      'CN' => 'CN',
      'emailAddress' => 'Email',
    )); // Array
  } // getClientCertSubfieldNames

  static public function getClientCert() {
    $sectionArray = Array(
      self::CLIENT_CERT_SECTION_PREFIX_SUBJECT,
      self::CLIENT_CERT_SECTION_PREFIX_ISSUER,
    ); // Array
    $subfieldNames = self::getClientCertSubfieldNames();
    $clientCert = Array();
    foreach($sectionArray as $section) {
      if (isset($_SERVER[$section])) {
        $clientCert[$section] = $_SERVER[$section];
      } // if
      foreach($subfieldNames as $opensslVar => $apacheVar) {
        $fullVarName = sprintf(self::APACHE_CLIENT_CERT_VAR_NAME_SPRINTF,
          $section, $apacheVar);
        if (isset($_SERVER[$fullVarName])) {
          $fullOpVarName = sprintf(self::APACHE_CLIENT_CERT_VAR_NAME_SPRINTF,
            $section, $opensslVar);
          $clientCert[$fullOpVarName] = $_SERVER[$fullVarName];
        } // if
      } // foreach
    } // foreach

    if (count($clientCert) == 0) {
      return(null);
    } else {
      return($clientCert);
    } // else
  } // getClientCert

  static public function isSslClientVerify() {
    if (!empty($_SERVER['SSL_CLIENT_VERIFY'])) {
      return(true);
    } // if
    return(false);
  } // isSslClientVerify

  static private function extractX509Section($opensslKeyValueArray, $section,
      $fieldNamePrefix) {
    if (!isset($opensslKeyValueArray[$section])) {
      return(null);
    } // if
    if (!is_array($opensslKeyValueArray[$section])) {
      return(null);
    } // if
    $valueArray = Array();
    /* format the full DN using the openssl short field names */
    $subfieldNames = self::getClientCertSubfieldNames();
    $dn = null;
    foreach($subfieldNames as $opensslKey => $apacheVar) {
      if (!isset($opensslKeyValueArray[$section][$opensslKey])) continue;
      $dn .= sprintf(self::DN_PATH_SECTION_SPRINTF, $opensslKey,
        $opensslKeyValueArray[$section][$opensslKey]);
    } // foreach
    $valueArray[$fieldNamePrefix] = $dn;

    /* extract openssl fields and convert to Apache _SERVER variable names */
    foreach($opensslKeyValueArray[$section] as $opensslKey => $value) {
      /* convert between openssl and apache names */
      if (isset($opensslKeyNames[$opensslKey])) {
        $opensslKey = $opensslKeyNames[$opensslKey];
      } // if
      $apacheVarName = sprintf(self::APACHE_CLIENT_CERT_VAR_NAME_SPRINTF,
        $fieldNamePrefix, $opensslKey);
      $valueArray[$apacheVarName] = $value;
    } // foreach
    return($valueArray);
  } // extractX509Section

  static public function x509ToClientCert($x509cert) {
    if (empty($x509cert)) return(null);
    $opensslKeyValueArray = openssl_x509_parse($x509cert, true);
CecAppLogger::logVariable($opensslKeyValueArray, "opensslKeyValueArray");
    if (!$opensslKeyValueArray) {
CecAppLogger::logNotice("x509ToClientCert failed ".$x509cert);
      return(null);
    } // if
    $subjectArray = self::extractX509Section($opensslKeyValueArray,
      self::OPENSSL_SECTION_SUBJECT,
      self::CLIENT_CERT_SECTION_PREFIX_SUBJECT);
    $issuerArray = self::extractX509Section($opensslKeyValueArray,
      self::OPENSSL_SECTION_ISSUER,
      self::CLIENT_CERT_SECTION_PREFIX_ISSUER);
    $clientCert = array_merge($subjectArray, $issuerArray);

    /* other fields */
    $timeFieldArray = Array(
      'validFrom_time_t',
      'validTo_time_t',
    ); // Array
    foreach($timeFieldArray as $timeField) {
      if (isset($opensslKeyValueArray[$timeField])) {
        $clientCert[$timeField] = $opensslKeyValueArray[$timeField];
      } // if
    } // foreach

    if (count($clientCert) == 0) {
      return(null);
    } else {
      return($clientCert);
    } // else
  } // x509ToClientCert

  static private function _generateTempname() {
    return(CecUtil::generateTempnam(self::TEMP_FILE_PREFIX));
  } // _generateTempname

  static public function generateX509WithText($string) {
    $outfile = self::_generateTempname();
    $ch = openssl_x509_read($string);
    openssl_x509_export_to_file($ch, $outfile, false);
    $returnString = file_get_contents($outfile);
    unlink($outfile);
    return($returnString);
  } // generateX509WithText

  static public function generateCsrWithText($string) {
    $outfile = self::_generateTempname();
    $infile = self::_generateTempname();
    file_put_contents($infile, $string);
    $cmd = sprintf(self::CMD_REQ_TEXT_SPRINTF, CecSystemUtil::OPENSSL_BIN,
      $infile, $outfile);
    CecCliUtil::execCommandLineSync($cmd);
    $returnString = file_get_contents($outfile);
    unlink($infile);
    unlink($outfile);
    return($returnString);
  } // generateCsrWithText

  static public function generatePkcs7WithText($string) {
    $outfile = self::_generateTempname();
    $infile = self::_generateTempname();
    file_put_contents($infile, $string);
    $cmd = sprintf(self::CMD_PKCS7_PRINT_CERTS_SPRINTF,
      CecSystemUtil::OPENSSL_BIN, $infile, $outfile);
    CecCliUtil::execCommandLineSync($cmd);
    $returnString = file_get_contents($outfile);
    unlink($infile);
    unlink($outfile);
    return($returnString);
  } // generatePkcs7WithText

  static private function _crl2pkcs7($signedCertText, $caCert) {
    $tf1 = CecUtil::stringToTempFile($signedCertText);
    $tf2 = CecUtil::stringToTempFile($caCert);
    $tf3 = self::_generateTempname();
    $cmd = sprintf(self::CMD_CRL2PKCS7_SPRINTF, CecSystemUtil::OPENSSL_BIN,
      $tf1, $tf2, $tf3);
    CecCliUtil::execCommandLineSync($cmd);
    $pkcs7 = file_get_contents($tf3);
    unlink($tf1);
    unlink($tf2);
    unlink($tf3);
    return($pkcs7);
  } // _crl2pkcs7

  static private function _generateNextSerialNumber($configFilePath, $caDir,
      $validityDays, $configSections, $overrideArray) {
    $serialNumber = self::FIRST_SERIAL_NUMBER;
    $serialFile = null;
    $configArray = CecOpensslConfig::getConfigurationInMemory($caDir,
      $validityDays, $configSections, $overrideArray);
    $serialFile = CecUtil::getNestedArrayElement($configArray,
      Array(CecOpensslConfig::SECTION_CA_DEFAULT, CecOpensslConfig::KEY_SERIAL));
    if (!empty($serialFile)) {
      if (!is_file($serialFile)) {
        $fh = fopen($serialFile, 'w');
        if ($fh !== false) {
          $result = fwrite($fh, $serialNumber);
          if (!$result) {
            CecAppLogger::logError("Failed to write serial file ".$serialFile);
          } // if
          fclose($fh);
        } else {
CecAppLogger::logError("Failed to create serial file ".$serialFile);
        } // else
      } else {
        $fh = fopen($serialFile, 'r');
        if ($fh !== false) {
          $count = fscanf($fh, self::SPRINTF_SERIAL_NUMBER, $sn);
          if (($count==1) && ($sn > 0)) {
            $serialNumber = $sn + 1;
          } // if
          fclose($fh);
        } // if
      } // else
    } // if
    return(Array($serialNumber, $serialFile));
  } // _generateNextSerialNumber

  private function _recordSerialNumberUsed($serialNumber, $serialFile) {
/*
    $fh = fopen($serialFile, 'w');
    if ($fh === false) return;
    fwrite($fh, sprintf(self::SPRINTF_SERIAL_NUMBER, $serialNumber));
    fclose($fh);
*/
  } // _recordSerialNumberUsed

  /*
   * @param csr string Text of CSR
   * @param caCert string Text of CA certificate
   * @param keyFile string Text of CA key file
   * @param privKey string Private key to open keyFile
   */
  static public function signCsr($configFilePath, $caDir, 
      $csr, $caCert, $keyFile, $privKey, $validityDays=null) {
    $configSections = null; /* meaning default */
    if (is_null($validityDays)) {
      $overrideArray = null;
    } else {
      $overrideArray = Array(
        CecOpensslConfig::SECTION_CA_DEFAULT => Array(
          CecOpensslConfig::KEY_DEFAULT_DAYS => $validityDays,
        ) // Array
      ); // Array
    } // else
    try {
      list($serialNumber, $serialFile) = self::_generateNextSerialNumber(
        $configFilePath, $caDir, $validityDays, $configSections, $overrideArray);
      $signedCertText2 = self::_signCsrInPhp($configFilePath, $caDir, $validityDays,
        $csr, $caCert, $keyFile, $privKey, $serialNumber, $configSections, $overrideArray);
      $signedCertText = self::_signCsrInCmd($configFilePath, $caDir, $validityDays,
        $csr, $caCert, $keyFile, $privKey, $configSections, $overrideArray);
      if (strcmp($signedCertText, $signedCertText2) != 0) {
CecAppLogger::logInfo("signedCertText are different:\ncmd=".$signedCertText
  ."\nphp=".$signedCertText2);
      } // if

      $pkcs7 = self::_crl2pkcs7($signedCertText, $caCert);
      if (empty($pkcs7)) {
        $msg = "crl2pkcs7 failed";
CecAppLogger::logError($msg);
        throw new Exception($msg);
      } // if
      self::_recordSerialNumberUsed($serialNumber, $serialFile);
      return(Array($signedCertText, $pkcs7));
    } catch (Exception $e) {
      throw $e;
    } // catch
  } // signCsr

  static private function _signCsrInCmd($configFilePath, $caDir, $validityDays, $csr,
      $caCert, $keyFile, $privKey, $configSections, $overrideArray) {
    $outFile = CecUtil::generateTempnam();
    $certFile = CecUtil::generateTempnam();
    $keyTempFile = CecUtil::generateTempnam();
    $csrFile = CecUtil::generateTempnam();
    file_put_contents($certFile, $caCert);
    file_put_contents($keyTempFile, $keyFile);
    file_put_contents($csrFile, $csr);
    $cmd = sprintf(self::CMD_CA_SIGN_CSR_SPRINTF, CecSystemUtil::OPENSSL_BIN,
      CecOpensslConfig::getConfiguration($configFilePath, $caDir, $validityDays,
        $configSections, $overrideArray, true),
      $certFile, $keyTempFile, $privKey, $csrFile, $outFile);
    $results = CecCliUtil::execCommandLineSync($cmd);
    $signedCertText = file_get_contents($outFile);
    unlink($outFile);
    unlink($certFile);
    unlink($keyTempFile);
    unlink($csrFile);
    return($signedCertText);
  } // _signCsrInCmd

  static private function _signCsrInPhp($configFilePath, $caDir, $validityDays, $csr,
      $caCert, $keyFile, $privKey, $serialNumber, $configSections, $overrideArray) {
    $signedCertResource = openssl_csr_sign($csr, $caCert,
      Array($keyFile, $privKey),
      self::SIGNED_CERT_VALIDITY_DAYS,
      CecOpensslConfig::getConfiguration($configFilePath, $caDir, $validityDays,
        $configSections, $overrideArray, false),
      $serialNumber);
    if (isset($php_errormsg)) {
      $error = "  Error: ".$php_errormsg;
    } else {
      $error = null;
    } // else
    if ($signedCertResource === false) {
      $msg = "openssl_csr_sign failed.".$error;
CecAppLogger::logError($msg);
      throw new Exception($msg);
      return(Array(false, null));
    } // if
    $status = openssl_x509_export($signedCertResource, $signedCertText);
    if ($status === false) {
      $msg = "openssl_x509_export failed.  Error: ".$php_errormsg;
CecAppLogger::logError($msg);
      throw new Exception($msg);
    } // if
    return($signedCertText);
  } // _signCsrInPhp

} // CecOpensslUtil
?>
