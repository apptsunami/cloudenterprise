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
/* CecGlobals.php */
$rDir = '';

require_once($rDir.'php/cec/CecLogger.php');

class CecGlobals {

  static public function setLogFilePath($path) {
    CecLogger::setLogFilePath($path);
  } // setLogFilePath

  static public function getLogger() {
    return(CecLogger::getLogger());
  } // getLogger

  static public function setLogger($logger) {
    CecLogger::setLogger($logger);
  } // setLogger

  static public function logVariable($var, $variableName, $printToScreen=TRUE) {
    CecLogger::logVariable($var, $variableName, $printToScreen);
  } // logVariable

  static public function logInfo($message, $printToScreen=TRUE) {
    CecLogger::logInfo($message, $printToScreen);
  } // logInfo

  static public function logError($errorMessage, $printToScreen=TRUE) {
    CecLogger::logError($errorMessage, $printToScreen);
  } // logError

} // CecGlobals
?>
