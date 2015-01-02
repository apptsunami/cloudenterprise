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
/* CecSforceMetadataClient.php */
require_once ('sforce/soapclient/SforceMetadataClient.php');

/**
 *  Supports Sforce metadata services
 */
class CecSforceMetadataClient extends SforceMetadataClient {

  const METADATA_NAMESPACE = 'urn:metadata.soap.sforce.com';
  const SFORCE_API_VERSION = '15.0';

  const POLL_SLEEP_TIME = 2; /* 2 seconds */

  const ASYNC_REQUEST_STATE_QUEUED = 'Queued';
  const ASYNC_REQUEST_STATE_IN_PROGRESS = 'InProgress';
  const ASYNC_REQUEST_STATE_COMPLETED = 'Completed';
  const ASYNC_REQUEST_STATE_ERROR = 'Error';

  public $debug;

  /**
   *  Create a new instance.
   *  @param string wsdl File path to metadata.wsdl.xml
   *  @param tns:LoginResult loginResult Returned from call to login
   *  @param SforcePartnerClient sforceConn Created by 'new SforcePartnerClient()'
   *  @return CecSforceMetadataClient
   */
  public function __construct($wsdl, $loginResult, $sforceConn) {
    parent::__construct($wsdl, $loginResult, $sforceConn);
    $this->debug = FALSE;
  } // __construct

  /**
   *  call checkStatus
   *  @param string ids ID returned from operation
   *  @return mixed results Returned from checkStatus
   */
  private function _checkStatus($ids) {
    $encodedObj->asyncProcessId = new SoapVar($ids, SOAP_ENC_OBJECT, 'ID', $this->namespace);
    return($this->sforce->checkStatus($encodedObj)->result);
  } // __checkStatus


  protected function echoDebugMessage($message) {
    if ($this->debug) {
      echo("\n".$message."\n");
    } // if
  } // echoDebugMessage

  /**
   *  Loop to check the asynchronous response until the operation is complete.
   *  @param mixed ars	Returned from call
   *  @return mixed results FALSE if failed
   */
  protected function checkAsyncResult($ars) {
    if (is_null($ars)) {
      $this->echoDebugMessage("Call failed with null AsyncResult.");
      return(FALSE);
    } // if
    if (is_array($ars)) {
      $createdObjectId = $ars[0]->id;
    } else {
      $createdObjectId = $ars->id;
    } // else
    try {
      $done = FALSE;
      $arsStatus = null;
      while(!$done) {
        $arsStatus = $this->_checkStatus($createdObjectId);
        if (empty($arsStatus)) {
          $this->echoDebugMessage("The object status cannot be retrieved.");
          return(FALSE);
        } // if
        if (isset($arsStatus->done)) {
          $done = $arsStatus->done;
        } // if
        if (isset($arsStatus->state)) {
          switch($arsStatus->state) {
          case self::ASYNC_REQUEST_STATE_COMPLETED:
            $this->echoDebugMessage("Completed.");
            return(FALSE);
          case self::ASYNC_REQUEST_STATE_ERROR:
            $this->echoDebugMessage("Error:statusCode=".$arsStatus->statusCode
              ." message=".$arsStatus->message);
            return(FALSE);
          default:
            $this->echoDebugMessage("The object state is ".$arsStatus->state);
          } // switch
        } // if
        sleep(self::POLL_SLEEP_TIME);
      } // while
      $this->echoDebugMessage("The ID for the created object is ".$arsStatus->id);
      return($arsStatus->id);
    } catch (Exception $e) {
      $this->echoDebugMessage("Failed to create object, error message: ".$e->getMessage());
    } // catch
  } // checkAsyncResult

  /**
   *  Return a SOAP class name from a php object.
   *  @param mixed obj php object of either class SforceCustomObject or SforceCustomField
   *  @return string Either 'CustomObject' or 'CustomField'
   */
  private function convertObjToSoapClass($obj) {
    if (is_subclass_of($obj, "SforceCustomObject")) {
      return('CustomObject');
    } else if (is_subclass_of($obj, "SforceCustomField")) {
      return('CustomField');
    } else {
      return('String');
    } // else
  } // convertObjToSoapClass

  /**
   *  Create a new custom object or a new custom field.
   *  @param mixed obj php object of either class SforceCustomObject or SforceCustomField
   *  @return mixed result from create
   */
  public function create($obj) {
    try {
      $soapClass = $this->convertObjToSoapClass($obj);
      $encodedObj->metadata = new SoapVar($obj, SOAP_ENC_OBJECT, $soapClass,
        $this->namespace);
      /* this call returns AsyncResult */
      $ars = $this->sforce->create($encodedObj);
      $result = $this->checkAsyncResult($ars->result);
      if (!$result) return(false);
      return($ars);
    } catch (Exception $e) {
      $this->echoDebugMessage(get_class($this).".create error: ".$e->getMessage());
      return(null);
    } // catch
  } // create

  /**
   *  Delete a custom object or a custom field.
   *  @param mixed obj php object of either class SforceCustomObject or SforceCustomField
   *  @return mixed result from delete
   */
  public function delete($obj) {
    $soapClass = $this->convertObjToSoapClass($obj);
    $encodedObj->metadata = new SoapVar($obj, SOAP_ENC_OBJECT, $soapClass,
      $this->namespace);
    /* this call returns AsyncResult */
    $ars = $this->sforce->delete($encodedObj);
    $result = $this->checkAsyncResult($ars->result);
    if (!$result) return(false);
    return($ars);
  } // delete

  /**
   *  Update a custom object or a custom field.
   *  @param string currentName Full name of custom object or custom field
   *  @param mixed obj php object of either class SforceCustomObject or SforceCustomField with new attributes
   *  @return mixed result from update
   */
  public function update($currentName, $obj) {
    $cName = new SoapVar($currentName, XSD_STRING);
    $soapClass = $this->convertObjToSoapClass($obj);
    $metadata = new SoapVar($obj, SOAP_ENC_OBJECT, $soapClass, $this->namespace);
    $encodedObj->UpdateMetadata = new SoapVar(
      Array("currentName"=>$cName, "metadata"=>$metadata),
      SOAP_ENC_ARRAY, 'UpdateMetadata', $this->namespace);
    $ars = $this->sforce->update($encodedObj);
    $result = $this->checkAsyncResult($ars->result);
    if (!$result) return(false);
    return($ars);
  } // update

  /**
   *  Returns a full description of metadata
   *  @return mixed result from describeMetadata
   */
  public function describeMetadata() {
    $encodedObj->asOfVersion = new SoapVar(self::SFORCE_API_VERSION, XSD_DOUBLE);
    /* returns FileProperties */
    $ars = $this->sforce->describeMetadata($encodedObj);
    return($ars);
  } // describeMetadata

} // CecSforceMetadataClient
?>
