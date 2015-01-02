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
/* CecCliUtil.php */
$rDir = '';
require_once($rDir.'php/cec/CecApplicationConfig.php');
require_once($rDir.'cec/php/CecAppLogger.php');
require_once($rDir.'php/cec/CecLogger.php');
require_once($rDir.'cec/php/utils/CecSystemUtil.php');
require_once($rDir.'cec/php/utils/CecUtil.php');

/**
 *  Command Line Interface Utilities.
 */
class CecCliUtil {

  const CLI_PARAM_FORMAT = '-%s=%s';
  const CLI_FORMAT = 'cd %s;%s ./%s %s';
  const EXEC_COMMAND_LINE = 'commandLine';
  const EXEC_OUTPUT = 'output';
  const EXEC_RETURN_VAR = 'returnVar';
  const STR_EXECUTION_SUCCESSFUL = "Cli execution successful.";
  const STR_EXECUTION_ERROR = "Cli execution failed with error code %d.";

  const OPTION_ON = 'on';
  const OPTION_OFF = null;
  const CMDLINE_SEP = '${NUL}';
  const LINUX_SUCCESS = 0;
  const LINUX_FAILURE = 1;
  const PARAM_ARRAY_SUFFIX = '[]';

  const MODE_COUNT = 1;
  const MODE_ANY = 2;
  const MODE_ALL = 3;
  const MODE_FIND_DEAD = 4;
  const MODE_FIND_LIVE = 5;

  const PARAM_VALUE_TRUE = 'true';
  const PARAM_VALUE_FALSE = 'false';
  const STR_MISSING_REQUIRED_PARAM_SPRINTF = "%s terminated due to missing parameter %s";

  /**
   *  getParamFromCli looks for a parameter in the command line
   *  argument list (argv) in the format of
   *  "-paramName=value" and returns the portion after "=".
   *  If not found, it returns the $valueIfNotSet.  Note that 
   *  "-paramName=" returns "".
   *
   *  @param string paramName as in "-paramName=value"
   *  @param string What value to return if the parameter is not found
   *  @return string Value as in "-paramName=value" or $valueIfNotSet
   */
  static public function getParamFromCli($paramName, $valueIfNotSet,
      $dieIfNotFound=false) {
    $paramFormat = sprintf(self::CLI_PARAM_FORMAT, $paramName, null);
    $paramFormatLength = strlen($paramFormat);
    $paramArrayFormat = sprintf(self::CLI_PARAM_FORMAT,
      self::_generateParamArrayName($paramName), null);
    $paramArrayFormatLength = strlen($paramArrayFormat);
    $valueArray = Array();
    $argc = $_SERVER["argc"];
    $argv = $_SERVER["argv"];
    /* starts from one to skip the command name */
    for($i=1; $i<$argc; $i++) {
      $arg = $argv[$i];
      if (substr($arg, 0, $paramArrayFormatLength) == $paramArrayFormat) {
        $valueArray[] = self::_stripValueFromArg($arg,
          $paramArrayFormatLength, $valueIfNotSet);
      } else if (substr($arg, 0, $paramFormatLength) == $paramFormat) {
        return(self::_stripValueFromArg($arg, $paramFormatLength, $valueIfNotSet));
      } // else
    } // for
    if (count($valueArray) > 0) return($valueArray);
    if (!$dieIfNotFound) {
      return($valueIfNotSet);
    } // if
    /* die */
    $argv = $_SERVER["argv"];
    $msg = sprintf(self::STR_MISSING_REQUIRED_PARAM_SPRINTF, $argv[0], $paramFormat);
    die($msg);
  } // getParamFromCli

  static private function _stripValueFromArg($arg, $paramFormatLength, $valueIfNotSet) {
    $value = substr($arg, $paramFormatLength);
    if ($value == '') return($valueIfNotSet);
    if ($value == self::PARAM_VALUE_TRUE) return(true);
    if ($value == self::PARAM_VALUE_FALSE) return(false);
    return($value);
  } // _stripValueFromArg

  /**
   *  getBooleanParamFromCli looks for a parameter in the command line
   *  argument list (argv) in the format of "-paramName=on" or not.
   *  If found, it returns true.  Otherwise it returns false.
   *
   *  @param string paramName as in "-paramName=value"
   *  @return boolean
   */
  static public function getBooleanParamFromCli($paramName) {
    $value = self::getParamFromCli($paramName, false);
    if ($value === false) return(false);
    if ($value === self::OPTION_ON) return(true);
    return(false);
  } // getBooleanParamFromCli

  static private function _generateParamArrayName($paramName) {
    return($paramName.self::PARAM_ARRAY_SUFFIX);
  } // _generateParamArrayName

  /**
   *  formatCliParam returns a shell command line parameter in the
   *    format "-paramName=value".
   *  @param string ParamName as in "-paramName=value"
   *  @param string Value as in "-paramName=value"
   *  @param boolean If true, it calls escapeshellarg
   *  @return string As in "-paramName=value"
   */
  static public function formatCliParam($paramName, $value, $escapeShellArg=false) {
    if (is_array($value)) {
      $paramName = self::_generateParamArrayName($paramName);
      $pArray = Array();
      foreach($value as $v) {
        $pArray[] = self::_formatNonArrayCliParam($paramName, $v, $escapeShellArg);
      } // foreach
      return(implode(' ', $pArray));
    } else {
      return(self::_formatNonArrayCliParam($paramName, $value, $escapeShellArg));
    } // else
  } // formatCliParam

  static private function _formatNonArrayCliParam($paramName, $value, $escapeShellArg) {
    if ($value === true) {
      $value = self::PARAM_VALUE_TRUE;
    } else if ($value === false) {
      $value = self::PARAM_VALUE_FALSE;
    } // else
    $str = sprintf(self::CLI_PARAM_FORMAT, $paramName, $value);
    if ($escapeShellArg) {
      /* escape the dash */
      return(self::CMDLINE_SEP.str_replace("-", "\\-", $str).self::CMDLINE_SEP);
    } else {
      return(escapeshellarg($str));
    } // else
  } // _formatNonArrayCliParam

  /**
   *  formatCliParamPattern returns a string of "-paramName=value" that can be
   *    used for command line pattern match.  This is useful for looking up processes
   *    with a certain command line argument.
   *  
   *  @param string ParamName As in "-paramName=value"
   *  @param string Value As in "-paramName=value"
   *  @return string In the format "-paramName=value"
   */
  static public function formatCliParamPattern($paramName, $value) {
    return(self::formatCliParam($paramName, $value, true));
  } // formatCliParamPattern

  /**
   *  formatCliCommand returns a shell command line to execute a php file
   *    in the <tt>cli</tt> directory.
   *  @param string phpFile Path of .php file in the <tt>cli</tt> directory
   *  @param string paramString Shell parameters in one string
   *  @return string Shell command line that can be passed to <tt>exec</tt>
   */
  static public function formatCliCommand($cliPath, $phpFile, $paramString) {
    $cmdLine = sprintf(self::CLI_FORMAT, $cliPath,
      CecSystemUtil::PHP_BIN, $phpFile, $paramString);
    return($cmdLine);
  } // formatCliCommand

  static private function appendAsyncChar($commandLine) {
    return($commandLine.' &');
  } // appendAsyncChar

  /**
   *  prependSshHost returns a command line that can be remotely executed
   *    on the remote host via ssh.  Note that it assumes that the currently
   *    running user name can ssh into the remote host without supplying a
   *    password.  See ssh documentation for details.
   *  If hostName is localhost then it just returns the command line.
   *
   *  @param string The command line to be executed.
   *  @param string Name of remote host to execute the command line.
   *  @return string A ssh command line that can be passed to <tt>exec</tt> and execute on the remote host
   */
  static public function prependSshHost($commandLine, $hostName, $userName, $identityFile) {
    if (empty($commandLine)) return(null);
    if (is_array($commandLine)) {
      if (count($commandLine) == 0) {
        return(null);
      } else if (count($commandLine) == 1) {
        reset($commandLine);
        $commandLine = current($commandLine);
      } else {
        $commandLine = implode(';', $commandLine);
        if (CecSystemUtil::isLocalHost($hostName)) {
          return(sprintf(CecSystemUtil::CMD_SH_C_SPRINTF, addslashes($commandLine)));
        } // if
      } // else
    } // if

    if (CecSystemUtil::isLocalHost($hostName)) {
      return($commandLine);
    } else {
      return(sprintf(CecSystemUtil::CMD_SSH_HOST_COMMAND_SPRINTF,
        $identityFile, $userName, $hostName, addslashes($commandLine)));
    } // else
  } // prependSshHost

  /**
   *  execCommandLineAsync executes a command line on the host
   *    asynchronously without waiting for any output.
   *
   *  @param string The command line to be executed.
   *  @param string Name of remote host to execute the command line.
   *  @return void
   */
  static public function execCommandLineAsync($commandLine, $hostName=null,
      $userName=null, $identityFile=null) {
    if (empty($commandLine)) {
CecAppLogger::logError("execCommandLineAsync.commandLine is empty");
      return;
    } // if
    $command = self::appendAsyncChar(
      self::prependSshHost($commandLine, $hostName, $userName, $identityFile)
      .' >/dev/null 2>/dev/null');
CecLogger::logCmd("execCommandLineAsync: ".$command);
    exec($command);
  } // execCommandLineAsync

  /**
   *  execCommandLineSync executes a command line on the host synchronously
   *    and returns the execution results and output.
   *
   *  @param string The command line to be executed.
   *  @param string Name of remote host to execute the command line.
   *  @return Array cliUtilResult: (executed command line, program output, shell return)
   */
  static public function execCommandLineSync($commandLine, $hostName=null,
      $userName=null, $identityFile=null, $appendAsync=false) {
CecLogger::logCmd($commandLine, "execCommandLineSync: commandLine on hostName ".$hostName);
    if (empty($commandLine)) {
CecAppLogger::logError("execCommandLineSync.commandLine is empty");
      return(self::generateResult($command, null, self::LINUX_FAILURE));
    } // if
    $output = Array();
    $command = self::prependSshHost($commandLine, $hostName, $userName, $identityFile);
    if ($appendAsync) {
      $command = self::appendAsyncChar($command);
    } // if
    exec($command, $output, $returnVar);
CecAppLogger::logVariable($output, "execCommandLineSync: ".$command." result=".$returnVar);
    return(self::generateResult($command, $output, $returnVar));
  } // execCommandLineSync

  static public function generateResult($command, $output, $returnVar) {
    return(Array(
      self::EXEC_COMMAND_LINE => $command,
      self::EXEC_OUTPUT => $output, 
      self::EXEC_RETURN_VAR => $returnVar));
  } // generateResult

  /**
   *  execCommandLineAsyncWithOutput executes a command line on the host asynchronously
   *    and returns the launch output.  Note that this is usually the shell
   *    launch sequence output, not the command line output.  It is useful to confirm
   *    that the launch is successful.
   *
   *  @param string The command line to be executed.
   *  @param string Name of remote host to execute the command line.
   *  @return Array cliUtilResult: (executed command line, shell output, shell return)
   */
  static public function execCommandLineAsyncWithOutput($commandLine, $hostName=null) {
    return(self::execCommandLineSync($commandLine, $hostName, true));
  } // execCommandLineAsyncWithOutput

  /**
   *  isExecutionResultSuccess checks a shell command line return to see if
   *    it is successful.  The Linux convention is zero means success.
   *  @param cliUtilResult Array(executed command line, shell output, shell return)
   */
  static public function isExecutionResultSuccess($result) {
    if ($result[self::EXEC_RETURN_VAR] == self::LINUX_SUCCESS) {
      return(true);
    } else {
      return(false);
    } // else
  } // isExecutionResultSuccess

  static private function formatReturnVarMessage($returnVar) {
    if (empty($returnVar) || ($returnVar == 0)) {
      return(self::STR_EXECUTION_SUCCESSFUL);
    } else {
      return(sprintf(self::STR_EXECUTION_ERROR, $returnVar));
    } // else
  } // formatReturnVarMessage

  /**
   *  formatExecResultMessage formats a cliUtilResult 
   *    (executed command line, command output, shell return) into a
   *    descriptive structure.
   *
   *  @param string Executed command line
   *  @param CliUtilResult Array(executed command line, command output, shell return)
   *  @return Array (command line, return variable message, command output)
   */
  static public function formatExecResultMessage($commandLine, $result) {
    $returnVarMsg = self::formatReturnVarMessage($result[self::EXEC_RETURN_VAR]);
    if (is_array($result[self::EXEC_OUTPUT])) {
      return(array_merge(Array($commandLine, $returnVarMsg),
        $result[self::EXEC_OUTPUT]));
    } else {
      return(Array($commandLine, $returnVarMsg, $result[self::EXEC_OUTPUT]));
    } // else
  } // formatExecResultMessage

  /**
   *  writeStringToFileLocal writes a string to a file on the local host.
   *  @param string Message to be written
   *  @param string Full file path of the file to be written to.
   *  $return boolean True if successful, false otherwise.
   */
  static public function writeStringToFileLocal($msg, $filePath) {
    $byteCount = file_put_contents($filePath, $msg, LOCK_EX);
    if ($byteCount == false) return(false);
    return(true);
  } // writeStringToFileLocal

  /**
   *  writeStringToFile writes a string to a file on a host that may be remote
   *    or local.
   *  @param string Message to be written.
   *  @param string Full file path of the file (relative to the host) to be written to.
   *  @param string Name of host where the filePath resides.
   *  $return boolean True if successful, false otherwise.
   */
  static public function writeStringToFile($msg, $filePath, $hostName=null) {
    /* overwrite existing file */
    if (CecSystemUtil::isLocalHost($hostName)) {
      return(self::writeStringToFileLocal($msg, $filePath));
    } // if
    /* create a temporary file */
    $tempFile = tempnam(CecApplicationConfig::TMP_DIRECTORY, null);
    $status = self::writeStringToFileLocal($msg, $tempFile);
    if ($status === false) {
CecAppLogger::logError("writeStringToFile failed to write to ".$tempFile);
      return(false);
    } // if

    /* scp to remote */
    $command = sprintf(CecSystemUtil::CMD_COPY_FILE_TO_REMOTE_SPRINTF,
      $tempFile, $hostName, $filePath);
    $result = self::execCommandLineSync($command);
    /* delete temporary file */
    unlink($tempFile);
    if ($result[self::EXEC_RETURN_VAR] != 0) return(false);
    return(true);
  } // writeStringToFile

  static public function isProcessRunning($pid, $hostName=null) {
    $cmd = sprintf(CecSystemUtil::CMD_CHECK_PROC_SPRINTF, $pid);
    $result = self::execCommandLineSync($cmd, $hostName);
CecAppLogger::logVariable($result, "isProcessRunning.result");
    $output = CecUtil::extractFirstNestedArrayValue(
      $result[self::EXEC_OUTPUT]);
    if ($output == 0) {
      return(true);
    } else {
      return(false);
    } // else
  } // isProcessRunning

  static public function checkRunningProcesses($processIDArray,
      $returnMode=self::MODE_COUNT) {
CecAppLogger::logVariable($processIDArray, "checkRunningProcesses.processIDArray");
    if(empty($processIDArray)) {
      switch($returnMode) {
      case self::MODE_COUNT:
        return(0);
      case self::MODE_ANY:
        return(false);
      case self::MODE_ALL:
        /* if the list is empty then 'all' means nothing */
        return(true);
      case self::MODE_FIND_DEAD:
      case self::MODE_FIND_LIVE:
        return(null);
      } // switch
    } // if

    $deadProcessIDArray = Array();
    $liveProcessIDArray = Array();
    $count = 0;
    foreach($processIDArray as $processID) {
      $isRunning = self::isProcessRunning($processID);
      switch($returnMode) {
      case self::MODE_COUNT:
        if ($isRunning) $count++;
        break;
      case self::MODE_ANY:
        if ($isRunning) return(true);
      case self::MODE_ALL:
        if (!$isRunning) return(false);
      case self::MODE_FIND_DEAD:
        if (!$isRunning) {
          $deadProcessIDArray[] = $processID;
        } // if
        break;
      case self::MODE_FIND_LIVE:
        if ($isRunning) {
          $liveProcessIDArray[] = $processID;
        } // if
        break;
      } // switch
    } // foreach

    switch($returnMode) {
    case self::MODE_COUNT:
      return($count);
    case self::MODE_ANY:
      return(false);
    case self::MODE_ALL:
      return(true);
    case self::MODE_FIND_DEAD:
      return($deadProcessIDArray);
    case self::MODE_FIND_LIVE:
      return($liveProcessIDArray);
    } // switch
  } // checkRunningProcesses

} // CecCliUtil
?>
