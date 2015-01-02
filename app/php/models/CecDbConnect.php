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
/* models/CecDbConnect.php */
$rDir = '';
require_once($rDir.'php/cec/CecApplicationConfig.php');
require_once($rDir.'Zend/Log.php');
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/models/CecDataTable.php');
require_once($rDir.'php/cec/CecLogger.php');

class CecDbConnect {

  const SELECT_LAST_INSERT_ID = 'SELECT LAST_INSERT_ID()';

  const MYSQL_ER_DB_DROP_EXISTS = 1008;
  const MYSQL_ER_BAD_DB_ERROR = 1049;

  const CHARSET_UTF8 = 'utf8';

  const WHITE_SPACES_PATTERN = '/[\s\t]*/';

  const FIELD_TABLE_SCHEMA = 'table_schema';
  const FIELD_DATA_LENGTH = 'data_length';
  const FIELD_INDEX_LENGTH = 'index_length';

  public $debug;
  private $databaseName;
  private $connection; // mysql link descriptor
  private $databaseSelected;

  public function __construct($debugLevel=CecConfig::DEBUG_LEVEL) {
    $this->debug = $debugLevel;
    $this->databaseSelected = false;
    $this->connection = null;
  } // __construct

  public function connect(
      $databaseName=CecApplicationConfig::MY_DATABASE_NAME,
      $host=CecApplicationConfig::MY_DATABASE_PATH,
      $login=CecApplicationConfig::MY_DATABASE_USER,
      $password=CecApplicationConfig::MY_DATABASE_PASSWORD,
      $createDatabase=false, $dieOnError=true, $newLink=true, $grantFromAllHosts=true) {
    $this->databaseName = $databaseName;

    /* Set up local db connection */
    try {
      $this->connection = @mysql_connect($host, $login, $password, $newLink);
    } catch (Exception $e) {
      $this->connection = null;
    } // catch

    // mysql_set_charset(self::CHARSET_UTF8, $this->connection);
    // $encoding = mysql_client_encoding($this->connection);
    // $this->logDebug("client_encoding=".$encoding);

    if (is_null($this->connection) || !is_resource($this->connection)) {
      $errno = mysql_errno();
      $msg = $this->formatMysqlError('Could not connect to '.$host.' errno='.$errno);
      if ($dieOnError) {
        die($msg);
      } else {
        $this->logDebug($msg);
        return($errno);
      } // else
    } // if

    if (is_null($this->databaseName)) {
      /* success */
      return(true);
    } // if

    if (!is_resource($this->connection)) {
        $this->logDebug("No database connection");
        return(false);
    } // if

    $status = @mysql_select_db($this->databaseName, $this->connection);
    if ($status) {
      $this->databaseSelected = true;
    } else {
      $errno = mysql_errno();
      if (!$createDatabase) {
        $msg = $this->formatMysqlError('Could not select db '
          .$this->databaseName.' on '.$host);
        if ($dieOnError) {
          die($msg);
        } else {
          $this->logDebug($msg);
          return($errno);
        } // else
      } else {
        try {
          $this->query("CREATE DATABASE ".$this->databaseName, $this->connection);
          if ($grantFromAllHosts) {
            $hostPattern = '%';
          } else {
            $hostPattern = $host;
          } // else
          $grantSql = sprintf("GRANT ALL ON %s.* to '%s'@'%s' IDENTIFIED BY '%s'",
            $this->databaseName, $login, $hostPattern, $password);
          $this->query($grantSql, $this->connection);
          $status = mysql_select_db($this->databaseName, $this->connection);
          if ($status) $this->databaseSelected = true;
        } catch(Exception $e) {
          $errno = mysql_errno();
          $msg = $this->formatMysqlError('Could not create db '
            .$this->databaseName.' on '.$host.' errno='.$errno);
          if ($dieOnError) {
            die($msg);
          } else {
            $this->logDebug($msg);
            return($errno);
          } // else
        } // catch
      } // else
    } // if
    /* success */
    return(true);
  } // __construct

  private function formatMysqlError($msg) {
    return($msg.' mysql_error="'.mysql_error()
        .'" mysql_errno='.mysql_errno());
  } // formatMysqlError

  private function logDebug($msg) {
    if (CecConfig::DEBUG_LEVEL == 0) return;
    if (isset($this->databaseName)) {
      $msg = "[".$this->databaseName."]: ".$msg;
    } // if
    CecLogger::logDebug($msg."\n");
  } // logDebug

  private function logError($msg) {
    if (isset($this->databaseName)) {
      $msg = "[".$this->databaseName."]: ".$msg;
    } // if
    CecLogger::logError($msg."\n");
  } // logError

  private function _logMysqlError($msg) {
    $this->logError($this->formatMysqlError($msg));
  } // _logMysqlError

  public function isDatabaseSelected() {
    return($this->databaseSelected);
  } // isDatabaseSelected

  public function getDebugLevel() {
    return($this->debug);
  } // getDebugLevel

  public function setDebugLevel($debugLevel) {
    $this->debug = $debugLevel;
  } // setDebugLevel

  public function dropDatabase($databaseName) {
    if (empty($databaseName)) return;
    try {
      $this->query("DROP DATABASE ".$databaseName, $this->connection);
    } catch(Exception $e) {
      $errno = mysql_errno();
      if (($errno == self::MYSQL_ER_BAD_DB_ERROR)
        || ($errno == self::MYSQL_ER_DB_DROP_EXISTS)) {
        /* ok if database does not exist */
        return;
      } // if
      die('Could not drop db '.$databaseName.': '.$e->getMessage()
        .' error message="'.mysql_error()
        .'" error number='.mysql_errno()."\n");
    } // catch
    return(true);
  } // dropDatabase

  public function showTables() {
    $result = mysql_query("show tables", $this->connection);
    if (!$result) {
      return(null);
    } // if
    $tableList = Array();
    while ($row = mysql_fetch_row($result)) {
      $tableList[] = $row[0];
    } // while
    mysql_free_result($result);
    return($tableList);
  } // showTables

  public function selectCountFromTable($tableName, $whereClause=null) {
    $sql = 'SELECT COUNT(*) FROM '.$tableName;
    if (!is_null($whereClause)) {
      $sql .= ' WHERE '.$whereClause;
    } // if
    $result = $this->query($sql);
    $row = mysql_fetch_row($result);
    $count = $row[0];
    mysql_free_result($result);
    return($count);
  } // selectCountFromTable

  public function getDatabaseName() {
    return($this->databaseName);
  } // getDatabaseName

  public function close() {
    if (is_null($this->connection)) {
      return;
    } // if
    mysql_close($this->connection);
    $this->connection = null;
  } // close

  public function query($sqlStr) {
    CecLogger::logSql($sqlStr);
    if (!$this->databaseSelected) {
      $this->logDebug('No database selected for query');
      /* sql can be a create/drop db so continue */
    } // if
    try {
      $result = mysql_query($sqlStr, $this->connection);
      if (!$result) {
        $sqlError = mysql_error();
        $this->_logMysqlError(__FUNCTION__." mysql_query failed on sql ".$sqlStr);
        throw new Exception($sqlError);
      } else if (!is_resource($result)) {
        $this->logDebug("mysql_query returns non-resource ".$result
          .' on sql '.$sqlStr);
      } // else if
      return($result);
    } catch (Exception $e) {
      $this->_logMysqlError(__FUNCTION__.' mysql_query failed on sql '.$sqlStr
        ." exception: ".$e->getMessage());
      return(false);
    } // catch
  } // query

  public function countExecuteAffected($sqlStr) {
    $this->query($sqlStr);
    return(mysql_affected_rows());
  } // countExecuteAffected

  public function getCurrentTime() {
    $result = $this->query('SELECT NOW()');
    $row = mysql_fetch_row($result);
    $currentTime = $row[0];
    mysql_free_result($result);
    return($currentTime);
  } // getCurrentTime

  /*
   *  convertDatabaseResultToList converts $result into an array
   *    and frees the result memory.
   */
  public function convertDatabaseResultToList($result,
      $convertSingularArray=FALSE, $tableNameValue=null) {
    $list = array();
    /* bind results into an array */
    if ($result === false) {
      $this->logError(__FUNCTION__.".result is false");
      return($list);
    } // if
    if (!is_resource($result)) {
      $this->logError($result, __FUNCTION__.".result is not a resource:"
        ." type=".gettype($result)." class=".get_class($result));
      return(null);
    } // if
    try {
      if (!is_null($result)) {
        while ($row = mysql_fetch_assoc($result)) {
          $data = $row;
          if ($convertSingularArray===TRUE) {
            if (is_array($row) && count($row) == 1) {
              /* an array with only one item */
              $v = array_values($row);
              $data = $v[0];
            } // if
          } // if
  
          if (!empty($tableNameValue) && is_array($data)) {
            $data[CecDataTable::FIELD_TABLE_NAME] = $tableNameValue;
          } // if
  
          if (isset($row['objid'])) {
            /* use objid as array index if it has one */
            $list[$row['objid']] = $data;
          } else {
            /* otherwise just add to array */
            $list[] = $data;
          }
        } // while
      } // if
      mysql_free_result($result);
    } catch (Exception $e) {
      $this->_logMysqlError(__FUNCTION__.' exception');
      return(null);
    } // catch
    reset($list);
    return($list);
  } // convertDatabaseResultToList

  public function convertResultToOneRow($result, $tableNameValue=null) {
    if ($result === false) return(null);
    if (!is_null($result)) {
      try {
        while ($row = mysql_fetch_assoc($result)) {
          /* returns the first row found */
          if (!empty($tableNameValue) && is_array($row)) {
            $row[CecDataTable::FIELD_TABLE_NAME] = $tableNameValue;
          } // if
          return($row);
        } // while
      } catch (Exception $e) {
        $this->_logMysqlError(__FUNCTION__." mysql_fetch_assoc exception");
        return(null);
      } // catch
    } // if
    /* returns nothing */
    return(null);
  } // convertResultToOneRow

   /* Transactions functions */
   function begin(){
     $null = mysql_query("START TRANSACTION", $this->connection);
     return mysql_query("BEGIN", $this->connection);
   } // begin

   function commit(){
     $this->logDebug("COMMIT\n");
     return mysql_query("COMMIT", $this->connection);
   } // commit
  
   function rollback(){
     $this->logDebug("ROLLBACK\n");
     return mysql_query("ROLLBACK", $this->connection);
   } // rollback

   function isOneAffected($sql) {
     $startingFrag = Array (
       self::SELECT_LAST_INSERT_ID,
       'SELECT ',
     );
     $containsFrag = null;
     return($this->_matchSqlFragment($sql, $startingFrag, $containsFrag));
   } // isOneAffected

   function isZeroAffected($sql) {
     $startingFrag = Array (
       'SET ',
       'INSERT IGNORE ',
       'DELETE IGNORE ',
       'UPDATE IGNORE ',
     );
     $containsFrag = Array (
       'ON DUPLICATE KEY UPDATE'
     );
     return($this->_matchSqlFragment($sql, $startingFrag, $containsFrag));
   } // isZeroAffected

   private function _matchSqlFragment($sql, $startingFrag, $containsFrag) {
     $trimSql = strtoupper(trim($sql));
     if (!is_null($startingFrag)) {
       foreach($startingFrag as $frag) {
         $pos = strpos($trimSql, $frag);
         if (($pos !== FALSE) && ($pos == 0)) {
           return(TRUE);
         } // if
         $this->logDebug('isZeroAffected('.strlen($frag).') "'
           .$frag.'"!="'.substr($trimSql,0,strlen($frag)).'"');
       } // foreach
     } // if

     if (!is_null($containsFrag)) {
       foreach($containsFrag as $frag) {
         if (strpos($trimSql, $frag) === FALSE) {
           continue;
         } // if
         return(TRUE);
       } // foreach
     } // if
     return(FALSE);
   } // isZeroAffected

   public function transaction($sqlArray){
     $retval = 1;
     $returnResult = null;
     $this->begin();
     $count = 0;
     foreach($sqlArray as $sql){
       if (is_null($sql) || ($sql=='')) continue;
       $count++;
       CecLogger::logSql("(tx:".$count.") ".$sql);
       $result = mysql_query($sql, $this->connection);
       $affected_rows = mysql_affected_rows();
$this->logDebug(__FUNCTION__." affected_rows=".$affected_rows);
       if ($affected_rows == -1) {
         $this->_logMysqlError(__FUNCTION__." affected_rows=-1: ".$sql);
         $retval = 0; 
         break;
       } else if ($affected_rows == 0) {
         if ($this->isZeroAffected($sql) === FALSE) {
           $this->logDebug(__FUNCTION__." affected_rows=0: ".$sql);
           $retval = 0; 
         } // if
       } else if ($affected_rows == 1) {
         if ($this->isOneAffected($sql) == 1) {
$this->logDebug(__FUNCTION__." return isOneAffected result");
           $returnResult = $result;
         } // if
       } else if (is_null($returnResult) && is_resource($result)) {
$this->logDebug(__FUNCTION__." return otherwise result");
         $returnResult = $result;
       } // else
     } // foreach
     if ($retval == 0) {
       $this->rollback();
$this->logDebug(__FUNCTION__." return false");
       return(false);
     } else {
       $this->commit();
       if (!is_null($returnResult)) {
         return($returnResult);
       } // if
$this->logDebug(__FUNCTION__." return true");
       return(true);
     } // else
  } // transaction

  /**
   *  Returns true if successful, false otherwise.
   */
  public function getLock($lockName, $timeOut=0) {
    $alias = "LOCK_STATUS";
    $sql = 'SELECT GET_LOCK("'.$lockName.'",'.$timeOut.') AS '.$alias;
    $result=$this->query($sql);
    $row = mysql_fetch_row($result);
    $status = $row[0];
    mysql_free_result($result);

    if ($status == 1) {
      return(true);
    } else {
      return(false);
    } // else
  } // getLock
  
  /**
   *  Returns true if successful, false otherwise.
   */
  public function releaseLock($lockName) {
    $alias = "LOCK_STATUS";
    $sql = 'SELECT RELEASE_LOCK("'.$lockName.'") AS '.$alias;
    $result=$this->query($sql);
    $row = mysql_fetch_row($result);
    $status = $row[0];
    mysql_free_result($result);

    if (is_null($status)) {
      return(false);
    } else {
      return(true);
    } // else
  } // releaseLock
  
  /**
   *  Returns true if successful, false otherwise.
   */
  public function isFreeLock($lockName) {
    $alias = "LOCK_STATUS";
    $sql = 'SELECT IS_FREE_LOCK("'.$lockName.'") AS '.$alias;
    $result=$this->query($sql);
    $row = mysql_fetch_row($result);
    $status = $row[0];
    mysql_free_result($result);

    if ($status == 1) {
      return(true);
    } else {
      return(false);
    } // else
  } // isFreeLock

  public function executeSqlFileList($sqlFileList) {
    foreach ($sqlFileList as $sqlFile) {
$this->logDebug("executeSqlFileList execute file ".$sqlFile);
      $sqlScript = file_get_contents($sqlFile);
      $sqlArray = CecDataTable::reformatSqlScriptToSqlArray($sqlScript);
      foreach($sqlArray as $sql) {
        if (empty($sql)) continue;
        if (preg_replace(self::WHITE_SPACES_PATTERN, "", $sql) == "") continue;
        try {
          $result = $this->query($sql.";");
        } catch (Exception $e) {
          $this->logError("executeSqlFileList error on file: ".$sqlFile
            ." sql: ".$sql.":".$e->getMessage());
          return;
        } // catch
      } // foreach
    } // foreach
  } // executeSqlFileList

  public function generateRowCounts($tableNameList=null, $skipPrefix=null,
      $whereClause=null) {
    if (is_null($tableNameList)) {
      $tableNameList = $this->showTables();
    } // if
    $tableStatisticsList = Array();
    if (!is_null($tableNameList)) {
      foreach($tableNameList as $tableName) {
        if (!is_null($skipPrefix)) {
          $prefixLen = strlen($skipPrefix);
          if (substr($tableName, 0, $prefixLen) == $skipPrefix) {
            continue;
          } // if
        } // if
        $tableStatisticsList[$tableName]
          = $this->selectCountFromTable($tableName, $whereClause);
      } // foreach
    } // if
    return($tableStatisticsList);
  } // generateRowCounts

  public function selectSchemaOfTable($tableName) {
    $sql = 'SELECT * FROM information_schema.columns WHERE '
      .self::FIELD_TABLE_SCHEMA.'="'
      .mysql_real_escape_string($this->databaseName)
      .'" AND table_name="'.$tableName.'"';
    return($this->convertDatabaseResultToList($this->query($sql)));
  } // selectSchemaOfTable

  public function getDatabaseSize() {
    $sql = 'SELECT sum('.self::FIELD_DATA_LENGTH.')+sum('.self::FIELD_INDEX_LENGTH
      .') FROM information_schema.tables WHERE '
      .self::FIELD_TABLE_SCHEMA.'= "'
      .mysql_real_escape_string($this->databaseName).'"';
    $result = $this->query($sql);
    if ($result === false) return(null);
    $list = $this->convertDatabaseResultToList($result, true);
    if (!is_array($list)) return(null);
    return(current($list));
  } // getDatabaseSize

} // CecDbConnect
?>
