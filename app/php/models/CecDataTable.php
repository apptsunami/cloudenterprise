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
/* models/CecDataTable.php */
$rDir = '';
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/helpers/CecHelperString.php');
require_once($rDir.'cec/php/models/CecSqlDelete.php');
require_once($rDir.'cec/php/models/CecSqlInsert.php');
require_once($rDir.'cec/php/models/CecSqlInsertSubselect.php');
require_once($rDir.'cec/php/models/CecSqlSelect.php');
require_once($rDir.'cec/php/models/CecSqlUpdate.php');
require_once($rDir.'cec/php/models/CecSqlStatement.php');
require_once($rDir.'php/cec/CecLogger.php');

class CecDataTable {
  /* constants */
  const FIELD_OBJID = 'objid';
  const FIELD_TABLE_NAME = 'cecTableName';
  const FIELD_SUBCLASS = 'cecSubclass';

  const RANDOM_SORT_ORDER = 'RAND()';
  const FUNCTION_NOW = 'NOW()';
  const FUNCTION_UNIX_NOW = 'UNIX_TIMESTAMP(NOW())';
  const FUNCTION_MD5 = 'MD5';
  const FUNCTION_PASSWORD = 'PASSWORD';
  const FUNCTION_CONCAT = 'CONCAT';
  const FUNCTION_REPLACE = 'REPLACE';
  const FUNCTION_DATE_ADD = 'DATE_ADD';

  /* for the date_add function */
  const INTERVAL_MICROSECOND  = 'MICROSECOND';
  const INTERVAL_SECOND  = 'SECOND';
  const INTERVAL_MINUTE  = 'MINUTE';
  const INTERVAL_HOUR  = 'HOUR';
  const INTERVAL_DAY  = 'DAY';
  const INTERVAL_WEEK  = 'WEEK';
  const INTERVAL_MONTH  = 'MONTH';
  const INTERVAL_QUARTER  = 'QUARTER';
  const INTERVAL_YEAR  = 'YEAR';
  const INTERVAL_SECOND_MICROSECOND  = 'SECOND_MICROSECOND';
  const INTERVAL_MINUTE_MICROSECOND  = 'MINUTE_MICROSECOND';
  const INTERVAL_MINUTE_SECOND  = 'MINUTE_SECOND';
  const INTERVAL_HOUR_MICROSECOND  = 'HOUR_MICROSECOND';
  const INTERVAL_HOUR_SECOND  = 'HOUR_SECOND';
  const INTERVAL_HOUR_MINUTE  = 'HOUR_MINUTE';
  const INTERVAL_DAY_MICROSECOND  = 'DAY_MICROSECOND';
  const INTERVAL_DAY_SECOND  = 'DAY_SECOND';
  const INTERVAL_DAY_MINUTE  = 'DAY_MINUTE';
  const INTERVAL_DAY_HOUR  = 'DAY_HOUR';
  const INTERVAL_YEAR_MONTH = 'YEAR_MONTH';

  const STR_NULL = 'NULL';
  const STR_IS_NULL = 'IS NULL';
  const STR_IS_NOT_NULL = 'IS NOT NULL';
  const OPERATOR_IN = 'IN';
  const OPERATOR_NOT_IN = 'NOT IN';

  const TIME_UNIT_YEAR = 'YEAR';
  const TIME_UNIT_MONTH = 'MONTH';
  const TIME_UNIT_DAY = 'DAY';

  const TABLE_ALIAS_FIELD_NAME_SEPARATOR = '.';
  const ORDER_BY_FIELD_SEPARATOR = ',';
  const EMPTY_JSON_STRING = '[ ]';
  const ERROR_MISSING_CHILD_METHOD = 'Error: missing child method';
  const ERROR_MISSING_TABLE_NAME = 'Error: missing table name';
  const ERROR_MISSING_FIELD_NAMES = 'Error: missing field names';
  const MYSQL_TRUE = "\x1";
  const MYSQL_FALSE = "\x0";
  const BOOLEAN_VALUE_TRUE = 1;
  const BOOLEAN_VALUE_FALSE = 0;
  const INVALID_OBJID = 0;

  const CUSTOM_FIELD_NAME_PREFIX = 'x_';
  const CUSTOM_FIELD_TABLE_NAME_SUFFIX = '_x';
  const SQL_COMMENT_PATTERN = '/^--(.*)$/m';
  const EMPTY_LINE_PATTERN = "/[\r\n]+[\s\t]*[\r\n]+/m";
  const NEW_LINE_PATTERN = "/([\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/m";
  const SQL_SEMICOLON_PATTERN = "/;[\r\n]+/m";

  /* constants from others */
  const ON_DUPLICATE_NOOP = CecSqlInsert::ON_DUPLICATE_NOOP;
  const ON_DUPLICATE_ASSIGN = CecSqlInsert::ON_DUPLICATE_ASSIGN;
  const ON_DUPLICATE_ADD = CecSqlInsert::ON_DUPLICATE_ADD;
  const ON_DUPLICATE_SUBTRACT = CecSqlInsert::ON_DUPLICATE_SUBTRACT;
  const ON_DUPLICATE_CONCAT = CecSqlInsert::ON_DUPLICATE_CONCAT;

  /* variables */
  private $saveDebug;

  protected $dbConnect;
  protected $fieldNameList;
  protected $tableName;
  protected $defaultSortOrder;
  protected $subclass;
  protected $limitCount;
  protected $limitOffset;
  protected $returnTableNameFlag;
  protected $customFieldTableName;
  protected $autoIncrementFieldName;

  /* methods */
  public function __construct($dbConnect, $tableName, $fieldNameList,
      $subclass=null, $autoIncrementFieldName=self::FIELD_OBJID,
      $findCustomFieldTable=FALSE) {
    $this->dbConnect = $dbConnect;
    $this->tableName = $tableName;
    $this->fieldNameList = $fieldNameList;
    $this->subclass = $subclass;
    $this->returnTableNameFlag = FALSE;
    $this->autoIncrementFieldName = $autoIncrementFieldName;
    $this->defaultSortOrder = $autoIncrementFieldName;
    if ($findCustomFieldTable) {
      $this->customFieldTableName = $this->findCustomFieldTableName();
    } else {
      $this->customFieldTableName = null;
    } // else
  } // __construct

  protected function findCustomFieldTableName() {
    $custTableName = $this->tableName.self::CUSTOM_FIELD_TABLE_NAME_SUFFIX;
    $countColumnName = 'ctr';
    $sql = 'SELECT COUNT(*) AS '.$countColumnName
      .' FROM information_schema.tables WHERE table_schema="'
      .$this->getDbConnect()->getDatabaseName()
      .'" AND table_name="'.$custTableName.'"';
    $result = $this->executeSql($sql);
    $row = $this->dbConnect->convertResultToOneRow($result);
    $count = $row[$countColumnName];
    if ($count == 0) return(null);
    return($custTableName);
  } // findCustomFieldTableName

  /* getters */
  public function getDbConnect() {
    return($this->dbConnect);
  } // getDbConnect

  public function getTableName() {
    return($this->tableName);
  } // getTableName

  public function getAutoIncrementFieldName() {
    return($this->autoIncrementFieldName);
  } // getAutoIncrementFieldName

  public function getLimitCount() {
    if (isset($this->limitCount)) {
       return($this->limitCount);
    } else {
       return(null);
    } // else
  } // getLimitCount

  public function getLimitOffset() {
    if (isset($this->limitOffset)) {
       return($this->limitOffset);
    } else {
       return(null);
    } // else
  } // getLimitOffset

  protected static function implodeSortOrder($sortOrder) {
    if (is_array($sortOrder)) {
      return(implode(',', $sortOrder));
    } else {
      return($sortOrder);
    } // else
  } // implodeSortOrder

  /* setters */
  public function setDefaultSortOrder($sortOrder) {
    $this->defaultSortOrder = self::implodeSortOrder($sortOrder);
  } // setDefaultSortOrder

  public function setDefaultSortOrderToRandom() {
    $this->setDefaultSortOrder(self::RANDOM_SORT_ORDER);
  } // setDefaultSortOrderToRandom

  public function setLimitCount($count) {
    $this->limitCount = $count;
  } // setLimitCount

  public function setLimitOffsetAndCount($offset, $count) {
    $this->limitOffset = $offset;
    $this->limitCount = $count;
  } // setLimitOffsetAndCount

  public function setReturnTableNameFlag($returnTableNameFlag) {
    $this->returnTableNameFlag = $returnTableNameFlag;
  } // setReturnTableNameFlag

  /* methods */
  public function enableDebug() {
    $this->saveDebug = $this->dbConnect->debug;
    $this->dbConnect->debug = Zend_Log::DEBUG;
  } // enableDebug

  public function disableDebug() {
    if (isset($this->saveDebug)) {
      $this->dbConnect->debug = $this->saveDebug;
    } else {
      $this->dbConnect->debug = Zend_Log::WARN;
    } // else
  } // disableDebug

  public function restoreDebug() {
    $this->dbConnect->debug = $this->saveDebug;
  } // restoreeDebug

  static public function wrapNull($str) {
    if (!is_array($str)) {
      return(is_null($str)?self::STR_NULL:$str);
    } else {
      $returnArray = Array();
      foreach($str as $s) {
        $returnArray[] = self::wrapNull($s);
      } // foreach
      return($returnArray);
    } // else
  } // wrapNull

  static public function wrapNullAndQuote($str) {
    if (!is_array($str)) {
      return(is_null($str)?self::STR_NULL:'"'.mysql_real_escape_string($str).'"');
    } else {
      $returnArray = Array();
      foreach($str as $s) {
        $returnArray[] = self::wrapNullAndQuote($s);
      } // foreach
      return($returnArray);
    } // else
  } // wrapNullAndQuote

  protected function wrapTrimStringArray($strArray) {
    if (!is_array($strArray)) {
      return($this->wrapNullAndQuote(trim($strArray)));
    } // if

    $dl = Array();
    foreach($strArray as $str) {
      $dl[] = $this->wrapNullAndQuote(trim($str));
    } // foreach
    return($dl);
  } // wrapStringArray

  static protected function isNotNullClause($expr) {
    return($expr.' '.self::STR_IS_NOT_NULL);
  } // isNotNullClause

  static protected function isNullClause($expr) {
    return($expr.' '.self::STR_IS_NULL);
  } // isNullClause

  static protected function ifClause($testExpr, $whenTrue, $whenFalse) {
    return('IF('.$testExpr.','.$whenTrue.','.$whenFalse.')');
  } // ifClause

  static protected function generateNotFilter($filter) {
    return('NOT('.$filter.')');
  } // generateNotFilter

  static protected function tableDotFieldName($tableName, $fieldName) {
    return($tableName.self::TABLE_ALIAS_FIELD_NAME_SEPARATOR.$fieldName);
  } // tableDotFieldName

  public function laterThanNow($dateTime) {
    return('(('.self::isNotNullClause($dateTime).') AND ('.$dateTime.' >= '
      .self::FUNCTION_NOW.'))');
  } // laterThanNow

  public function notLaterThanNow($dateTime) {
    return('(('.self::isNullClause($dateTime).') OR ('.$dateTime.' < '
      .self::FUNCTION_NOW.'))');
  } // notLaterThanNow

  static protected function generateReplaceExpr($arg, $search, $replacement) {
    return(self::FUNCTION_REPLACE."(".$arg.",".$search.",".$replacement.")");
  } // generateReplaceExpr

  static protected function generateConcatExpr($argArray) {
    if (empty($argArray)) return(null);
    if (!is_array($argArray)) return($argArray);
    return(self::FUNCTION_CONCAT."(".implode(',', $argArray).")");
  } // generateConcatExpr

  static protected function bitwiseAnd($op1, $op2) {
    return $op1.'&'.$op2;
  } // bitwiseAnd

  static protected function bitwiseOr($op1, $op2) {
    return $op1.'|'.$op2;
  } // bitwiseOr

  static private function _formatFieldListAsString($fieldNameList) {
    if (!is_array($fieldNameList)) return($fieldNameList);
    $str = '';
    foreach ($fieldNameList as $f) {
      if (!empty($str)) {
        $str .= ',';
      } // if
      $str .= self::escape($f);
    } // foreach
    return $str;
  } // _formatFieldListAsString

  protected function getFieldListAsString() {
    return(self::_formatFieldListAsString($this->fieldNameList));
  } // getFieldListAsString

  protected function getSelectPhrase($fieldNameList=null, $distinct=false) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->fieldNameList;
    } // if
    $str = 'SELECT ';
    if ($distinct) $str .= 'DISTINCT ';
    return($str.$this->_formatFieldListAsString($fieldNameList));
  } // getSelectPhrase

  protected function executeSql($sql) {
    if (is_null($sql)) return(null);
    if (!is_array($sql)) {
      return($this->dbConnect->query($sql));
    } else {
      return($this->executeTransaction($sqlArray));
    }
  } // executeSql

  protected function executeSqlAndReturnRowsAffected($sql) {
    return($this->dbConnect->countExecuteAffected($sql));
  } // executeSql

  protected function convertResultToObjid($result) {
    $objidRow = $this->dbConnect->convertDatabaseResultToList($result, TRUE);
    if (count($objidRow) == 0) return(null);
    return(current($objidRow));
  } // convertResultToObjid

  protected function executeSqlAndReturnNewId($sql) {
    $AS_LAST_ID = ' AS LAST_ID';
    if (!is_array($sql)) {
      $sqlArray = Array();
      $sqlArray[] = $sql;
      $sqlArray[] = CecDbConnect::SELECT_LAST_INSERT_ID.$AS_LAST_ID;
      $result = $this->executeTransaction($sqlArray);
    } else {
      $sql[] = CecDbConnect::SELECT_LAST_INSERT_ID.$AS_LAST_ID;
      $result = $this->executeTransaction($sql);
    } // else
    return($this->convertResultToObjid($result));
  } // executeSqlAndReturnNewId

  protected function executeTransaction($sqlArray) {
    return($this->dbConnect->transaction($sqlArray));
  } // executeTransaction

  public function injectSubclassIntoFilter($filter) {
    if (empty($this->fieldNameList)) return($filter);

    if (!in_array(self::FIELD_SUBCLASS, $this->fieldNameList)) return($filter);
    $filterArray = Array();
    if (!is_null($filter)) {
      $filterArray[] = $filter;
    } // if
    $filterArray[] = $this->generateEqualToOrIsNullClause(
      self::FIELD_SUBCLASS, $this->subclass, FALSE);
    return($this->implodeAndFilterArray($filterArray));
  } // injectSubclassIntoFilter

  private function _selectFromWhere($filter, $sortOrder, $limitCount,
      $fieldNameArray=null, $distinctFlag=false, $limitOffset=null) {
    if (!isset($this->tableName)) {
CecLogger::logError(self::ERROR_MISSING_TABLE_NAME.':'.get_class($this)."\n");
      return(null);
    } // if
    if (is_null($fieldNameArray)) {
      if (!isset($this->fieldNameList)) {
CecLogger::logError(self::ERROR_MISSING_FIELD_NAMES.':'.get_class($this)."\n");
        return(null);
      } // if
    } // if
    $sql = $this->getSelectPhrase($fieldNameArray, $distinctFlag)
      .' FROM '.self::escape($this->tableName);
    if (!is_null($filter) && !empty($filter)) {
      $sql .= ' WHERE '.$filter;
    } // if
    if (!is_null($sortOrder) && !empty($sortOrder)) {
      $sql .= ' ORDER BY '.$sortOrder;
    } // if
    if (!is_null($limitCount)) {
      $sql .= ' LIMIT ';
      if (!is_null($limitOffset)) {
        $sql .= $limitOffset.',';
      } // if
      $sql.=$limitCount;
    } // if
    return($sql);
  } // _selectFromWhere

  public function fetchSome($filter=null, $sortOrder=null, $fieldNameArray=null,
      $distinctFlag=false) {
    if (is_null($sortOrder)) {
      if (isset($this->defaultSortOrder)) {
        $sortOrder = $this->defaultSortOrder;
      } // if
    } else {
      $sortOrder = self::implodeSortOrder($sortOrder);
    } // else
    $sql = $this->_selectFromWhere($this->injectSubclassIntoFilter($filter),
      (is_null($sortOrder)?
        (isset($this->defaultSortOrder)?$this->defaultSortOrder:null):$sortOrder),
      (isset($this->limitCount)?$this->limitCount:null), $fieldNameArray,
      $distinctFlag,
      (isset($this->limitOffset)?$this->limitOffset:null));
    return($this->executeSql($sql));
  } // fetchSome

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

  private function _convertDbResultToList($result) {
    if ($this->returnTableNameFlag === TRUE) {
      return($this->dbConnect->convertDatabaseResultToList($result,
        FALSE, $this->tableName));
    } else {
      return($this->dbConnect->convertDatabaseResultToList($result, true));
    } // else
  } // _convertDbResultToList

  public function fetchSomeAsArray($filter, $sortOrder=null, $andFilter=null,
      $fieldNameArray=null, $distinctFlag=false) {
    if (!is_null($andFilter)) {
      $filters = Array();
      $filters[] = $filter;
      $filters[] = $andFilter;
      $filter = $this->implodeAndFilterArray($filters);
    } // if
    $result = $this->fetchSome($filter, $sortOrder, $fieldNameArray,
      $distinctFlag);
    return($this->_convertDbResultToList($result));
  } // fetchSomeAsArray

  public function fetchBatchAsArray($batchSize, $filter, $sortOrder=null, 
      $andFilter=null, $fieldNameArray=null) {
    /* save limit */
    $prevLimitCount = $this->getLimitCount();
    $this->setLimitCount($batchSize);
    $results = $this->fetchSomeAsArray($filter, $sortOrder, $andFilter, $fieldNameArray);
    /* restore limit */
    $this->setLimitCount($prevLimitCount);
    return($results);
  } // fetchBatchAsArray

  public function fetchFirstAsArray($filter, $sortOrder=null) {
    return($this->getFirstOfArray(
      $this->fetchSomeAsArray($filter, $sortOrder)));
  } // fetchFirstAsArray

  static protected function getFirstOfArray($results) {
    if (is_null($results)) return(null);
    if (count($results) == 0) return(null);
    reset($results);
    return(current($results));
  } // getFirstOfArray

  public function fetchSomeSortedTextFilteredAsArray($sortField, $sortDesc, 
      $textFilter=null, $tokenize=FALSE) {
    $sortOrder = $this->generateSortOrder($sortField, $sortDesc);
    if (is_null($textFilter)) {
      $filter = null;
    } else {
      $filter = $this->generateTextContainsClause($textFilter, $tokenize);
    } // if
    return($this->fetchSomeAsArray($filter, $sortOrder));
  } // fetchSomeSortedTextFilteredAsArray

  public function fetchAll($excludeObjidArray=null, $sortOrder=null, $fieldNameArray=null, $distinctFlag=false) {
    $filter = null;
    if (!is_null($excludeObjidArray) && (count($excludeObjidArray)>0)) {
      $excludeFilter = implode(',',$excludeObjidArray);
      if (!empty($excludeFilter)) {
        $filter = $this->autoIncrementFieldName.' NOT IN('.$excludeFilter.')';
      } // if
    } // if
    return($this->fetchSome($this->injectSubclassIntoFilter($filter),
      $sortOrder, $fieldNameArray, $distinctFlag));
  } // fetchAll

  public function fetchAllAsArray($excludeObjidArray=null, $sortOrder=null,
      $fieldNameArray=null) {
    $result = $this->fetchAll($excludeObjidArray, $sortOrder, $fieldNameArray);
    return($this->_convertDbResultToList($result));
  } // fetchAllAsArray

  public function fetchOne($filter, $sortOrder=null, $fieldNameArray=null,
      $distinctFlag=false) {
    $result = $this->fetchSome($filter, $sortOrder, $fieldNameArray,
      $distinctFlag);
    if ($this->returnTableNameFlag === TRUE) {
      return($this->dbConnect->convertResultToOneRow($result, $this->tableName));
    } else {
      return($this->dbConnect->convertResultToOneRow($result));
    } // else
  } // fetchOne

  public function fetchOneByObjid($objid, $fieldNameArray=null) {
    if (is_null($objid)) return(null);
    $filter = $this->autoIncrementFieldName.'='.$objid;
    return($this->fetchOne($filter, null, $fieldNameArray));
  } // fetchOneByObjid

  public function fetchRandomOne($filter=null) {
    $sql = $this->_selectFromWhere($filter, self::RANDOM_SORT_ORDER, '1', false);
    $result = $this->executeSql($sql);
    return($this->dbConnect->convertResultToOneRow($result));
  } // fetchRandomOne

  public function selectOneObjidByFilter($filter) {
    $data = $this->fetchOne($filter);
    if (is_null($data)) return(null);
    return($data[$this->autoIncrementFieldName]);
  } // selectObjidByUrl

  public function generateSqlSetLastInsertId($var) {
    return('SET '.$var.'=LAST_INSERT_ID();');
  } // generateSqlSetLastInsertId

  public function generateStartTransactionSql() {
    return('START TRANSACTION;');
  } // generateStartTransactionSql

  public function generateCommitSql() {
    return('COMMIT;');
  } // generateCommitSql

  protected function jsonify($result) {
    if (!isset($this->fieldNameList) || !isset($this->tableName)) {
      return(self::ERROR_MISSING_CHILD_METHOD);
    } // if
    $str = '';
    while ($row = mysql_fetch_assoc($result)) {
      $str .= '[ "';
      $count = 0;
      foreach ($this->fieldNameList as $field) {
        if ($count > 0) $str .= ', ';
        $str .= '"'.$row[$field].'"';
        $count++;
      } // foreach
      $str .'" ],';
    } // while
    return($str);
  } // jsonify

  private function _jsonifyResult($result) {
    $str = '[ ';
    $count =0;
    while ($row = mysql_fetch_row($result)) {
      if ($count > 0) {
        $str .= ', ';
      } // if
      $str .= '[ "' ;
        for ($i=0; $i<count($row); $i++) {
          if ($i > 0) {
            $str .= '", "';
          } // if
          $str .= addslashes($row[$i]);
        } // for
      $str .= '" ]' ;
      $count++;
    } // while
    $str .= ' ]';
    return($str);
  } // _jsonifyResult

  public function jsonifyAll() {
    $result = $this->fetchAll();
    if (is_null($result)) {
      return(self::EMPTY_JSON_STRING);
    } // if
    $str = $this->_jsonifyResult($result);
    mysql_free_result($result);
    return($str);
  } // jsonifyAll

  public function joinArray($arr, $sepChar) {
    if (is_null($arr)) return('');
    if (count($arr)==0) return('');
    $str = null;
    $index =0;
    foreach ($arr as $objid) {
      if ($index > 0) {
        $str .= $sepChar;
      } // if
      $str .= $objid;
      $index++;
    } // foreach
    return($str);
  } // joinArray

  private function _generateInClause($operator, $fieldName, $value,
      $quoteValues=false) {
    if (is_null($value)) return(null);

    if (is_array($value)) {
      if (!$quoteValues) {
        $value = implode(',',$value);
      } else {
        $str = null;
        foreach($value as $v) {
          if (!is_null($str)) $str .= ',';
          $str .= $this->wrapNullAndQuote($v);
        } // foreach
        $value = $str;
      } // else
    } else {
      if ($quoteValues) {
        $value = $this->wrapNullAndQuote($value);
      } // if
    } // else
    $value = trim($value);
    if (is_null($value) || ($value == "")) return(null);
    return(self::escape($fieldName).' '.$operator.' (' .$value.')');
  } // _generateInClause

  protected function getInClause($fieldName, $value, $quoteValues=false) {
    return($this->_generateInClause(self::OPERATOR_IN, $fieldName, $value,
      $quoteValues));
  } // getInClause

  protected function getNotInClause($fieldName, $value, $quoteValues=false) {
    return($this->_generateInClause(self::OPERATOR_NOT_IN, $fieldName, $value,
      $quoteValues));
  } // getNotInClause

  public function injectSubclassIntoStatement($stmt) {
    if (empty($this->fieldNameList)) return;

    if (!in_array(self::FIELD_SUBCLASS, $this->fieldNameList)) return;
    if (is_null($this->subclass)) {
      $stmt->addStringFieldValuePair(self::FIELD_SUBCLASS, null);
    } else {
      $stmt->addStringFieldValuePair(self::FIELD_SUBCLASS, $this->subclass);
    } // else
  } // injectSubclassIntoStatement

  public function newDeleteStatement() {
    return(new CecSqlDelete($this));
  } // newDeleteStatement

  protected function newInsertStatement($ignore=1) {
    return(new CecSqlInsert($this, $ignore));
  } // newInsertStatement

  protected function newInsertSubselectStatement($ignore=1) {
    return(new CecSqlInsertSubselect($this, $ignore));
  } // newInsertSubselectStatement

  protected function newSelectStatement($tableAlias=null) {
    return(new CecSqlSelect($this, $tableAlias));
  } // newSelectStatement

  public function newUpdateStatement($ignore=1, $tableAlias=null,
      $additionalTableArray=null) {
    return(new CecSqlUpdate($this, $ignore, $tableAlias, $additionalTableArray));
  } // newUpdateStatement

  public function executeSqlSelect($sql, $convertSingularArray=TRUE,
      $useLimitOffsetAndCount=false) {
    if (empty($sql)) return null;
    if ($sql instanceof CecSqlStatement) {
      $sql = $sql->toString();
    } // if
    $sql = trim($sql, " ;");
    if (!is_null($this->limitCount)) {
      $sql .= ' LIMIT ';
      if (!is_null($this->limitOffset)) {
        $sql .= $this->limitOffset.',';
      } // if
      $sql.=$this->limitCount;
    } // if
    $result = $this->executeSql($sql);
    return $this->dbConnect->convertDatabaseResultToList($result,
      $convertSingularArray);
  } // executeSqlSelect

  protected function customizeUpdateByObjid($objid, &$stmt) {
    /* to be defined in child class */
    return;
  } // customizeUpdateByObjid

  protected function generateAutoIncrementFieldEqualClause($objid) {
    if (is_array($objid)) {
      return($this->getInClause($this->autoIncrementFieldName, $objid));
    } else {
      return($this->autoIncrementFieldName.'='.$objid);
    } // else
  } // generateAutoIncrementFieldEqualClause

  protected function bindParamToStatement($stmt, $field, $value) {
    if (is_numeric($value)) {
      $stmt->addNonStringFieldValuePair($field, $value);
    } else {
      if (($value === '') &&
          $this->updateEmptyStringMeansNull($field)) {
        $stmt->addStringFieldValuePair($field, null);
      } else {
        $stmt->addStringFieldValuePair($field, $value);
      } // else
    } // else
  } // bindParamToStatement

  private function bindParamArrayToStmt($stmt, $paramArray) {
    if (!is_array($paramArray)) return;
    foreach($paramArray as $field => $value) {
      if ($field == $this->autoIncrementFieldName) continue;
      if (array_search($field, $this->fieldNameList)===FALSE) continue;
      $this->bindParamToStatement($stmt, $field, $value);
    } // foreach
  } // bindParamArrayToStmt

  public function generateUpdateByObjidStatement($objid, $paramArray,
      $additionalFilter=null) {
    if (count($paramArray) == 0) return(null);
    $stmt = $this->newUpdateStatement();
    $filters = Array();
    if (!empty($objid)) {
      $filters[] = $this->generateAutoIncrementFieldEqualClause($objid);
    } // if
    if (!empty($additionalFilter)) {
      $filters[] = $additionalFilter;
    } // if
    $stmt->setWhereClause($this->implodeAndFilterArray($filters));
    $this->bindParamArrayToStmt($stmt, $paramArray);
    $this->customizeUpdateByObjid($objid, $stmt);
    return($stmt);
  } // generateUpdateByObjidStatement

  public function generateInsertStatementFromArray($paramArray) {
    $stmt = $this->newInsertStatement();
    $this->bindParamArrayToStmt($stmt, $paramArray);
    return $stmt;
  } // generateInsertStatementFromArray

  protected function updateEmptyStringMeansNull($fieldName) {
    return(false);
  } // updateEmptyStringMeansNull

  public function updateByObjid($objid, $paramArray, $additionalFilter=null) {
    $stmt = $this->generateUpdateByObjidStatement($objid, $paramArray,
      $additionalFilter);
    if (is_null($stmt)) return;
    return($this->executeSql($stmt->toString()));
  } // updateByObjid

  public function getSqlDeleteByFilter($filter) {
    $stmt = $this->newDeleteStatement();
    if (!is_null($filter) && ($filter != "")) {
      $stmt->setWhere($filter);
    } // if
    return($stmt->toString(true));
  } // getSqlDeleteByFilter

  public function getSqlDeleteByKey($fieldName, $value) {
    if (is_array($value)) {
      return($this->getSqlDeleteByFilter($fieldName.' IN ('
        .implode($value,',').')'));
    } else {
      return($this->getSqlDeleteByFilter($fieldName.'='.$value));
    } // else
  } // getSqlDeleteByKey

  public function getSqlDeleteByObjid($objid) {
    return($this->getSqlDeleteByKey($this->autoIncrementFieldName, $objid));
  } // getSqlDeleteByObjid

  public function deleteByObjid($objid) {
    $sql = $this->getSqlDeleteByObjid($objid);
    return($this->executeSql($sql));
  } // deleteByObjid

  public function deleteByFilter($filter) {
    $sql = $this->getSqlDeleteByFilter($filter);
    return($this->executeSql($sql));
  } // deleteByFilter

  public function deleteAll() {
    return($this->deleteByFilter(null));
  } // deleteAll

  protected function customizeInsert($stmt) {
    /* to be defined in child class */
    return;
  } // customizeInsert

  private function generateInsertSql($paramArray, $onDuplicateUpdate=0, $ignore=1) {
    if (count($paramArray) == 0) return;
    $stmt = $this->generateInsertStatementFromArray($paramArray);
/*
    $stmt = $this->newInsertStatement($ignore);
    foreach($paramArray as $field => $value) {
      if (array_search($field, $this->fieldNameList)===FALSE) continue;
      $stmt->addStringFieldValuePair($field, $value, $onDuplicateUpdate);
    } // foreach
*/
    $this->customizeInsert($stmt);
    return($stmt->toString());
  } // generateInsertSql

  public function insert($paramArray, $onDuplicateUpdate=0, $ignore=1) {
    $sql = $this->generateInsertSql($paramArray, $onDuplicateUpdate, $ignore);
    $result = $this->executeSql($sql);
    if (!$result) return(false);
    return(mysql_affected_rows());
  } // insert

  public function insertAndReturnNewId($paramArray, $onDuplicateUpdate=0, $ignore=1) {
    $sql = $this->generateInsertSql($paramArray, $onDuplicateUpdate, $ignore);
    return($this->executeSqlAndReturnNewId($sql));
  } // insertAndReturnNewId

  /**
   * indexArrayByDataField($dataList, $dataField)
   * If $dataField is not unique, the returned list will contain the 'last' record
   *   with the given $dataField.
   */
  public function indexArrayByDataField($dataList, $indexField,
      $valueField=null) {
    if (is_null($dataList)) return(null);
    if (is_null($indexField)) {
      $indexField = $this->autoIncrementFieldName;
      if (is_null($indexField)) {
        return(null);
      } // if
    } // if
    $newList = Array();
    foreach ($dataList as $data) {
      if (is_null($valueField)) {
        $newList[$data[$indexField]] = $data;
      } else {
        $newList[$data[$indexField]] = $data[$valueField];
      } // else
    } // foreach
    return($newList);
  } // indexArrayByDataField

  protected function getTextSearchableFieldList() {
    /* to be defined in child class */
    return(null);
  } // getTextSearchableFieldList

  protected function generateStringContainsFieldClause($str, $fieldName,
      $matchEmptyVallue=false) {
    /* value of fieldName is contained inside a string */
    /* fieldName is of type text, varchar, ... etc. */
    $containsFilter = $this->wrapNullAndQuote($str).' LIKE '
      .self::FUNCTION_CONCAT.'("%",'.$fieldName.',"%")';
    if ($matchEmptyVallue) {
      return('('.$containsFilter.')');
    } // if
    $filterArray = Array();
    $filterArray[] = $containsFilter;
    $filterArray[] = self::isNotNullClause($fieldName);
    $filterArray[] = $fieldName.' != ""';
    return('('.$this->implodeAndFilterArray($filterArray).')');
  } // generateStringContainsFieldClause

  protected function generateFieldContainsClause($fieldName, $string) {
    if (is_null($string) || ($string == '')) {
      return(null);
    } // if
    return($fieldName.' LIKE "%'.self::escape($string).'%"');
  } // generateFieldContainsClause

  protected function generateTextContainsClause($stringPattern,
      $caseInsensitive=TRUE, $tokenize=FALSE) {
    $searchableFieldList = $this->getTextSearchableFieldList();
    if (is_null($searchableFieldList)) return(null);

    $sp = Array();
    foreach($searchableFieldList as $field) {
      $sp[] = $this->_generateStringPatternClause($field, $stringPattern,
        $caseInsensitive);
      if ($tokenize) {
        $tokenArray = CecHelperString::tokenizeString($stringPattern);
        if (!is_null($tokenArray)) {
          foreach($tokenArray as $token) {
            if ($token != $stringPattern) {
              $sp[] = $this->_generateStringPatternClause($field, $token,
                $caseInsensitive);
            } // if
          } // foreach
        } // if
      } // if
    } // foreach
    $str = implode($sp, ' OR ');
    if (count($sp) > 1) {
      return('('.$str.')');
    } else {
      return($str);
    } // else
  } // generateTextContainsClause

  private static function ucase($field) {
    return 'UCASE('.$field.')';
  } // ucase

  private function _generateStringPatternClause($field, $stringPattern, $caseInsensitive) {
    $clause = null;
    if ($caseInsensitive) {
      $clause .= self::ucase($field);
    } else {
      $clause .= $field;
    } // else
    $clause .= ' LIKE ';
    $pattern = '"%'.mysql_real_escape_string($stringPattern).'%"';
    if ($caseInsensitive) {
      $clause .= self::ucase($pattern);
    } else {
      $clause .= $pattern;
    } // else
    return($clause);
  } // _generateStringPatternClause

  public function textSearch($stringPattern, $additionalFilter=null,
      $caseInsensitive=TRUE, $tokenize=FALSE) {
    $filterArray = Array();
    $textFilter = $this->generateTextContainsClause($stringPattern,
      $caseInsensitive, $tokenize);
    if (!is_null($textFilter)) {
      $filterArray[] = $textFilter;
    } // if
    if (!is_null($additionalFilter)) {
      $filterArray[] = $additionalFilter;
    } // if
    if (count($filterArray) == 0) {
      return(null);
    } // if
    $filter = $this->implodeAndFilterArray($filterArray);
    if (empty($filter)) return(null);
    return($this->fetchSomeAsArray($filter));
  } // textSearch

  /**
   * escape escapes string for use in SQL statement
   */
  public static function escape($string) {
    return mysql_real_escape_string($string);
  } // escape

  public function getFieldNameList() {
    if (!isset($this->autoIncrementFieldName)) {
      return(Array(self::FIELD_OBJID));
    } else {
      return(Array($this->autoIncrementFieldName));
    } // else
  } // getFieldNameList

  public function getRequiredFields() {
    return Array();
  } // getRequiredFields

  public function hasAllRequiredFields($data) {
    if (empty($data)) return false;
    if (!is_array($data)) return false;
    $requiredFields = $this->getRequiredFields();
    foreach ($requiredFields as $f) {
      if (empty($data[$f])) return false;
    } // foreach
    return true;
  } // hasAllRequiredFields

  /**
   *  doesDataContainAllFields returns TRUE if $data contains
   *  every field in this fieldNameList.
   */
  public function doesDataContainAllFields($data) {
    $fList = $this->getFieldNameList();
    foreach ($fList as $fieldName) {
      if (!isset($data[$fieldName])) {
        return(FALSE);
      } // if
    } // foreach
    return(TRUE);
  } // doesDataContainAllFields

  static public function getTableNameValue($data) {
    if (isset($data[self::FIELD_TABLE_NAME])) {
      return($data[self::FIELD_TABLE_NAME]);
    } // if
    return(null);
  } // getTableNameValue

  public function getUnixNow() {
    $nowColumnName = 'unow';
    $sql = 'SELECT '.self::FUNCTION_UNIX_NOW.' AS '.$nowColumnName;
    $result = $this->executeSql($sql);
    $row = $this->dbConnect->convertResultToOneRow($result);
    return($row[$nowColumnName]);
  } // getUnixNow

  public function executeAggregateFunction($functionName, $filter) {
    $countColumnName = 'ctr';
    $sql = 'SELECT '.$functionName.' AS '.$countColumnName
      .' FROM '.$this->tableName;
    if (!is_null($filter)) {
      $sql .= ' WHERE '.$filter;
    } // if
    $result = $this->executeSql($sql);
    $row = $this->dbConnect->convertResultToOneRow($result);
    return($row[$countColumnName]);
  } // executeAggregateFunction

  static protected function generateCountClause($expr) {
    if (is_array($expr)) {
      $expr = implode(',',$expr);
    } // if
    return('COUNT('.$expr.')');
  } // generateCountClause

  public function selectCount($filter=null, $fieldName=null, $distinctFlag=false) {
    if (is_null($fieldName)) {
      $fieldName = '*';
    } // if
    if ($distinctFlag) {
      $fieldName = 'DISTINCT '.$fieldName;
    } // if
    $clause = self::generateCountClause($fieldName);
    return($this->executeAggregateFunction($clause, $filter));
  } // selectCount

  static protected function generateMinClause($expr) {
    if (is_array($expr)) {
      $expr = implode(',',$expr);
    } // if
    return('MIN('.$expr.')');
  } // generateMinClause

  public function selectMinColumn($columnName, $filter=null) {
    return($this->executeAggregateFunction(self::generateMinClause($columnName), $filter));
  } // selectMinColumn

  static protected function generateMaxClause($expr) {
    if (is_array($expr)) {
      $expr = implode(',',$expr);
    } // if
    return('MAX('.$expr.')');
  } // generateMaxClause

  public function selectMaxColumn($columnName, $filter=null) {
    return($this->executeAggregateFunction(self::generateMaxClause($columnName),
      $filter));
  } // selectMaxColumn

  static public function generateSortOrder($sortField, $sortDesc) {
    $sortOrder = null;
    if (!is_null($sortField)) {
      $sortOrder = $sortField;
      if ($sortDesc === true) {
        $sortOrder .= ' DESC'; /* descending */
      } // if
    } // if
    return($sortOrder);
  } // generateSortOrder

  public function selectBySortTextFilter($sortField, $sortDesc, 
      $textFilter=null, $additionalFilter=null,
      $caseInsensitive=TRUE, $tokenize=FALSE) {
    $sortOrder = $this->generateSortOrder($sortField, $sortDesc);
    $filterArray = Array();
    if (!empty($textFilter)) {
      $filterArray[] = $this->generateTextContainsClause($textFilter,
        $caseInsensitive, $tokenize);
    } // if
    if (!is_null($additionalFilter)) {
      $filterArray[] = $additionalFilter;
    } // if
    $filter = $this->implodeAndFilterArray($filterArray);
    return($this->fetchSomeAsArray($filter, $sortOrder));
  } // selectBySortTextFilter

  public function normalizeDataFormat($dateString, $format) {
    $dateComponentArray = strptime($dateString, $format);
    return(sprintf(self::MYSQL_DATE_TIME_FORMAT, 
      $dateComponentArray["tm_year"], 
      $dateComponentArray["tm_mon"],
      $dateComponentArray["tm_mday"]));
  } // normalizeDataFormat

  public function generateSqlSelectVariable($var) {
    return('SELECT '.$var.';');
  } // generateSqlSelectVariable

  public function generateSqlSelectLastInsertId() {
    return(CecDbConnect::SELECT_LAST_INSERT_ID);
  } // generateSqlSelectLastInsertId

  public function generateEqualToOrIsNullClause($fieldName, $fieldValue,
      $isInteger, $caseInsensitive=false) {
    if (is_null($fieldValue)) {
      return(self::isNullClause($fieldName));
    } // if
    if ($caseInsensitive) {
      $fieldName = self::ucase($fieldName);
    } // if
    if (is_array($fieldValue)) {
      /* multiple values */
      if ($isInteger) {
         $filter = implode(',', $fieldValue);
      } else {
        $tmpArray = Array();
        foreach($fieldValue as $v) {
          $tmp = '"'.self::escape($v).'"';
          if ($caseInsensitive) {
            $tmp = self::ucase($tmp);
          } // if
          $tmpArray[] = $tmp;
        } // foreach
        $filter = implode(',', $tmpArray);
      } // else
      return($this->getInClause($fieldName, $filter));
    } else {
      /* single value */
      if ($isInteger) {
        return($fieldName.'='.$fieldValue);
      } else {
        $val = '"'.self::escape($fieldValue).'"';
        if ($caseInsensitive) {
          $val = self::ucase($val);
        } // if
        return($fieldName.'='.$val);
      } // else
    } // else
  } // generateEqualToOrIsNullClause

  /**
   *  Instantiate a new object with all field values set to null.
   */
  public function instantiateObjectWithNullValues($initialValues=null) {
    $emptyObject = Array();
    foreach($this->fieldNameList as $fieldName) {
      $emptyObject[$fieldName] = null;
    } // foreach
    if (!empty($this->subclass)) {
      $emptyObject[self::FIELD_SUBCLASS] = $this->subclass;
    } // if
    if (is_array($initialValues)) {
      foreach($initialValues as $key => $value) {
        $emptyObject[$key] = $value;
      } // foreach
    } // if
    return($emptyObject);
  } // instantiateObjectWithNullValues

  protected function generateObjidInClause($objidArray) {
    if (empty($objidArray)) return(null);
    $objidArrayStr = implode(',', $objidArray);
    if (empty($objidArrayStr)) {
      return(null);
    } // if
    /* array of objids */
    return($this->getInClause($this->autoIncrementFieldName, $objidArrayStr));
  } // generateObjidInClause

  public function fetchByObjidArray($objidArray, $sortOrder=null, $fieldNameArray=null) {
    if (is_null($objidArray)) {
      return($this->fetchAllAsArray(null, $sortOrder, $fieldNameArray));
    } // if
    if (is_array($objidArray)) {
      $filter = $this->generateObjidInClause($objidArray);
      if (empty($filter)) {
        return($this->fetchAllAsArray(null, $sortOrder, $fieldNameArray));
      } // if
      return($this->fetchSomeAsArray($filter, $sortOrder, null, $fieldNameArray));
    } // if
    /* single objid */
    $filter = $this->generateEqualToOrIsNullClause($this->autoIncrementFieldName,
      $objidArray, true);
    return($this->fetchSomeAsArray($filter, $sortOrder, null, $fieldNameArray));
  } // fetchByObjidArray

  public function fetchByObjidArrayInOrder($objidArray, $useObjidAsKeyInResult, $fieldNameArray=null) {
    if (empty($objidArray)) return(null);
    if ($useObjidAsKeyInResult && !is_null($fieldNameArray)) {
      /* objid must be a select field */
      if (!in_array($this->autoIncrementFieldName, $fieldNameArray)) {
        $fieldNameArray[] = $this->autoIncrementFieldName;
      } // if
    } // if
    $objArray = $this->fetchByObjidArray($objidArray, null, $fieldNameArray);
    if (empty($objArray)) return($objArray);
    /* put into an indexed array of objid => obj */
    $objidAsKeyArray = Array();
    foreach($objArray as $obj) {
      $objid = $obj[$this->autoIncrementFieldName];
      $objidAsKeyArray[$objid] = $obj;
    } // foreach
    unset($objArray);

    /* put back in the order in the objidArray */
    $origIndexAsKeyArray = Array();
    foreach($objidArray as $objid) {
      if ($useObjidAsKeyInResult) {
        $origIndexAsKeyArray[$objid] = $objidAsKeyArray[$objid];
      } else {
        $origIndexAsKeyArray[] = $objidAsKeyArray[$objid];
      } // else
    } // foreach
    unset($objidAsKeyArray);
    return($origIndexAsKeyArray);
  } // fetchByObjidArrayInOrder

  public function generateSqlSetVariable($var, $expression) {
    /* expression can be a sql expression and should not be quoted */
    return('SET '.$var.'=('.$expression.');');
  } // generateSqlSetVariable

  public function selectSchemaOfTable() {
    return($this->dbConnect->selectSchemaOfTable($this->getTableName()));
  } // selectSchemaOfTable

  static public function md5($value) {
    return(self::FUNCTION_MD5.'("'.self::escape($value).'")');
  } // md5

  static public function password($value) {
    return(self::FUNCTION_PASSWORD.'("'.self::escape($value).'")');
  } // password

  static public function dateAdd($startDate, $timeQuantity, $timeUnit) {
    return  self::FUNCTION_DATE_ADD.'('.$startDate.', INTERVAL '
      .$timeUnit.' '.$timeQuantity.')';
  } // dateAdd

  static public function reformatSqlScriptToSqlArray($sql) {
    $str = preg_replace(self::NEW_LINE_PATTERN, " ",
      preg_replace(self::EMPTY_LINE_PATTERN, null,
      preg_replace(self::SQL_COMMENT_PATTERN, null, $sql)));
    return(explode(";", $str));
  } // reformatSqlScriptToSqlArray

  protected function getMyDebugLevel() {
    return($this->getDbConnect()->getDebugLevel());
  } // getMyDebugLevel

  public function generateSelectFieldsByFilterSql($filter, $fieldArray=null,
      $distinctFlag=false) {
    $stmt = $this->newSelectStatement();
    if ($distinctFlag) {
      $stmt->setDistinctFlag($distinctFlag);
    } // if
    if (!is_null($filter)) {
      $stmt->setWhere($filter);
    } // if
    if (is_null($fieldArray)) {
      /* null means select all */
      $fieldArray = $this->getFieldNameList();
    } // if
    if (is_array($fieldArray)) {
      foreach($fieldArray as $field) {
        $stmt->addSelectField($field);
      } // foreach
    } else {
      $stmt->addSelectField($fieldArray);
    } // else
    return($stmt->toString());
  } // generateSelectFieldsByFilterSql

  public function generateSelectFieldsByObjidSql($objid, $fieldArray=null,
      $distinctFlag=false) {
    $filter = $this->generateEqualToOrIsNullClause(
      $this->getAutoIncrementFieldName(), $objid, TRUE);
    return($this->generateSelectFieldsByFilterSql($filter, $fieldArray,
      $distinctFlag));
  } // generateSelectFieldsByObjidSql

  public function selectFieldsByFilter($filter, $fieldArray=null,
      $distinctFlag=false, $convertSingularArray=TRUE) {
    $sql = $this->generateSelectFieldsByFilterSql($filter, $fieldArray, $distinctFlag);
    return($this->executeSqlSelect($sql, $convertSingularArray));
  } // selectFieldsByFilter

  public function convertResultToOneValue($result) {
    $resultRow = $this->dbConnect->convertResultToOneRow($result);
    if (is_null($resultRow)) {
      return(null);
    } // if
    $v = array_values($resultRow);
    return($v[0]);
  } // convertResultToOneValue

  public function selectDistinctFieldByObjidArray($objidArray, $fieldArray) {
    $filter = $this->generateObjidInClause($objidArray);
    if (is_null($fieldArray)) {
      $convertSingularArray = false;
    } else {
      $convertSingularArray = (count($fieldArray) == 1);
    } // else
    return($this->selectFieldsByFilter($filter, $fieldArray, true,
      $convertSingularArray));
  } // selectDistinctFieldByObjidArray

  public function fetchNextBatchHigherThanObjid($filter=null, $batchSize=null,
      $startingObjid=null, $fieldNameArray=null) {
    if (empty($this->autoIncrementFieldName)) {
      return(null);
    } // if
    if (is_null($startingObjid)) {
      $objidFilter = null;
    } else {
      $objidFilter = $this->autoIncrementFieldName.'>'
        .self::wrapNullAndQuote($startingObjid);
    } // else
    if (is_null($filter)) {
      $combinedFilter = $objidFilter;
    } else {
      $combinedFilter = $this->implodeAndFilterArray(Array($filter,$objidFilter));
    } // else
    /* objid must be returned */
    if (!is_null($fieldNameArray)) {
      if (!in_array($this->autoIncrementFieldName, $fieldNameArray)) {
        $fieldNameArray[] = $this->autoIncrementFieldName;
      } // if
    } // if
    return($this->fetchBatchAsArray($batchSize, $combinedFilter,
      $this->autoIncrementFieldName, null, $fieldNameArray));
  } // fetchNextBatchHigherThanObjid

  protected static function generateSelectVariablesSql($varToNameArray) {
    if (empty($varToNameArray)) return null;
    $str = 'SELECT ';
    $count = 0;
    foreach($varToNameArray as $varName => $asName) {
      if ($count > 0) $str .= ',';
      $str .= $varName.' AS '.$asName;
      $count++;
    } // foreach
    return $str;
  } // generateSelectVariablesSql

  public function selectOneByObjidFromArray($dataArray, $objid) {
    if (empty($objid) || !is_array($dataArray)) return null;
    $fieldName = $this->autoIncrementFieldName;
    foreach($dataArray as $data) {
      if (!empty($data[$fieldName])) {
        if ($objid == $data[$fieldName]) {
          return $data;
        } // if
      } // if
    } // foreach
    return null;
  } // selectOneByObjidFromArray

  private static function _buildIntSorter($key) {
    return create_function('$a, $b',
      'if (!isset($a["'.$key.'"]) || !isset($b["'.$key.'"])) return -1;
      $fa = intVal($a["'.$key.'"]);$fb = intVal($b["'.$key.'"]);
      if ($fa == $fb) return 0;if ($fa < $fb) return -1;
      return 1;');
  } // _buildIntSorter

  private static function _buildFloatSorter($key) {
    return create_function('$a, $b',
      'if (!isset($a["'.$key.'"]) || !isset($b["'.$key.'"])) return -1;
      $fa = floatVal($a["'.$key.'"]);$fb = floatVal($b["'.$key.'"]);
      if ($fa == $fb) return 0;if ($fa < $fb) return -1;
      return 1;');
  } // _buildFloatSorter

  private static function _buildStringSorter($key) {
    return create_function('$a, $b',
      'if (!isset($a["'.$key.'"]) || !isset($b["'.$key.'"])) return -1;
      return strnatcmp($a["'.$key.'"], $b["'.$key.'"]);');
  } // _buildStringSorter

  public static function sortInMemoryArrayInt(&$dataArray, $fieldName) {
    if (empty($dataArray)) return;
    usort($dataArray, self::_buildIntSorter($fieldName));
  } // sortInMemoryArrayInt

  public static function sortInMemoryArrayFloat(&$dataArray, $fieldName) {
    if (empty($dataArray)) return;
    usort($dataArray, self::_buildFloatSorter($fieldName));
  } // sortInMemoryArrayFloat

  public static function sortInMemoryArrayString(&$dataArray, $fieldName) {
    if (empty($dataArray)) return;
    usort($dataArray, self::_buildStringSorter($fieldName));
  } // sortInMemoryArrayString

} // CecDataTable
?>
