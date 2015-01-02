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
/* CecSforceConnection.php */
$rDir = '';
require_once('sforce/soapclient/SforcePartnerClient.php');
require_once($rDir.'cec/php/soapclient/CecSforceMetadataClient.php');
require_once($rDir."cec/php/models/CecSforceTableProfile.php");
require_once($rDir.'php/cec/CecLogger.php');

class CecSforceConnection {

  private $sfciUserName;
  private $sfciPassword;
  private $sfciSecurityToken;
  private $metadataXmlPath;
  private $partnerXmlPath;

  private $sforceMetadataClient;
  private $sforcePartnerClient;
  private $sforcePartnerLogin;
  private $sfciProfile;
  private $sfciUserInfo;

  public function __construct($sfciUserName, $sfciPassword, $sfciSecurityToken,
      $partnerXmlPath, $metadataXmlPath) {
    $this->sfciUserName = $sfciUserName;
    $this->sfciPassword = $sfciPassword;
    $this->sfciSecurityToken = $sfciSecurityToken;
    $this->partnerXmlPath = $partnerXmlPath;
    $this->metadataXmlPath = $metadataXmlPath;
    $this->sforceMetadataClient = null;
    $this->sforcePartnerClient = null;
    $this->sforcePartnerLogin = null;
    $this->sfciProfile = null;
    $this->sfciUserInfo = null;

    $this->_createPartnerConnection();
  } // __construct

  protected function getAlohaAppToken() {
    return(null);
  } // getAlohaAppToken

  private function _createPartnerConnection() {
    if (!is_null($this->sforcePartnerClient)) return;
    if (empty($this->sfciUserName)) {
CecLogger::logError("_createPartnerConnection: sfciUserName cannot be empty");
      return;
    } // if
    if (empty($this->sfciPassword)) {
CecLogger::logError("_createPartnerConnection: sfciPassword cannot be empty");
      return;
    } // if
    if (empty($this->sfciSecurityToken)) {
CecLogger::logError("_createPartnerConnection: sfciSecurityToken cannot be empty");
      return;
    } // if
    if (empty($this->partnerXmlPath)) {
CecLogger::logError("_createPartnerConnection: partnerXmlPath cannot be empty");
      return;
    } // if
    try {
CecLogger::logDebug("_createPartnerConnection: userName=".$this->sfciUserName);
      $this->sforcePartnerClient = new SforcePartnerClient();
      $sforcePartnerConnection = $this->sforcePartnerClient->createConnection($this->partnerXmlPath);
      $alohaAppToken = $this->getAlohaAppToken();
      if (!empty($alohaAppToken)) {
        $this->sforcePartnerClient->setCallOptions(new CallOptions($alohaAppToken, null));
      } // if
      $this->sforcePartnerLogin = $this->sforcePartnerClient->login(
        $this->sfciUserName, $this->sfciPassword.$this->sfciSecurityToken);
      return(true);
    } catch (Exception $e) {
CecLogger::logError($e, "_createPartnerConnection error: ".$e->faultstring);
      return(false);
    } // catch
  } // createPartnerConnection

  public function isConnectionReady() {
    if (empty($this->sforcePartnerClient)) return(false);
    if (empty($this->sforcePartnerLogin)) return(false);
    return(true);
  } // isConnectionReady

  private function getMetadataClient() {
    if (empty($this->sforceMetadataClient)) {
      $status = $this->_createMetadataClient();
      if (!$status) {
CecLogger::logError("_createMetadataClient failed");
        return(null);
      } // if
    } // if
    return($this->sforceMetadataClient);
  } // getMetadataClient

  private function _createMetadataClient() {
    if (empty($this->sforcePartnerLogin)) {
CecLogger::logError("_createMetadataClient: sforcePartnerLogin cannot be empty");
      return(false);
    } // if
    if (empty($this->metadataXmlPath)) {
CecLogger::logError("_createMetadataClient: metadataXmlPath cannot be empty");
      return(false);
    } // if
    try {
      $this->sforceMetadataClient = new SforceMetadataClient($this->metadataXmlPath,
        $this->sforcePartnerLogin, $this->sforcePartnerClient);
      return(true);
    } catch (Exception $e) {
CecLogger::logError($e, "_createMetadataClient error: ".$e->faultstring);
      return(false);
    } // catch
  } // createMetadataClient

  public function describeGlobal() {
    if (!$this->isConnectionReady()) {
      return(null);
    } // if
    try {
      return($this->sforcePartnerClient->describeGlobal());
    } catch (Exception $e) {
CecLogger::logError($e, "describeGlobal throws exception ".$e->getMessage());
      return(null);
    } // catch
  } // describeGlobal

  public function describeSObject($tableName, $ignoreError=false) {
    if (!$this->isConnectionReady()) {
      return(null);
    } // if
    try {
      return($this->sforcePartnerClient->describeSObject($tableName));
    } catch (Exception $e) {
      if (!$ignoreError) {
CecLogger::logError($e, "describeSObject throws exception ".$e->getMessage());
      } // if
      return(null);
    } // catch
  } // describeSObject

  public function query($soql, $queryOptions) {
    if (!$this->isConnectionReady()) {
      return(null);
    } // if
    try {
      return($this->sforcePartnerClient->query($soql, $queryOptions));
    } catch (Exception $e) {
CecLogger::logError($e, "query throws exception ".$e->getMessage());
      return(false);
    } // catch
  } // query

  public function queryMore($queryLocator) {
    if (!$this->isConnectionReady()) {
      return(null);
    } // if
    try {
      return($this->sforcePartnerClient->queryMore($queryLocator));
    } catch (Exception $e) {
CecLogger::logError($e, "queryMore throws exception ".$e->getMessage());
      return(false);
    } // catch
  } // query

  public function upsert($externalIdFieldName, $sObjectArray) {
    if (!$this->isConnectionReady()) {
      return(null);
    } // if
    try {
      return($this->sforcePartnerClient->upsert($externalIdFieldName, $sObjectArray));
    } catch (Exception $e) {
CecLogger::logError(Array("upsert throws exception ".$e->getMessage(), $e));
      return(null);
    } // catch
  } // upsert

  public function update($sObjectArray) {
    if (!$this->isConnectionReady()) {
      return(null);
    } // if
    try {
      return($this->sforcePartnerClient->update($sObjectArray));
    } catch (Exception $e) {
CecLogger::logError($e, "update throws exception ".$e->getMessage());
      return(null);
    } // catch
  } // update

  public function createMetadata($metadataDescription) {
    $sforceMetadataClient = $this->getMetadataClient();
    if (empty($this->sforceMetadataClient)) {
      return(false);
    } // if
    $sforceMetadataClient->create($metadataDescription);
  } // createMetadata

  /*
    These are the fields in the returned stdClass object:

    accessibilityMode
    currencySymbol
    orgDefaultCurrencyIsoCode
    orgDisallowHtmlAttachments
    orgHasPersonAccounts
    organizationId
    organizationMultiCurrency
    organizationName
    profileId
    roleId
    userDefaultCurrencyIsoCode
    userEmail
    userFullName
    userId
    userLanguage
    userLocale
    userName
    userTimeZone
    userType
    userUiSkin
  */
  public function getUserInfo() {
    if (!$this->isConnectionReady()) {
      return(null);
    } // if
    try {
      return($this->sforcePartnerClient->getUserInfo());
    } catch (Exception $e) {
CecLogger::logError($e, "getUserInfo throws exception ".$e->getMessage());
      return(null);
    } // catch
  } // getUserInfo

  private function _fetchSfciUserInfo() {
    if (empty($this->sfciUserInfo)) {
      $this->sfciUserInfo = $this->getUserInfo();
    } // if
    return($this->sfciUserInfo);
  } // _fetchSfciUserInfo

  public function getOrganizationId() {
    $sfciUserInfo = $this->_fetchSfciUserInfo();
    if (empty($sfciUserInfo)) return(null);
    return($sfciUserInfo->organizationId);
  } // getOrganizationId

  private function _fetchSfciProfile() {
    if (empty($this->sfciProfile)) {
      $sfciUserInfo = $this->_fetchSfciUserInfo();
      if (empty($sfciUserInfo)) return(null);
      $profileId = $sfciUserInfo->profileId;
      $tableProfile = new CecSforceTableProfile($this);
      if (!$tableProfile->isAccessible()) {
        return(null);
      } // if
      $this->sfciProfile = $tableProfile->selectOneByIdAsArray($profileId);
CecLogger::logDebug($this->sfciProfile, "sfciProfile of profileID=".$profileId);
    } // if
    return($this->sfciProfile);
  } // _fetchSfciProfile

  public function isSystemAdministratorProfile() {
    $sfciProfile = $this->_fetchSfciProfile();
    if (empty($sfciProfile)) return(false);
    if (empty($sfciProfile[CecSforceTableProfile::FIELD_NAME])) return(false);
    if ($sfciProfile[CecSforceTableProfile::FIELD_NAME] ==
        CecSforceTableProfile::STANDARD_PROFILE_SYSTEM_ADMINISTRATOR) {
      return(true);
    } // if
    return(false);
  } // isSystemAdministratorProfile

} // CecSforceConnection
?>
