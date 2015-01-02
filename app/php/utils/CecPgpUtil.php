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
/* CecPgpUtil.php */
$rDir = '';
require_once($rDir.'cec/php/CecAppLogger.php');
require_once($rDir.'cec/php/utils/CecCliUtil.php');
require_once($rDir.'cec/php/utils/CecSystemUtil.php');

class CecPgpUtil {

  static public function decryptFile($inputFile, $outputFile, $password) {
    /* only runs on localhost */
    $cmd = sprintf(CecSystemUtil::CMD_GPG_DECRYPT_SPRINTF,
      $password, $outputFile, $inputFile);
    $result = CecCliUtil::execCommandLineSync($cmd);
    if (CecCliUtil::isExecutionResultSuccess($result)) return(true);
    CecAppLogger::logVariable(CecCliUtil::formatExecResultMessage($cmd, $result));
    return(false);
  } // decryptFile

} // CecPgpUtil
?>
