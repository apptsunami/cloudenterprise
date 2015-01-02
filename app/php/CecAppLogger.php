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
/* CecAppLogger.php */
$rDir = '';
require_once('Zend/Debug.php');
require_once('Zend/Log.php');
require_once('Zend/Log/Writer/Stream.php');
require_once($rDir.'cec/php/CecAppLogger.php');
require_once($rDir.'php/cec/CecApplicationConfig.php');

class CecAppLogger {

  const GLOBAL_LOGGER = 'globalLogger';
  const LOG_FILE_PATH = '/tmp/globalLogger.debug.log';
  const GLOBAL_DEBUG_SUBSYSTEMS = 'debugSubsystems';

  const PRINT_TO_SCREEN_DEFAULT = false;

  const STR_SYSTEM_INFO = "|t_e_m|";
  const STR_SYSTEM_INFO_SPRINTF = "|t_e_m|%16.5f|%10.5f|%12d|%12d|%s|";
  const MEMORY_GET_USAGE_MESSAGE_LENGTH = 50;

  const SHOW_REAL_MEMORY_USAGE = false;
  const DEBUG_CLI_MODE = 'cli';

  const MAX_DUMP_FILE_LENGTH = 1000000;

  static private $priorityArray = Array(
    Zend_Log::EMERG => "EMERG",
    Zend_Log::ALERT => "ALERT",
    Zend_Log::CRIT => "CRIT",
    Zend_Log::ERR => "ERR",
    Zend_Log::WARN => "WARN",
    Zend_Log::NOTICE => "NOTICE",
    Zend_Log::INFO => "INFO",
    Zend_Log::DEBUG => "DEBUG",
  ); // Array

  static private $logFilePath = self::LOG_FILE_PATH;
  static private $logFilterLevel = null;
  static private $logWriter = null;
  static private $lastLogTime = null;
  static private $logMemoryUsageFlag;
  static private $defaultFilterLevel;
  static private $showStackTraceOnError;
  static private $maxMemoryUsage = 0;
  static private $memoryLimit;

  static private function getLogMemoryUsageFlag() {
    if (!isset(self::$logMemoryUsageFlag)) {
      return(CecApplicationConfig::DEBUG_MEMORY_USAGE);
    } else {
      return(self::$logMemoryUsageFlag);
    } // else
  } // getLogMemoryUsageFlag

  static private function getFilterLevel() {
    if (!isset(self::$defaultFilterLevel)) {
      return(CecApplicationConfig::DEFAULT_DEBUG_LEVEL);
    } else {
      return(self::$defaultFilterLevel);
    } // else
  } // getFilterLevel

  static private function getShowStackTraceOnError() {
    if (!isset(self::$showStackTraceOnError)) {
      return(CecApplicationConfig::SHOW_STACK_TRACE_ON_ERROR);
    } else {
      return(self::$showStackTraceOnError);
    } // else
  } // getShowStackTraceOnError

  static public function resetMaxMemoryUsage() {
    self::$maxMemoryUsage = 0;
  } // resetMaxMemoryUsage

  static public function getMaxMemoryUsage() {
    return(self::$maxMemoryUsage);
  } // getMaxMemoryUsage

  static public function getPriorityLabelArray() {
    return(self::$priorityArray);
  } // getPriorityLabelArray

  static public function setLogFilePath($path, $fileHandle=null) {
    /* save and unset the previous logger */
    if (isset($GLOBALS[self::GLOBAL_LOGGER])) {
      $oldLogger = $GLOBALS[self::GLOBAL_LOGGER];
      unset($GLOBALS[self::GLOBAL_LOGGER]);
    } else {
      $oldLogger = null;
    } // if
    $status = self::_newLogger($path, $fileHandle);
    if ($status === false) {
      /* restore the previous logger on failure */
      if (!is_null($oldLogger)) {
        $GLOBALS[self::GLOBAL_LOGGER] = $oldLogger;
      } else {
        /* provide a log to default file */
        unset($GLOBALS[self::GLOBAL_LOGGER]);
        $status = self::_newLogger(self::LOG_FILE_PATH);
      } // else
    } // if
    unset($oldLogger);
  } // setLogFilePath

  static public function setLogMemoryUsage($logMemoryUsageFlag) {
    self::$logMemoryUsageFlag = $logMemoryUsageFlag;
    self::appendLog("setLogMemoryUsage to ".self::$logMemoryUsageFlag,
      Zend_Log::DEBUG, self::PRINT_TO_SCREEN_DEFAULT, true);
  } // setLogMemoryUsage

  static public function getLogFilePath() {
    return(self::$logFilePath);
  } // getLogFilePath

  static public function getLogger() {
    if (!isset($GLOBALS[self::GLOBAL_LOGGER])) {
      self::_newLogger(self::$logFilePath);
      if (!isset($GLOBALS[self::GLOBAL_LOGGER])) {
        return(null);
      } // if
    } // if
    return($GLOBALS[self::GLOBAL_LOGGER]);
  } // getLogger

  static private function _newLogger($path, $fileHandle=null) {
    try {
      if (is_null($fileHandle)) {
        $writer = @new Zend_Log_Writer_Stream($path);
      } else {
        $writer = @new Zend_Log_Writer_Stream($fileHandle);
      } // else
      self::$logWriter = new Zend_Log($writer);
      self::_setLoggerContext(self::$logWriter, $path, self::getFilterLevel());
      Zend_Debug::setSapi(self::DEBUG_CLI_MODE);
      return(true);
    } catch (Exception $e) {
      return(false);
    } // catch
  } // _newLogger

  static private function _setLoggerContext($logWriter, $logPath, $logFilter) {
    self::setLogger($logWriter);
    self::_setLogFilePath($logPath);
    self::setLogFilter($logFilter);
  } // _setLoggerContext

  private static function _setLogFilePath($path) {
    /* redirect all PHP errors to this log file */
    ini_set('error_log', $path);
    self::$logFilePath = $path;
  } // _setLogFilePath

  /**
   *  Add filter so that only entries with priority higher than $filter will be logged.
   *  There is no remove filter capability.
   *
   *  These are constants from Zend_Log (Zend/Log.php):
   *  const EMERG   = 0;  // Emergency: system is unusable
   *  const ALERT   = 1;  // Alert: action must be taken immediately
   *  const CRIT    = 2;  // Critical: critical conditions
   *  const ERR     = 3;  // Error: error conditions
   *  const WARN    = 4;  // Warning: warning conditions
   *  const NOTICE  = 5;  // Notice: normal but significant condition
   *  const INFO    = 6;  // Informational: informational messages
   *  const DEBUG   = 7;  // Debug: debug messages
   */
  static public function setLogFilter($filterLevel) {
    if (!is_numeric($filterLevel)) return;
    self::$logFilterLevel = $filterLevel;
    $filterPriority = new Zend_Log_Filter_Priority($filterLevel);
    $logger = self::getLogger();
    if (!is_null($logger)) {
      $logger->addFilter($filterPriority);
    } // if
  } // setLogFilter

  static public function getLogFilter() {
    if (!isset(self::$logFilterLevel)) {
      return(null);
    } // if
    return(self::$logFilterLevel);
  } // getLogFilter

  static public function getLoggerContext() {
    return(Array(self::$logWriter, self::$logFilePath, self::$logFilterLevel));
  } // getLoggerContext

  static public function restoreLoggerContext($loggerContext) {
    self::_setLoggerContext($loggerContext->logWriter,
      $loggerContext->logFilePath, $loggerContext->logFilterLevel);
  } // restoreLoggerContext

  static public function logStackTraceToString() {
    $str = "-------------------------------\n";
    $callStack = debug_backtrace();
    foreach($callStack as $call) {
      if (isset($call["file"])) {
        $str .= $call["file"];
      } else {
        $str .= "''";
      } // else
      $str .= '(';
      if (isset($call["line"])) {
        $str .= $call["line"];
      } else {
        $str .= "''";
      } // else
      $str .= '):';
      if (isset($call["function"])) {
        $str .= $call["function"];
      } else {
        $str .= "''";
      } // else
      $str .= "\n";
    } // foreach
    $str .= "-------------------------------\n";
    return($str);
  } // logStackTraceToString

  static public function setLogger($logger) {
    $GLOBALS[self::GLOBAL_LOGGER] = $logger;
  } // setLogger

  static public function setDebugSubsystems($subsystemList) {
    if (is_array($subsystemList)) {
      $GLOBALS[self::GLOBAL_DEBUG_SUBSYSTEMS] = $subsystemList;
    } else {
      $GLOBALS[self::GLOBAL_DEBUG_SUBSYSTEMS] = array($subsystemList);
    } // else
  } // setDebugSubsystems

  static private function checkSubsystem($subsystem) {
    if (empty($subsystem)) return(true);
    if (!isset($GLOBALS[self::GLOBAL_DEBUG_SUBSYSTEMS])) return(true);
    if (in_array($subsystem, $GLOBALS[self::GLOBAL_DEBUG_SUBSYSTEMS])) {
      return(true);
    } // if
    return(false);
  } // checkSubsystem

  static private function appendLog($message, $priority,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT,
      $skipLogMemoryUsage=false, $memoryUsage=null) {
    $logger = self::getLogger();
    if (is_null($logger)) return;
    if (empty($message)) {
      $stack = "\n".self::logStackTraceToString();
      $logger->log("log Exception:".$stack, Zend_Log::ERR);
    } else {
      if (!is_string($message)) {
        $message = self::_dumpVarToString($message);
      } // if
      try {
        $logger->log($message, $priority);
      } catch (Exception $e) {
        $stack = "\n".self::logStackTraceToString();
        $logger->log("log Exception:".$stack, Zend_Log::ERR);
      } // catch
    } // else
    if ($printToScreen) {
      echo('<br>'.htmlentities($message));
    } // if
    if (!$skipLogMemoryUsage && self::getLogMemoryUsageFlag()) {
      self::logMemoryUsage($message, $priority, $memoryUsage);
    } // if
  } // appendLog

  static public function logDebug($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, false),
      Zend_Log::DEBUG, $printToScreen);
  } // logDebug

  static public function logInfo($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, false),
      Zend_Log::INFO, $printToScreen);
  } // logInfo

  static public function logNotice($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, false),
      Zend_Log::NOTICE, $printToScreen);
  } // logNotice

  static public function logWarning($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, false),
      Zend_Log::WARN, $printToScreen);
  } // logWarning

  static public function logError($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, true),
      Zend_Log::ERR, $printToScreen);
  } // logError

  static public function logCritical($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, true),
      Zend_Log::CRIT, $printToScreen);
  } // logCritical

  static public function logAlert($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, true),
      Zend_Log::ALERT, $printToScreen);
  } // logAlert

  static public function logEmergency($message, $messageLabel=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    self::appendLog(self::_formatLogMessage($message, $messageLabel, true),
      Zend_Log::EMERG, $printToScreen);
  } // logEmergency

  static private function _formatLogMessage($message, $messageLabel,
      $showStack=false) {
    $msg = self::_dumpVarToString($message, $messageLabel);
    if (!$showStack || !self::getShowStackTraceOnError()) {
      return($msg);
    } // if
    return($msg."\n".self::logStackTraceToString());
  } // _formatLogMessage

  static public function logStackTrace($priority=Zend_Log::DEBUG) {
    self::appendLog(self::logStackTraceToString(), $priority);
  } // logStackTrace

  static private function getMemoryUsage() {
    $memoryUsage = memory_get_usage(self::SHOW_REAL_MEMORY_USAGE);
    if ($memoryUsage > self::$maxMemoryUsage) {
      self::$maxMemoryUsage = $memoryUsage;
    } // if
    return($memoryUsage);
  } // getMemoryUsage

  static private function _dumpVarToString($var, $varLabel=null) {
    if (is_object($var) || is_array($var) || !is_null($varLabel)) {
      return(trim(Zend_Debug::dump($var, $varLabel, false), "\n"));
    } else if (!is_null($varLabel)) {
      return($varLabel.":\n".$var);
    } else {
      return(''.$var);
    } // else
  } // _dumpVarToString

  static public function logVariableToString($var, $variableName=null) {
    /* depth-first flattening of values ignore keys */
    $glue = "\n";
    if (!is_null($variableName)) {
      $str = $variableName.":";
    } else {
      $str = null;
    } // else
    if (!is_array($var)) {
      if (!is_null($str)) {
        $str .= $glue;
      } // if
      return($str.self::_dumpVarToString($var));
    } // if
    foreach($var as $v) {
      if (!is_null($str)) {
        $str .= $glue;
      } // if
      if (is_array($v)) {
        /* recursion */
        $str .= self::logVariableToString($v);
      } else {
        $str .= self::_dumpVarToString($v);
      } // else
    } // foreach
    return($str);
  } // logVariableToString

  static public function logVariable($var, $variableName=null, 
      $priority=Zend_Log::DEBUG, $subsystem=null,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    if (isset(self::$logFilterLevel)) {
      if (self::$logFilterLevel < $priority) return;
    } // if
    /* get usage before dumping variable which can consume a lot of memory */
    $memoryUsage = self::getMemoryUsage();
    return(self::appendLog(self::_dumpVarToString($var, $variableName),
      $priority, $printToScreen, false, $memoryUsage));
  } // logVariable

  static public function logMemoryUsage($description=null,
      $priority=Zend_Log::DEBUG, $memoryUsage=null) {
    $currentMicroTime = microtime(true);
    if (!is_null(self::$lastLogTime)) {
      $elapsedTime = $currentMicroTime - self::$lastLogTime;
    } else {
      $elapsedTime = null;
    } // if
    self::$lastLogTime = $currentMicroTime;

    if (!empty($description)) {
      if (is_array($description)) {
        $descErrorMsg = Zend_Debug::dump($description, 'Found array description',
          self::PRINT_TO_SCREEN_DEFAULT);
        self::appendLog($descErrorMsg, Zend_Log::ERR, self::PRINT_TO_SCREEN_DEFAULT, true);
        self::appendLog(self::logStackTraceToString(), Zend_Log::ERR, self::PRINT_TO_SCREEN_DEFAULT, true);
        return;
      } // if
      $description = str_replace("\n", " ",
        substr($description,0,self::MEMORY_GET_USAGE_MESSAGE_LENGTH));
    } // if

    if (is_null($memoryUsage)) {
      $memoryUsage = self::getMemoryUsage();
    } // if
    $msg = sprintf(self::STR_SYSTEM_INFO_SPRINTF, $currentMicroTime,
      $elapsedTime, $memoryUsage, self::$maxMemoryUsage, $description);

    $logger = self::getLogger();
    self::appendLog($msg, $priority, self::PRINT_TO_SCREEN_DEFAULT, true);
  } // logMemoryUsage

  static public function getMemoryLimit() {
    if (!isset(self::$memoryLimit)) {
      $mem = strtoupper(ini_get('memory_limit'));
      $multiplierArray = Array(
        "K" => 1024, 
        "M" => 1048576, 
        "G" => 1073741824);
      $n = sscanf($mem, "%d%c", $d, $unit);
      if ($n == 0) {
        return(null);
      } // if
      self::logDebug("memory_limit=".$mem." d=".$d." unit=".$unit." n=".$n, null, false);
      if ($n == 2) {
        if (isset($multiplierArray[$unit])) {
          $d = $d*$multiplierArray[$unit];
        } else {
          self::logError("Unknown memory_limit unit ".$unit);
        } // else
      } // else
      self::$memoryLimit = $d;
    } // if
    return(self::$memoryLimit);
  } // getMemoryLimit

  /**
   *  $threshold is an integer representing the number of bytes available
   */
  static public function isFreeMemoryBelowThreshold($threshold) {
    $memLimit = self::getMemoryLimit();
    $memoryUsage = memory_get_usage(self::SHOW_REAL_MEMORY_USAGE);
    if (($memoryUsage + $threshold)>$memLimit) return(true);
    return(false);
  } // isFreeMemoryBelowThreshold

  /**
   *  $fraction is a floating point number between 0 and 1
   */
  static public function isFreeMemoryBelowFraction($fraction) {
    $memLimit = self::getMemoryLimit();
    $memoryUsage = memory_get_usage(self::SHOW_REAL_MEMORY_USAGE);
    if ((1-($memoryUsage/$memLimit))<$fraction) return(true);
    return(false);
  } // isFreeMemoryBelowFraction

  static public function dumpFile($filePath, $label=null,
      $priority=Zend_Log::DEBUG,
      $printToScreen=self::PRINT_TO_SCREEN_DEFAULT) {
    if (!is_file($filePath)) {
      self::appendLog(self::_formatLogMessage(
          "File does not exist", $label." ".$filePath, false),
        Zend_Log::DEBUG, $printToScreen);
      return;
    } // if
    self::appendLog(self::_formatLogMessage(
        file_get_contents($filePath, false, null, -1, self::MAX_DUMP_FILE_LENGTH),
        $label." ".$filePath, false),
      Zend_Log::DEBUG, $printToScreen);
  } // dumpFile

} // CecAppLogger
?>
