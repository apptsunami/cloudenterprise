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
/* CecSforceTable.php */
$rDir = '';
require_once('sforce/soapclient/SforceHeaderOptions.php');
require_once($rDir.'php/cec/CecLogger.php');
require_once($rDir.'cec/php/models/CecSforceConnection.php');
require_once($rDir.'cec/php/models/CecSforceSoqlSelect.php');
require_once($rDir."cec/php/soapclient/CecSforceCustomField.php");
/**
 *  Provides support for one table in Sforce.
 */
class CecSforceTable {

  const SOQL_STRING_LIMIT = 10000;

  const SFORCE_DEFAULT_SELECT_BATCH_LIMIT = 500;
  const SFORCE_MINIMUM_SELECT_BATCH_LIMIT = 200;
  const SFORCE_MAXIMUM_SELECT_BATCH_LIMIT = 2000;

  const SFORCE_UPDATE_BATCH_LIMIT = 200;
  const SFORCE_CUSTOM_NAME_SUFFIX = '__c';

  /*
   * The following are read-only fields found on most objects.
   */
  const FIELD_ID = 'Id';
  const FIELD_IS_DELETED = 'IsDeleted';
  const FIELD_CREATED_BY_ID = 'CreatedById';
  const FIELD_CREATED_DATE = 'CreatedDate';
  const FIELD_LAST_MODIFIED_BY_ID = 'LastModifiedById';
  const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
  const FIELD_SYSTEM_MODSTAMP = 'SystemModstamp';

  const FIELD_TYPE_ID = 'ID';

  const RESULT_CREATED = 'created';
  const RESULT_UPDATED = 'updated';
  const RESULT_FAILED = 'failed';
  const RESULT_ERROR_MESSAGES = 'errorMessages';

  const SCHEMA_FIELD_TYPE_DATE_TIME = 'datetime';

  const SOQL_QUOTE = "'";
  const COMMA_EOL = ",\n";
  /* leading spaces just for cosmetics */
  const SCHEMA_FIELD_INDENT_SPACES = "  ";

  protected $sforceConnection;
  protected $tableName;
  protected $schemaFieldNameList;
  protected $fieldNameList;
  protected $limitCount;
  protected $idFieldArray;
  protected $schema;
  protected $indexedFieldDetailsArray;

  /**
   *  Instantiate a new object.
   *  @param CecSforceConnection sforceConnection	CecSforceConnection object
   *  @param string tableName	Sforce table name
   *  @param array fieldNameList Array of database field names only if you want to force a list of database field names.
   *     Otherwise this module will automatically discover the field names visible to the currently logged in user.
   *  @return void
   */
  public function __construct($sforceConnection, $tableName, $fieldNameList) {
    $this->sforceConnection = $sforceConnection;
    $this->tableName = $tableName;
    $this->fieldNameList = $fieldNameList;
    $this->limitCount = self::SFORCE_DEFAULT_SELECT_BATCH_LIMIT;
    $this->idFieldArray = null;
  } // __construct

  /* getters */
  public function getConnection() {
    return($this->sforceConnection);
  } // getConnection

  public function getTableName() {
    return($this->tableName);
  } // getTableName

  public function getDefaultFieldNameList() {
    /* to be defined in child class */
    return(Array(self::FIELD_ID));
  } // getDefaultFieldNameList

  /* setters */
  public function setLimitCount($count) {
    $this->limitCount = $count;
  } // setLimitCount

  public function getTableSchema() {
    if (!isset($this->schema)) {
      $schema = $this->sforceConnection->describeSObject(
        $this->getTableName(), true);
      if (is_null($schema)) return(null);
      $this->schema = $schema;
    } // if
    return($this->schema);
  } // getTableSchema

  /**
   *  Returns a list of field names in this table that are visible to the current user.
   *  @return array fieldNameList	Array of field name strings
   */
  public function getFieldNameList() {
    if (isset($this->schemaFieldNameList)) {
      return($this->schemaFieldNameList);
    } // if
    $metaData = $this->getTableSchema();
    if (is_null($metaData)) {
CecLogger::logDebug("getTableSchema returns null metaData for table ".$this->tableName);
      return(null);
    } // if

    $fields = $metaData->fields;
    $fieldNameList = Array();
    $indexedFieldDetailsArray = Array();
    foreach ($fields as $field) {
      $indexedFieldDetailsArray[$field->name] = $field;
      $fieldNameList[] = $field->name;
      if (strtoupper($field->type) == self::FIELD_TYPE_ID) {
        /* add field to idFieldArray */
        if (is_null($this->idFieldArray)) {
          $this->idFieldArray = Array();
        } // if
        $this->idFieldArray[] = $field;
      } // if
    } // foreach
    $this->schemaFieldNameList = $fieldNameList;
    $this->indexedFieldDetailsArray = $indexedFieldDetailsArray;
    return($fieldNameList);
  } // getFieldNameList

  /**
   *  Returns an array of field name => field structure
   *  @return array field name => field structure
   */
  protected function getIndexedFieldDetailsArray() {
    if (!isset($this->indexedFieldDetailsArray)) {
      $this->getFieldNameList();
    } // if
    return($this->indexedFieldDetailsArray);
  } // getIndexedFieldDetailsArray

  public function isAccessible() {
    $fieldNameList = $this->getFieldNameList();
    if (empty($fieldNameList)) {
      return(false);
    } // if
    return(true);
  } // isAccessible

  /**
   *  Returns all rows in the native Sforce structure.
   *  @return array StdClass objects
   */
  private function selectAll($orderBy=null) {
    return($this->selectSome($queryLocator, true, null, null, $orderBy));
  } // selectAll

  private function selectSome(&$queryLocator, $returnEverything, $filter,
      $fieldNameList=null, $orderBy=null) {
    $tableName = $this->getTableName();
    if (is_null($tableName)) {
CecLogger::logError("Null table name");
      return(null);
    } // if

    if (empty($fieldNameList)) {
      $fieldNameList = $this->getFieldNameList();
      if (empty($fieldNameList)) {
CecLogger::logError("table ".$tableName." not accessible");
        return(null);
      } // if
    } // if
    $soql = 'SELECT '.implode(',', $fieldNameList).' FROM '.$tableName;
    if (!is_null($filter)) {
      $soql .= ' WHERE '.$filter;
    } // if
    if (!is_null($orderBy)) {
      $soql .= ' ORDER BY '.$orderBy;
    } // if
    if (!empty($this->limitCount)) {
      $soql .= ' LIMIT '.$this->limitCount;
    } // if
CecLogger::logDebug("selectSome.soql=".$soql);
    return($this->executeSoql($soql, $this->limitCount, $queryLocator, $returnEverything));
  } // selectSome

  private function executeSoql($soql, $limitCount, &$queryLocator, $returnEverything=true) {
    $queryOptions = new QueryOptions($limitCount);
    try {
      $response = $this->sforceConnection->query(($soql), $queryOptions);
      if (is_null($response)) return(null);
      if ($response->done) {
CecLogger::logDebug("executeSoql: return initial batch and done");
        $queryLocator = null;
        return($response->records);
      } // if
      /* there are more */
      if (!$returnEverything) {
CecLogger::logDebug("executeSoql: return initial batch and the queryLocator");
        $queryLocator = $response->queryLocator;
        return($response->records);
      } // if

CecLogger::logDebug("executeSoql: return everything");
      /* get everything and then return */
      $cumulativeRecords = $response->records;
      $done = false;
      while (!$done) {
        $response = $this->sforceConnection->queryMore($response->queryLocator);
        if (is_null($response)) break;
        $cumulativeRecords = array_merge($cumulativeRecords, $response->records);
        $done = $response->done;
      } // while
      return($cumulativeRecords);
    } catch (Exception $e) {
CecLogger::logError("soql ".$soql." execution error: ".$e->getMessage());
      return(null);
    } // catch
  } // executeSoql

  private function executeSoqlQueryLocator(&$queryLocator) {
CecLogger::logDebug("executeSoqlQueryLocator");
    $response = $this->sforceConnection->queryMore($queryLocator);
    if ($response->done) {
      $queryLocator = null;
    } else {
      $queryLocator = $response->queryLocator;
    } // else
    return($response->records);
  } // executeSoqlQueryLocator

  public function selectCount() {
    $query = 'SELECT COUNT() FROM '.$this->getTableName();
    $response = $this->sforceConnection->query(($query));
    if (is_null($response)) return(null);
    return($response->records);
  } // selectCount

  public function ifTableExists() {
    if (is_null($this->getTableSchema())) {
      return(FALSE);
    } else {
      return(TRUE);
    } // else
  } // ifTableExists

  private function parseAnyField($anyField, &$data) {
      $doc = new DOMDocument();
      $fullXml = '<?xml version="1.0" encoding="UTF-8"?>'
        .'<record xmlns:xsi="http://www.w3.org/1999/XMLSchema-instance"'
        .' xmlns:sf="urn:sobject.partner.soap.sforce.com">'
        .$anyField.'</record>';

      $doc->loadXML($fullXml, LIBXML_NOWARNING);
      $rootNode = $doc->firstChild;
      $childNodes = $rootNode->childNodes;
      if ($childNodes) {
        for($i=0; $i<$childNodes->length; $i++) {
          $childNode = $childNodes->item($i);
          /* remove the sf: prefix */
          $fieldName = str_replace('sf:', '', $childNode->tagName);
          $data[$fieldName] = $childNode->nodeValue;
        } // for
      } // if
  } // parseAnyField

  /**
   *  Select every row and return an array of array (fieldName => value) objects.
   *  @return array Array of (fieldName => value) objects
   */
  public function selectAllAsArray() {
    return($this->selectSomeAsArray(null));
  } // selectAllAsArray

  protected function convertStdObjectToArray($stdObj) {
    $data = Array();
    if (isset($stdObj->Id)) {
      if (!is_array($stdObj->Id)) {
        $data[self::FIELD_ID] = $stdObj->Id;
      } else if (isset($stdObj->Id[0])) {
        $data[self::FIELD_ID] = $stdObj->Id[0];
      } // if
    } // if
    if (isset($stdObj->any)) {
      $this->parseAnyField($stdObj->any, $data);
    } else if (!is_null($stdObj->fields)){
      $fields = get_object_vars($stdObj->fields);
      $data = array_merge($data, $fields);
      for($i=0; $i<10; $i++) {
        if (isset($fields[$i])) {
          $childType = $fields[$i]->type;
          /* recursion */
          $childDataArray = $this->convertStdObjectToArray($fields[$i]);
          foreach($childDataArray as $k => $v) {
            /* add childtype dot in front of field name */
            $data[$childType.'.'.$k] = $v;
          } // foreach
        } // if
      } // for
    } else if (isset($stdObj->sobjects)) {
      /* recursion: extract nested structure */
      $sobjects = $stdObj->sobjects;
      return($this->convertStdObjectToArray(current($sobjects)));
    } // else
    $this->convertStdObjectDataFormat($data);
    return($data);
  } // convertStdObjectToArray

  private function convertStdObjectDataFormat(&$data) {
    $fieldDetailsArray = $this->getIndexedFieldDetailsArray();
    foreach($data as $fieldName => $fieldValue) {
      if (!isset($fieldDetailsArray[$fieldName])) continue;
      $fieldDetails = $fieldDetailsArray[$fieldName];
      if ($fieldDetails->type == self::SCHEMA_FIELD_TYPE_DATE_TIME) {
        $data[$fieldName] = strtotime($fieldValue);
      } // if
    } // foreach
  } // convertStdObjectDataFormat

  public function selectSomeAsArrayInitialBatch(&$queryLocator, $filter,
      $fieldNameList=null, $orderBy=null) {
    return($this->_selectSomeAndConvert($queryLocator, false, $filter,
      $fieldNameList, $orderBy));
  } // selectSomeAsArrayInitialBatch

  public function selectSomeAsArraySubsequentBatch(&$queryLocator) {
    return($this->_convertSObjectArray(
      $this->executeSoqlQueryLocator($queryLocator)));
  } // selectSomeAsArraySubsequentBatch

  public function selectSomeAsArray($filter, $fieldNameList=null,
      $orderBy=null) {
    /* queryLocator is not returned */
    $queryLocator = null;
    return($this->_selectSomeAndConvert($queryLocator, true, $filter,
      $fieldNameList, $orderBy));
  } // selectSomeAsArray

  private function _selectSomeAndConvert(&$queryLocator, $returnEverything,
      $filter, $fieldNameList=null, $orderBy=null) {
    return($this->_convertSObjectArray( 
      $this->selectSome($queryLocator, $returnEverything,
        $filter, $fieldNameList, $orderBy)));
  } // _selectSomeAndConvert

  private function _convertSObjectArray($sObjectArray) {
    $dataArray = Array();
    if (!empty($sObjectArray)) {
      foreach($sObjectArray as $sObject) {
        $dataArray[] = $this->convertStdObjectToArray($sObject);
      } // foreach
    } // if
    return($dataArray);
  } // _selectSomeAndConvert

  /**
   *  Handles one result from upsert.
   *  @param Array stat	Array of upsert statistics
   *  @param mixed result	Sforce upsert result
   *  @return void	Values in stat will be modified.
   */
  private function evaluateOneResult(&$stat, $result) {
    if ($result->success) {
      if (isset($result->created) && ($result->created==true)) {
        /* successful created */
        $stat[self::RESULT_CREATED] += 1;
      } else {
        /* successful updated */
        $stat[self::RESULT_UPDATED] += 1;
      } // else
    } else {
      $stat[self::RESULT_FAILED] += 1;
      /* add the error message of this result to the error message array */
      if (is_null($stat[self::RESULT_ERROR_MESSAGES])) {
        $stat[self::RESULT_ERROR_MESSAGES] = Array();
        $stat[self::RESULT_ERROR_MESSAGES][] = $result->errors->message;
      } // if
    } // else
  } // evaluateOneResult

  /**
   *  Returns an array of 4 elements:
   *    number created
   *    number updated
   *    number failed
   *    array of error messages
   *
   *  @param results Return result from upsert
   *  @return Array of 3 integers plus an array of error messages
   */
  private function formatUpsertResults($results) {
    $stat = Array();
    $stat[self::RESULT_CREATED] = 0;
    $stat[self::RESULT_UPDATED] = 0;
    $stat[self::RESULT_FAILED] = 0;
    $stat[self::RESULT_ERROR_MESSAGES] = null;
    if (empty($results)) return($stat);
    if (is_array($results)) {
      foreach ($results as $result) {
        $this->evaluateOneResult($stat, $result);
      } // foreach
    } else {
        $this->evaluateOneResult($stat, $results);
    } // else
    return($stat);
  } // formatUpsertResults

  /*
    First, you must have declared an external id on a custom field either already created
    or one that is being created. Please understand that an external id has to be one of
    the following field types:
      1) Text
      2) Number
      3) Email
      4) Auto Number

    As a sforce administrator, you login to Salesforce.com and go to 
    Setup > App Setup > Customize > $this->tableName > Fields. Then scroll down to Custom Fields 
    and Relationships. Either Create a new field that you will be utilizing or edit an 
    existing field which is a valid field type and make it an external field. 
  */
  /**
   *  Upsert an array of SObjects using a specified external id field.
   *  @param string externalIdFieldName	An external id field
   *  @param array SObject array
   */
  public function upsertBySObjectArray($externalIdFieldName, $sObjectArray,
    $formatResults=false) {
CecLogger::logDebug($sObjectArray, 'upsertBySObjectArray.sObjectArray');
    $results = $this->sforceConnection->upsert($externalIdFieldName, $sObjectArray);
CecLogger::logDebug($results, 'upsertBySObjectArray.results');
    if ($formatResults) {
      return($this->formatUpsertResults($results));
    } else {
      return($results);
    } // else
  } // upsertBySObjectArray

  /**
   *  Upsert one object.
   */
  public function upsertBySObject($externalIdFieldName, $sObject,
      $formatResults=false) {
    $sObjectArray = Array();
    $sObjectArray[] = $sObject;
    return($this->upsertBySObjectArray($externalIdFieldName, $sObjectArray,
      $formatResults));
  } // upsertBySObject

  /**
   *  Update an array of SObjects using a specified external id field.
   *  @param string externalIdFieldName	An external id field
   *  @param array SObject array
   */
  public function updateBySObjectArray($sObjectArray, $formatResults=false) {
CecLogger::logDebug($sObjectArray, 'upsertBySObjectArray.sObjectArray');
    $results = $this->sforceConnection->update($sObjectArray);
CecLogger::logDebug($results, 'upsertBySObjectArray.results');
    if ($formatResults) {
      return($this->formatUpsertResults($results));
    } else {
      return($results);
    } // else
  } // updateBySObjectArray

  /**
   *  Update one object.
   */
  public function updateBySObject($sObject, $formatResults=false) {
    $sObjectArray = Array();
    $sObjectArray[] = $sObject;
    return($this->updateBySObjectArray($sObjectArray, $formatResults));
  } // updateBySObject

  /**
   *  Creates a new SObject appropriate as a child relation reference object.
   *  @param string objectType	Sforce table name
   *  @param mixed objectId	Sforce 'Id'
   *  @param Array fieldNameValueArray	Array of (fieldName => value) used to uniquely identify the referenced object.
   *  @return SObject	A child reference SObject
   */
  private function newReferenceSObject($objectType, $objectId,
      $fieldNameValueArray=null) {
    if (empty($objectId)) return(null);
    $sObject = new SObject();
    //$sObject->Id = Array($objectId, $objectId);
    $sObject->Id = $objectId;
    $sObject->type = $objectType;
    $sObject->fields = $fieldNameValueArray;
    return($sObject);
  } // newReferenceSObject

  static protected function timestampToString($timestamp) {
    return(date("c", $timestamp));
  } // timestampToString

  /**
   *  Transform a field name and value to a SObject field.
   *  @param Array	SObject field array
   *  @param string fieldName	Sforce field name
   *  @param mixed value	Sforce field value in native PHP syntax
   *  @param string externalIdFieldName	Sforce external ID field name
   *  @return void	A field in the fieldArray will be added
   */
  private function transformDataToSObjectField(&$fieldArray, $fieldName, $value,
      $externalIdFieldName) {
    if ($fieldName == self::FIELD_ID) {
      $fieldArray[$fieldName] = $value;
      return(true);
    } // if

    $fieldDetails = $this->getIndexedFieldDetailsArray();
    if (empty($fieldDetails[$fieldName])) {
CecLogger::logDebug("transformDataToSObjectField cannot find details of field ".$fieldName);
      return(false);
    } // if
    $field = $fieldDetails[$fieldName];

    /* do not insert if not updateable */
    if (empty($field->updateable) && ($fieldName != $externalIdFieldName)) {
CecLogger::logDebug($fieldName." not updateable");
      return(false);
    } // if

if (is_array($value)) {
  CecLogger::logDebug($value, 'transformDataToSObjectField array in field '.$fieldName);
} // if

    /* transform to xsd format */
    switch(strtoupper($field->type)) {
    case 'REFERENCE':
    case 'STRING':
    case 'TEXTAREA':
      $newValue = htmlspecialchars($value);
      break;
    case 'DATE':
    case 'DATETIME':
      $newValue = self::timestampToString(strtotime($value));
      break;
    case 'DOUBLE':
      $newValue = $value;
      break;
    default:
      $newValue = $value;
    } // switch
    $fieldArray[$fieldName] = $newValue;
    return(true);
  } // transformDataToSObjectField

  /**
   *  Transform an array of (fieldName => value) to an SObject.
   *  @param array Array of (fieldName => value)
   *  @param string externalIdFieldName	An external ID field name
   *  @return SObject
   */
  private function transformDataToSObject($data, $externalIdFieldName) {
    $sObject = new SObject();
    $sObject->type = $this->getTableName();
    $sObjectFieldArray = Array();
    /* reformat as necessary */
    foreach($data as $fieldName => $value) {
      if (is_null($value)) {
        $sObject->fieldsToNull[] = $fieldName;
      } else {
        $status = $this->transformDataToSObjectField($sObjectFieldArray, $fieldName,
          $value, $externalIdFieldName);
      } // else
    } // foreach
    if (!empty($sObjectFieldArray)) {
      $sObject->fields = $sObjectFieldArray;
    } // if
    return($sObject);
  } // transformDataToSObject

  private function transformDataArrayToSObjectArray($externalIdFieldName, $dataArray) {
    if (empty($dataArray)) return;
    $sObjectArray = Array();
    foreach($dataArray as $data) {
      $sObject = $this->transformDataToSObject($data, $externalIdFieldName);
      if (empty($sObject)) continue;
      $sObjectArray[] = $sObject;
    } // foreach
    return($sObjectArray);
  } // transformDataArrayToSObjectArray

  /**
   *  Upsert an array of (fieldName => value) arrays using a specified
   *    external id field.
   *  @param string externalIdFieldName	An external id field
   *  @param array Array of (fieldName => value) arrays
   */
  public function upsertByDataArray($externalIdFieldName, $dataArray,
      $formatResults=false) {
    if (empty($dataArray)) return;
    $sObjectArray = $this->transformDataArrayToSObjectArray(
      $externalIdFieldName, $dataArray);
    return($this->upsertBySObjectArray($externalIdFieldName, $sObjectArray,
      $formatResults));
  } // upsertByDataArray

  /**
   *  Upsert an object of (fieldName => value) using a specified
   *    external id field.
   *  @param string externalIdFieldName	An external id field
   *  @param array (fieldName => value) array
   *
   */
  public function upsertByData($externalIdFieldName, $dataObject,
      $formatResults=false) {
    $sObject = $this->transformDataToSObject($dataObject, $externalIdFieldName);
    return($this->upsertBySObject($externalIdFieldName, $sObject,
      $formatResults));
  } // upsertByData

  /**
   *  Update an array of (fieldName => value) arrays using a specified
   *    external id field.
   *  @param string externalIdFieldName	An external id field
   *  @param array Array of (fieldName => value) arrays
   */
  public function updateByDataArray($externalIdFieldName, $dataArray,
      $formatResults=false) {
    if (empty($dataArray)) return;
    $sObjectArray = $this->transformDataArrayToSObjectArray(
      $externalIdFieldName, $dataArray);
    return($this->updateBySObjectArray($sObjectArray, $formatResults));
  } // updateByDataArray

  /**
   *  Update an object of (fieldName => value) using a specified
   *    external id field.
   *  @param string externalIdFieldName	An external id field
   *  @param array (fieldName => value) array
   *
   */
  public function updateByData($externalIdFieldName, $dataObject,
      $formatResults=false) {
    $sObject = $this->transformDataToSObject($dataObject, $externalIdFieldName);
    return($this->updateBySObject($sObject, $formatResults));
  } // updateByData

  /**
   *  Generate SQL of one field in the create table command
   *  @return string	SQL fragment to create one field
   */
  private function formatOneFieldSchema($field) {
    if (!empty($field->calculated)) return(null);
    /* leading spaces just for cosmetics */
    $str = self::SCHEMA_FIELD_INDENT_SPACES.$field->name;
    switch (strtoupper($field->type)) {
    case 'ID':
      $str .= ' VARCHAR(18)';
      break;
    case 'INT':
      $str .= ' INT';
      break;
    case 'STRING':
      $str .= ' VARCHAR('.$field->length.')';
      break;
    case 'TEXTAREA':
      $str .= ' TEXT';
      break;
    case 'PHONE':
      $str .= ' VARCHAR(120)';
      break;
    case 'URL':
      $str .= ' VARCHAR(512)';
      break;
    case 'CURRENCY':
      $str .= ' DOUBLE PRECISION';
      break;
    case 'DATE':
      $str .= ' DATE';
      break;
    case 'DATETIME':
      $str .= ' DATETIME';
      break;
    case 'REFERENCE':
      $str .= ' VARCHAR(18)';
      break;
    case 'PICKLIST':
      $str .= ' VARCHAR(120)';
      break;
    case 'EMAIL':
      $str .= ' VARCHAR(120)';
      break;
    default:
      $str .= ' VARCHAR(120)';
    } // switch
    if (empty($field->nillable)) {
      $str .= ' NOT NULL';
    } // if
    if (!empty($field->defaultedOnCreate)) {
      //$str .= ' DEFAULT '.$field->defaultedOnCreate;
    } // if
    return($str);
  } // formatOneFieldSchema

  /**
   *  Generate SQL of one primary key in the create table command
   *  @return string	SQL fragment to create one primary key
   */
  private function formatOnePrimaryKey($field) {
    if (strtoupper($field->type) == self::FIELD_TYPE_ID) {
      return(self::SCHEMA_FIELD_INDENT_SPACES.'PRIMARY KEY ('.$field->name.')');
    } else {
      return(null);
    } // else
  } // formatOnePrimaryKey

  /**
   *  Generates a string that can be used in MySQL to create this table.  It reads
   *    the schema data from Sforced, meaning this schema is subjected to field-level
   *    security of Sforce.
   *  @return string	String of MySQL create table command
   */
  public function generateMysqlTableSchema() {
    $metaData = $this->getTableSchema();
    $fields = $metaData->fields;
    $fieldList = Array();
    $primaryKeyList = Array();
    foreach ($fields as $field) {
      $fieldSpec = $this->formatOneFieldSchema($field);
      if (!empty($fieldSpec)) {
        $fieldList[] = $fieldSpec;
      } // if
      $primaryKeySpec = $this->formatOnePrimaryKey($field);
      if (!empty($primaryKeySpec)) {
        $primaryKeyList[] = $primaryKeySpec;
      } // if
    } // foreach

    $str = "\n--\n-- ".$this->tableName."\n--"
      ."\nCREATE TABLE IF NOT EXISTS ".$this->tableName."(\n"
      .implode(self::COMMA_EOL,$fieldList);
    if (count($primaryKeyList) > 0) {
      $str .= self::COMMA_EOL.implode(self::COMMA_EOL, $primaryKeyList);
    } // if
    $str .= "\n) TYPE=InnoDB;\n";
    return($str);
  } // generateMysqlTableSchema

  static public function quoteSoqlString($str) {
    return(self::SOQL_QUOTE.addslashes($str).self::SOQL_QUOTE);
  } // quoteSoqlString

  protected function newSelectStatement() {
    $statement = new CecSforceSoqlSelect($this);
    return($statement);
  } // newSelectStatement

  public function generateSelectIdSoql($filter=null) {
    return($this->generateSelectSoql(self::FIELD_ID, $filter));
  } // generateSelectIdSoql

  public function generateSelectSoql($fields, $filter=null, $limit=null) {
    $stmt = $this->newSelectStatement();
    $stmt->setSelectFieldArray($fields);
    $stmt->setWhere($filter);
    $stmt->setLimit($limit);
    return($stmt->toString());
  } // generateSelectIdSoql

  static protected function parentFieldName($fieldName) {
    return('parent.'.$fieldName);
  } // parentFieldName

  static private function _generateInClause($fieldName, $values,
      $inClause, $quoteString=true) {
    if (is_array($values)) {
      $valueStr = null;
      foreach($values as $v) {
        if (!is_null($valueStr)) {
          $valueStr .= ',';
        } // if
        if ($quoteString) {
          $valueStr .= self::quoteSoqlString($v);
        } else {
          $valueStr .= $v;
        } // else
      } // foreach
      $values = $valueStr;
    } else {
      if ($quoteString) {
        $values = self::quoteSoqlString($values);
      } // if
    } // else

    /* format return string */
    $str = $fieldName;
    if ($inClause) {
      $str .= ' IN ';
    } else {
      $str .= ' NOT IN ';
    } // else
    $str .= ' ('.$values.')';
    return($str);
  } // _generateInClause

  static protected function generateInClause($fieldName,
      $values, $quoteString=true) {
    return(self::_generateInClause($fieldName, $values,
      true, $quoteString));
  } // generateInClause

  static protected function generateNotInClause($fieldName,
      $values, $quoteString=true) {
    return(self::_generateInClause($fieldName, $values,
      false, $quoteString));
  } // generateNotInClause

  protected function generateLaterThanClause($fieldName, $timestamp) {
    if (empty($timestamp)) return(null);
    return($fieldName.'>'.self::timestampToString($timestamp));
  } // generateLaterThanClause

  private function _selectByLaterThan($fieldName, $timestamp,
      $fieldNameList=null) {
    $filter = self::generateLaterThanClause(self::FIELD_CREATED_DATE,
      $timestamp);
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    return($this->selectSomeAsArray($filter, $fieldNameList));
  } // _selectByLaterThan

  public function selectByCreatedLaterThan($timestamp, $fieldNameList=null) {
    return(self::_selectByLaterThan(self::FIELD_CREATED_DATE,
      $timestamp, $fieldNameList));
  } // selectByCreatedLaterThan
 
  public function selectByUserModifiedLaterThan($timestamp, $fieldNameList=null) {
    return(self::_selectByLaterThan(self::FIELD_LAST_MODIFIED_DATE,
      $timestamp, $fieldNameList));
  } // selectByUserModifiedLaterThan

  public function selectBySystemModifiedLaterThan($timestamp, $fieldNameList=null) {
    return(self::_selectByLaterThan(self::FIELD_SYSTEM_MODSTAMP,
      $timestamp, $fieldNameList));
  } // selectBySystemModifiedLaterThan

  private function _implodeFilterArray($filterArray, $glueWord) {
    if (is_null($filterArray)) return(null);
    if (count($filterArray) == 0) return(null);
    $strippedArray = Array();
    foreach($filterArray as $filter) {
      $f = trim($filter);
      if (empty($f)) continue;
      $strippedArray[] = '('.$f.')';
    } // foreach
    if (count($strippedArray) == 0) return(null);
    return('('.implode(' '.$glueWord.' ', $strippedArray).')');
  } // _implodeFilterArray

  public function implodeAndFilterArray($filterArray) {
    return($this->_implodeFilterArray($filterArray, 'AND'));
  } // implodeAndFilterArray

  public function implodeOrFilterArray($filterArray) {
    return($this->_implodeFilterArray($filterArray, 'OR'));
  } // implodeOrFilterArray

  public function selectOneByIdAsArray($id, $fieldNameList=null) {
    $filter = self::FIELD_ID.'='.self::quoteSoqlString($id);
    $dataArray = $this->selectSomeAsArray($filter, $fieldNameList);
    if (empty($dataArray)) return(null);
    reset($dataArray);
    return(current($dataArray));
  } // selectOneByIdAsArray

  public function createCustomField($customField) {
    $this->sforceConnection->createMetadata($customField);
  } // createCustomField

  public function createCustomFields($tableDetails) {
    $sObjectName = $this->getTableName();
    $existingSchemaFieldList = $this->getFieldNameList();
    $suffixLen = strlen(self::SFORCE_CUSTOM_NAME_SUFFIX);
    foreach ($tableDetails as $columnName => $columnDetails) {
      /* add sforce customization suffix */
      if (substr($columnName, -$suffixLen) != 
          self::SFORCE_CUSTOM_NAME_SUFFIX) {
        $columnName .= self::SFORCE_CUSTOM_NAME_SUFFIX;
      } // if

      /* do nothing if this column already exists */
      if (!is_null($existingSchemaFieldList)) {
        if (in_array($columnName, $existingSchemaFieldList)) continue;
      } // if

      $customField= new CecSforceCustomField();
      /* prefix the full name by the table name */
      $fullName = $sObjectName.'.'.$columnName;
      $customField->setFullName($fullName);
      $customField->setLabel($columnName);
      foreach($columnDetails as $field => $value) {
        $functionName = 'set'.$field;
        if (strtoupper($value) == 'TRUE') {
          $customField->$functionName(TRUE);
        } else if (strtoupper($value) == 'FALSE') {
          $customField->$functionName(FALSE);
        } else {
          $customField->$functionName($value);
        } // else
      } // foreach
      $results = $this->createCustomField($customField);
CecLogger::logDebug($results, "createCustomFields.results");
    } // foreach
  } // createCustomFields

  protected function createCustomObject($customObject) {
    return($this->sforceConnection->createMetadata($customObject));
  } // createCustomObject

} // CecSforceTable
?>
