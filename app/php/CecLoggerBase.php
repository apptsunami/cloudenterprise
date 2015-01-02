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
/* CecLoggerBase.php */
$rDir = '';

require_once('Zend/Debug.php');
require_once('Zend/Log.php');
require_once('Zend/Log/Writer/Stream.php');

class CecLoggerBase {

  const GLOBAL_LOGGER = 'cecLogger';
  const LOG_FILE_PATH = '/tmp/cec.log';

  static public $logFilePath;

  static public function setLogFilePath($path) {
    self::$logFilePath = $path;
  } // setLogFilePath

  static public function getLogger() {
    if (!isset(self::$logFilePath)) {
      self::$logFilePath = self::LOG_FILE_PATH;
    } // if
    if (!isset($GLOBALS[self::GLOBAL_LOGGER])) {
        $writer = new Zend_Log_Writer_Stream(self::$logFilePath);
        self::setLogger(new Zend_Log($writer));
    } // if
    return($GLOBALS[self::GLOBAL_LOGGER]);
  } // getLogger

  static public function setLogger($logger) {
    $GLOBALS[self::GLOBAL_LOGGER] = $logger;
  } // setLogger

  static public function logVariable($var, $variableName, $printToScreen=TRUE) {
    $logger = self::getLogger();
    $logger->info(Zend_Debug::dump($var, $variableName, $printToScreen));
  } // logVariable

  static public function logInfo($message, $printToScreen=TRUE) {
    $logger = self::getLogger();
    $logger->info($message);
    if ($printToScreen) {
      echo($message);
    } // if
  } // logInfo

  static public function logError($errorMessage, $printToScreen=TRUE) {
    $logger = self::getLogger();
    $logger->err($errorMessage);
    if ($printToScreen) {
      echo($errorMessage);
    } // if
  } // logError

} // CecLoggerBase
?>
