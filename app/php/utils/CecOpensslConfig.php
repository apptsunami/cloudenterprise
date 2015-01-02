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
/* CecOpensslConfig.php */
$rDir = '';
require_once('Zend/Log.php');
require_once($rDir.'cec/php/CecAppLogger.php');

class CecOpensslConfig {

  const SECTION_OID_SECTION = 'oid_section';
  const SECTION_NEW_OIDS = 'new_oids';
  const SECTION_CA = 'ca';
  const SECTION_CA_DEFAULT = 'CA_default';
  const SECTION_POLICY_MATCH = 'policy_match';
  const SECTION_POLICY_ANYTHING = 'policy_anything';
  const SECTION_REQ = 'req';
  const SECTION_REQ_DISTINGUISHED_NAME = 'req_distinguished_name';
  const SECTION_REQ_ATTRIBUTES = 'req_attributes';
  const SECTION_USR_CERT = 'usr_cert';
  const SECTION_V3_REQ = 'v3_req';
  const SECTION_V3_CA = 'v3_ca';
  const SECTION_CRL_EXT = 'crl_ext';
  const SECTION_PROXY_CERT_EXT = 'proxy_cert_ext';

  const KEY_CONFIG = 'config';

  /* CA_default keys */
  const KEY_DIR = 'dir';
  const KEY_CERTS = 'certs';
  const KEY_CRL_DIR = 'crl_dir';
  const KEY_DATABASE = 'database';
  const KEY_NEW_CERTS_DIR = 'new_certs_dir';
  const KEY_CERTIFICATE = 'certificate';
  const KEY_SERIAL = 'serial';
  const KEY_CRLNUMBER = 'crlnumber';
  const KEY_CRL = 'crl';
  const KEY_PRIVATE_KEY = 'private_key';
  const KEY_RANDFILE = 'RANDFILE';
  const KEY_X_509_EXTENSIONS = 'x509_extensions';
  const KEY_NAME_OPT = 'name_opt';
  const KEY_CERT_OPT = 'cert_opt';
  const KEY_DEFAULT_DAYS = 'default_days';
  const KEY_DEFAULT_CRL_DAYS = 'default_crl_days';
  const KEY_DEFAULT_MD = 'default_md';
  const KEY_PRESERVE = 'preserve';
  const KEY_POLICY = 'policy';

  /* these paths are relative to ca home dir */
  const PRIVATE_DIR = '/private';
  const DEFAULT_KEY_FILE = 'privkey.pem';
  const CA_CERTIFICATE_PEM = 'ca_cert.pem';
  const CA_PRIVATE_KEY_PEM = '/private/ca_key.pem';
  const SERIAL_NUMBER_FILE = '/serial';
  const CRL_PEM = '/crl.pem';

  const CRL_DAYS = 30;

  static private $defaultSections = Array(
    self::SECTION_NEW_OIDS,
    self::SECTION_CA,
    self::SECTION_CA_DEFAULT,
    self::SECTION_POLICY_MATCH,
    self::SECTION_POLICY_ANYTHING,
    self::SECTION_REQ,
    self::SECTION_REQ_DISTINGUISHED_NAME,
    self::SECTION_REQ_ATTRIBUTES,
    self::SECTION_USR_CERT,
    self::SECTION_V3_CA,
  ); // Array

  static private function _writeConfigToFile($fh, $configArray) {
    if (!is_array($configArray)) return;
    foreach($configArray as $key => $value) {
      if (!is_array($value)) {
        fwrite($fh, $key.' = '.CecUtil::quoteString($value)."\n");
      } else {
        fwrite($fh, '[ '.$key.' ]'."\n");
        /* recursion */
        self::_writeConfigToFile($fh, $value);
      } // else
    } // foreach
  } // _writeConfigToFile

  static public function getConfiguration($configFilePath, $caDir, $validityDays, $sections=null, $overrideArray=null,
      $filePathOnly=true) {
    if (empty($configFilePath)) return(null);
    $fh = fopen($configFilePath, "w");
    if ($fh === false) {
      throw new Exception("Cannot create file ".$configFilePath
        ."  Error: ".$php_errormsg);
    } // if
    self::_writeConfigToFile($fh,
      self::getConfigurationInMemory($caDir, $validityDays, $sections, $overrideArray));
    fclose($fh);
    if ($filePathOnly) {
      return($configFilePath);
    } else {
      return(Array(self::KEY_CONFIG=>$configFilePath));
    } // else
  } // getConfiguration

  static public function getConfigurationInMemory($caDir, $validityDays, $sections=null,
      $overrideArray=null) {
    if (is_null($sections)) {
      $sections = self::$defaultSections;
    } // if
    $configArray = Array(
      /* This definition stops the following lines choking if HOME isn't defined. */
      'HOME' => $_ENV['HOME'],
      'RANDFILE' => $_ENV['HOME'].'/.rnd',
      /* Extra OBJECT IDENTIFIER info: */
      'oid_section' => self::SECTION_NEW_OIDS,
    ); // Array
    if (!empty($sections)) {
      foreach($sections as $section) {
        switch($section) {
        case self::SECTION_NEW_OIDS:
          $configArray[self::SECTION_NEW_OIDS] =
            self::_getConfigurationNewOids($caDir);
          break;
          break;
        case self::SECTION_CA:
          $configArray[self::SECTION_CA] =
            self::_getConfigurationCa($caDir);
          break;
        case self::SECTION_CA_DEFAULT:
          $configArray[self::SECTION_CA_DEFAULT] =
            self::_getConfigurationCaDefault($caDir, $validityDays);
          break;
        case self::SECTION_POLICY_MATCH:
          $configArray[self::SECTION_POLICY_MATCH] =
            self::_getConfigurationPolicyMatch($caDir);
          break;
        case self::SECTION_POLICY_ANYTHING:
          $configArray[self::SECTION_POLICY_ANYTHING] =
            self::_getConfigurationPolicyAnything($caDir);
          break;
        case self::SECTION_REQ:
          $configArray[self::SECTION_REQ] =
            self::_getConfigurationReq($caDir);
          break;
        case self::SECTION_REQ_DISTINGUISHED_NAME:
          $configArray[self::SECTION_REQ_DISTINGUISHED_NAME] =
            self::_getConfigurationReqDistinguishedName($caDir);
          break;
        case self::SECTION_REQ_ATTRIBUTES:
          $configArray[self::SECTION_REQ_ATTRIBUTES] =
            self::_getConfigurationReqAttributes($caDir);
          break;
        case self::SECTION_USR_CERT:
          $configArray[self::SECTION_USR_CERT] =
            self::_getConfigurationUsrCert($caDir);
          break;
        case self::SECTION_V3_REQ:
          $configArray[self::SECTION_V3_REQ] =
            self::_getConfigurationV3Req($caDir);
          break;
        case self::SECTION_V3_CA:
          $configArray[self::SECTION_V3_CA] =
            self::_getConfigurationV3Ca($caDir);
          break;
        case self::SECTION_CRL_EXT:
          $configArray[self::SECTION_CRL_EXT] =
            self::_getConfigurationCrlExt($caDir);
          break;
        case self::SECTION_PROXY_CERT_EXT:
          $configArray[self::SECTION_PROXY_CERT_EXT] =
            self::_getConfigurationProxyCertExt($caDir);
          break;
        } // switch
      } // foreach
    } // if
    if (!is_null($overrideArray)) {
      self::_overrideConfigurationArray($configArray, $overrideArray);
    } // if
    return($configArray);
  } // getConfigurationInMemory

  static private function _overrideConfigurationArray(&$configArray, $overrideArray) {
    if (!is_array($overrideArray)) {
CecAppLogger::logVariable($overrideArray,
  __FUNCTION__.".overrideArray should be an array", Zend_Log::ERR);
      return;
    } // if
    foreach($overrideArray as $key => $value) {
      if (!isset($configArray[$key])) {
CecAppLogger::logVariable($configArray,
  __FUNCTION__." cannot find key ".$key, Zend_Log::ERR);
        continue;
      } // if
      if (!is_array($value)) {
        if (!is_array($configArray[$key])) {
          $configArray[$key] = $value;
        } else {
CecAppLogger::LogVariable($configArray,
  "Try to assign value ".$value." to config array entry", Zend_Log::ERR);
        } // else
      } else {
        /* recursion */
        self::_overrideConfigurationArray($configArray[$key], $value);
      } // else
    } // foreach
  } // _overrideConfigurationArray

  static private function _getConfigurationNewOids($caDir) {
    return(Array(
/* We can add new OIDs in here for use by 'ca' and 'req'. */
/* Add a simple OID like this: */
//    'testoid1' => '1.2.3.4',
/* Or use config file substitution like this: */
//    'testoid2' => $testoid1.'.5.6',
    ));
  } // _getConfigurationNewOids

  static private function _getConfigurationCa($caDir) {
    return(Array(
      'default_ca' => self::SECTION_CA_DEFAULT,
    ));
  } // _getConfigurationCa

  static private function _getConfigurationCaDefault($caDir, $validityDays) {
    $configArray = Array(
      /* Where everything is kept */
      self::KEY_DIR => $caDir,
      /* Where the issued certs are kept */
      self::KEY_CERTS => $caDir.'/certs',
      /* Where the issued crl are kept */
      self::KEY_CRL_DIR => $caDir.'/crl',
      /* database index file */
      self::KEY_DATABASE => $caDir.'/index.txt',
      /* Set to 'no' to allow creation of several certs with same subject */
//    'unique_subject' => 'no',
      /* default place for new certs */
      self::KEY_NEW_CERTS_DIR => $caDir.'/newcerts',
      /* The CA certificate */
      self::KEY_CERTIFICATE => $caDir.self::CA_CERTIFICATE_PEM,
      /* The current serial number */
      self::KEY_SERIAL => $caDir.self::SERIAL_NUMBER_FILE,
      /* the current crl number */
      /* must be commented out to leave a V1 CRL */
      self::KEY_CRLNUMBER => $caDir.'/crlnumber',
      /* The current CRL */
      self::KEY_CRL => $caDir.self::CRL_PEM,
      /* The private key */
      self::KEY_PRIVATE_KEY => $caDir.self::CA_PRIVATE_KEY_PEM,
      /* private random number file */
      self::KEY_RANDFILE => $caDir.'/private/.rand',
      /* The extentions to add to the cert */
      self::KEY_X_509_EXTENSIONS => self::SECTION_USR_CERT,
      /* Comment out the following two lines for the "traditional" (and highly broken) format. */
      /* Subject Name options */
      self::KEY_NAME_OPT => 'ca_default',
      /* Certificate field options */
      self::KEY_CERT_OPT => 'ca_default',
      /* Extension copying option: use with caution.  */
//    'copy_extensions' => 'copy',

/*
 Extensions to add to a CRL. Note: Netscape communicator chokes on V2 CRLs
 so this is commented out by default to leave a V1 CRL.
 crlnumber must also be commented out to leave a V1 CRL.
*/
//    'crl_extensions' => self::SECTION_CRL_EXT,
      /* how long (days) to certify for */
      self::KEY_DEFAULT_DAYS => $validityDays,
      /* how long before next CRL */
      self::KEY_DEFAULT_CRL_DAYS => self::CRL_DAYS,
      /* which md to use */
      self::KEY_DEFAULT_MD => 'sha1',
      /* keep passed DN ordering */
      self::KEY_PRESERVE => 'no',
/*
  A few difference way of specifying how similar the request should look
  For type CA, the listed attributes must be the same, and the optional
  and supplied fields are just that :-)
*/
      self::KEY_POLICY => self::SECTION_POLICY_MATCH,
    );

    $directoryKeys = Array(
      self::KEY_DIR,
      self::KEY_CERTS,
      self::KEY_CRL_DIR,
      self::KEY_NEW_CERTS_DIR,
      self::PRIVATE_DIR,
    ); // Array
    foreach($directoryKeys as $d) {
      CecUtil::createDirectory($configArray[$d]);
    } // foreach
    
    $fileKeys = Array(
      self::KEY_DATABASE,
      // self::KEY_SERIAL,
      self::KEY_CRLNUMBER,
      self::KEY_RANDFILE,
    ); // Array
    foreach($fileKeys as $f) {
      CecUtil::createFile($configArray[$f]);
    } // foreach

    return($configArray);
  } // _getConfigurationCaDefault

  static private function _getConfigurationPolicyMatch($caDir) {
    return(Array(
      'countryName' => 'match',
      'stateOrProvinceName' => 'match',
      'organizationName' => 'match',
      'organizationalUnitName' => 'optional',
      'commonName' => 'supplied',
      'emailAddress' => 'optional',
    ));
  } // _getConfigurationPolicyMatch

  static private function _getConfigurationPolicyAnything($caDir) {
/*
 For the 'anything' policy
 At this point in time, you must list all acceptable 'object' types.
*/
    return(Array(
      'countryName' => 'optional',
      'stateOrProvinceName' => 'optional',
      'localityName' => 'optional',
      'organizationName' => 'optional',
      'organizationalUnitName' => 'optional',
      'commonName' => 'supplied',
      'emailAddress' => 'optional',
    ));
  } // _getConfigurationPolicyAnything

  static private function _getConfigurationReq($caDir) {
    return(Array(
      'default_bits' => 1024,
      'default_md' => 'sha1',
      'default_keyfile' => self::DEFAULT_KEY_FILE,
      'distinguished_name' => self::SECTION_REQ_DISTINGUISHED_NAME,
      'attributes' => self::SECTION_REQ_ATTRIBUTES,
      /* The extentions to add to the self signed cert */
      'x509_extensions' => self::SECTION_V3_CA,
/*
 Passwords for private keys if not present they will be prompted for
 input_password = secret
 output_password = secret

 This sets a mask for permitted string types. There are several options. 
 default: PrintableString, T61String, BMPString.
 pkix   : PrintableString, BMPString.
 utf8only: only UTF8Strings.
 nombstr : PrintableString, T61String (no BMPStrings or UTF8Strings).
 MASK:XXXX a literal mask value.
 WARNING: current versions of Netscape crash on BMPStrings or UTF8Strings
 so use this option with caution!
 we use PrintableString+UTF8String mask so if pure ASCII texts are used
 the resulting certificates are compatible with Netscape
*/
      'string_mask' => 'MASK:0x2002',
      /* The extensions to add to a certificate request */
//    'req_extensions' => self::SECTION_V3_REQ,
    ));
  } // _getConfigurationReq

  static private function _getConfigurationReqDistinguishedName($caDir) {
    return(Array(
      'countryName' => "Country Name (2 letter code)",
      'countryName_default' => "GB",
      'countryName_min' => 2,
      'countryName_max' => 2,
      'stateOrProvinceName' => "State or Province Name (full name)",
      'stateOrProvinceName_default' => "Berkshire",
      'localityName' => "Locality Name (eg, city)",
      'localityName_default' => "Newbury",
      '0.organizationName' => "Organization Name (eg, company)",
      '0.organizationName_default' => "My Company Ltd",
      /* we can do this but it is not needed normally :-) */
//    '1.organizationName' => "Second Organization Name (eg, company)",
//    '1.organizationName_default' => "World Wide Web Pty Ltd",
      'organizationalUnitName' => "Organizational Unit Name (eg, section)",
//    'organizationalUnitName_default' => '',
      'commonName' => "Common Name (eg, your name or your server's hostname)",
      'commonName_max' => 64,
      'emailAddress' => "Email Address",
      'emailAddress_max' => 64,
//    'SET-ex3' => 'SET extension number 3',
    ));
  } // _getConfigurationReqDistinguishedName

  static private function _getConfigurationReqAttributes($caDir) {
    return(Array(
      'challengePassword' => "A challenge password",
      'challengePassword_min' => 4,
      'challengePassword_max' => 20,
      'unstructuredName' => "An optional company name",
    ));
  } // _getConfigurationReqAttributes

  static private function _getConfigurationUsrCert($caDir) {
/*
 These extensions are added when 'ca' signs a request.
 This goes against PKIX guidelines but some CAs do it and some software
 requires this to avoid interpreting an end user certificate as a CA.
*/
    return(Array(
      'basicConstraints' => 'CA:FALSE',
/*
 Here are some examples of the usage of nsCertType. If it is omitted
 the certificate can be used for anything *except* object signing.
*/
/* This is OK for an SSL server. */
//    'nsCertType' => 'server',
/* For an object signing certificate this would be used. */
//    'nsCertType' => 'objsign',
/* For normal client use this is typical */
//    'nsCertType' => 'client, email',
/* and for everything including object signing: */
//    'nsCertType' => 'client, email, objsign',
/* This is typical in keyUsage for a client certificate. */
//    'keyUsage' => 'nonRepudiation, digitalSignature, keyEncipherment',
/* This will be displayed in Netscape's comment listbox. */
      'nsComment' => "OpenSSL Generated Certificate",
/* PKIX recommendations harmless if included in all certificates. */
      'subjectKeyIdentifier' => 'hash',
      'authorityKeyIdentifier' => 'keyid,issuer',
/*
 This stuff is for subjectAltName and issuerAltname.
 Import the email address.
*/
//    'subjectAltName' => 'email:copy',
/* An alternative to produce certificates that aren't deprecated according to PKIX.  */
//    'subjectAltName' => 'email:move',
/* Copy subject details */
//    'issuerAltName' => 'issuer:copy',
//    'nsCaRevocationUrl' => 'http://www.domain.dom/ca-crl.pem',
//    'nsBaseUrl'
//    'nsRevocationUrl'
//    'nsRenewalUrl'
//    'nsCaPolicyUrl'
//    'nsSslServerName'
    ));
  } // _getConfigurationReqAttributes

  static private function _getConfigurationV3Req($caDir) {
    /*  Extensions to add to a certificate request */
    return(Array(
      'basicConstraints' => 'CA:FALSE',
      'keyUsage' => 'nonRepudiation, digitalSignature, keyEncipherment',
    ));
  } // _getConfigurationV3Req

  static private function _getConfigurationV3Ca($caDir) {
    /* Extensions for a typical CA */
    return(Array(
      /* PKIX recommendation. */
      'subjectKeyIdentifier' => 'hash',
      'authorityKeyIdentifier' => 'keyid:always,issuer:always',
      /* This is what PKIX recommends but some broken software chokes on
         critical extensions. */
//    'basicConstraints' => 'critical,CA:true',
      /* So we do this instead. */
      'basicConstraints' => 'CA:true',
      /* Key usage: this is typical for a CA certificate.
         However since it will prevent it being used as an test
         self-signed certificate it is best left out by default.
       */
//    'keyUsage' => 'cRLSign, keyCertSign',
      /* Some might want this also */
//    'nsCertType' => 'sslCA, emailCA',

      /* Include email address in subject alt name: another PKIX recommendation */
//    'subjectAltName' => 'email:copy',
      /* Copy issuer details */
//    'issuerAltName' => 'issuer:copy',

      /* DER hex encoding of an extension: beware experts only! */
//    'obj' => 'DER:02:03',
      /* Where 'obj' is a standard or added object You can even override
         a supported extension: */
//    'basicConstraints' => 'critical, DER:30:03:01:01:FF',
    ));
  } // _getConfigurationV3Ca

  static private function _getConfigurationCrlExt($caDir) {
    return(Array(
    /* CRL extensions. */
    /* Only issuerAltName and authorityKeyIdentifier make any sense in a CRL. */
//    'issuerAltName' => 'issuer:copy',
      'authorityKeyIdentifier' => 'keyid:always,issuer:always',
    ));
  } // _getConfigurationCrlExt

  static private function _getConfigurationProxyCertExt($caDir) {
/*
   These extensions should be added when creating a proxy certificate
   This goes against PKIX guidelines but some CAs do it and some software
   requires this to avoid interpreting an end user certificate as a CA.
*/
    return(Array(
      'basicConstraints' => 'CA:FALSE',
/*
  Here are some examples of the usage of nsCertType. If it is omitted
  the certificate can be used for anything *except* object signing.
*/
/* This is OK for an SSL server. */
//    'nsCertType' => 'server',
/* For an object signing certificate this would be used. */
//    'nsCertType' => 'objsign',
/* For normal client use this is typical */
//    'nsCertType' => 'client, email',

/* and for everything including object signing: */
//    'nsCertType' => 'client, email, objsign',

/* This is typical in keyUsage for a client certificate. */
//    'keyUsage' => 'nonRepudiation, digitalSignature, keyEncipherment',

/* This will be displayed in Netscape's comment listbox. */
      'nsComment' => 'OpenSSL Generated Certificate',
/* PKIX recommendations harmless if included in all certificates. */
      'subjectKeyIdentifier' => 'hash',
      'authorityKeyIdentifier' => 'keyid,issuer:always',
/*
  This stuff is for subjectAltName and issuerAltname.
  Import the email address.
*/
//    'subjectAltName' => 'email:copy',
/* An alternative to produce certificates that aren't deprecated according to PKIX. */
//    'subjectAltName' => 'email:move',
/* Copy subject details */
//    'issuerAltName' => 'issuer:copy',
//    'nsCaRevocationUrl' => 'http://www.domain.dom/ca-crl.pem',
//    'nsBaseUrl'
//    'nsRevocationUrl'
//    'nsRenewalUrl'
//    'nsCaPolicyUrl'
//    'nsSslServerName'
/* This really needs to be in place for it to be a proxy certificate. */
      'proxyCertInfo' => 'critical,language:id-ppl-anyLanguage,pathlen:3,policy:foo',
    ));
  } // _getConfigurationProxyCertExt

} // CecOpensslConfig
?>
