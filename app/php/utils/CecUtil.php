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
/* CecUtil.php */
$rDir = '';
require_once('Zend/Debug.php');
require_once('Zend/Mail.php');
require_once('Zend/Mail/Transport/Sendmail.php');
require_once($rDir.'cec/php/CecAppLogger.php');
require_once($rDir.'php/cec/CecApplicationConfig.php');
require_once($rDir.'cec/php/utils/CecCliUtil.php');

Class CecUtil {

  const MAX_MKDIR_ATTEMPTS = 5;
  const QUOTE_CHARACTER_SET = "\"'";
  const DOUBLE_QUOTE = '"';
  const SINGLE_QUOTE = "'";
  const CHAR_DECIMAL_POINT = '.';
  const CHAR_THOUSAND_SEPARATOR = ',';
  const FILE_PATH_SEP = DIRECTORY_SEPARATOR;
  const FILE_EXTENSION_SEP = '.';
  const PARENT_DIRECTORY_PATH = '../';
  const FILE_PATTERN_ANY = '*';

  const EXTENSION_TXT = 'txt';
  const EXTENSION_CSV = 'csv';
  const EXTENSION_XML = 'xml';
  const EXTENSION_SQL = 'sql';
  // const EXTENSION_PHP = 'php';
  const EXTENSION_GZ = 'gz';
  const EXTENSION_BZ2 = 'bz2';
  const EXTENSION_ZIP = 'zip';

  const DIR_HOME_SSH = '$HOME/.ssh';

  const FILE_SYSTEM_ATTR_MODE = 'mode';
  const FILE_SYSTEM_ATTR_UID = 'uid';
  const FILE_SYSTEM_ATTR_GID = 'gid';
  const FILE_SYSTEM_ATTR_SIZE = 'size';
  const FILE_SYSTEM_ATTR_MTIME = 'mtime';
  const FILE_SYSTEM_ATTR_USER = 'user';
  const FILE_SYSTEM_ATTR_GROUP = 'group';
  const FILE_SYSTEM_ATTR_NAME = 'name';


  const FILE_PATH_INFO_DIRNAME = 'dirname';
  const FILE_PATH_INFO_BASENAME = 'basename';
  const FILE_PATH_INFO_EXTENSION = 'extension';
  const FILE_PATH_INFO_FILENAME = 'filename';

  const STR_TRUE = "true";
  const STR_FALSE = "false";

  // const FULL_DATE_TIME_FORMAT = 'c'; /* ISO 8601 */

  const DEFAULT_EMAIL_SENDER_DOMAIN = CecApplicationConfig::COMPANY_DOMAIN_NAME;
  const DEFAULT_EMAIL_SENDER_ADDRESS_SPRINTF = "nobody@%s";
  const DEFAULT_EMAIL_SENDER_NAME = CecApplicationConfig::COMPANY_NAME;
  const DEFAULT_EMAIL_WRAP_WIDTH = null;

  const REGEX_MATCH_ANYTHING = '.*';
  const PATTERN_MATCH_ANYTHING = '/.*/';
  const PATTERN_AT_LEAST_ONE_LETTER = '/^.*\D.*$/';
  const ILLEGAL_FILE_NAME_CHARACTERS = '/[^A-Za-z0-9_.]/';
  const ILLEGAL_FILE_NAME_CHAR_REPLACEMENT = '_';
  const PATTERN_TIME_ZONE_OFFSET = '/\([A-Za-z]{3}[\+\-][0-9]{2}:[0-9]{2}\)/';
  const PATTERN_TIME_ZONE_UT = '/UT$/';
  const TIME_ZONE_UTC = 'UTC';

  const FILE_PERMISSION_MASK_U = 0700; /* user */
  const FILE_PERMISSION_MASK_G_O = 077; /* group other */
  const FILE_PERMISSION_RWRWRW = 0666;
  const FILE_PERMISSION_RWXRWXRWX = 0777;
  const FILE_PERMISSION_RWX = 0700;

  /* make sure underscore is part of XmlSaxElement::ATTRIBUTE_NAME_PATTERN */
  const XML_ATTR_NAME_SPACE_REPLACEMENT = '_';

  static public function sec2hms($sec, $padHours = false) {
    // holds formatted string
    $hms = "";
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600);
    // add to $hms, with a leading 0 if asked for
    if($padHours){
      $hms .= str_pad($hours, 2, "0", STR_PAD_LEFT);
    } else {
      $hms .= $hours;
    } // else
    $hms .= ':';
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in
    // minutes past the hour: to get that, we need to
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60);

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60);
    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    return $hms;
  } // sec2hms

  static public function isXmlElementMultiValue($node) {
    if (is_array($node)) {
      if (isset($node[0])) return(true);
    } // if
    return(false);
  } // isXmlElementMultiValue

  static public function generatePassword($length = 8) {
    // start with a blank password
    $password = "";
    // define possible characters
    $possible = "0123456789bcdfghjkmnpqrstvwxyz";
    // set up a counter
    $i = 0;
    // add random characters to $password until $length is reached
    while ($i < $length) {
      // pick a random character from the possible ones
      $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
      // we don't want this character if it's already in the password
      if (!strstr($password, $char)) {
        $password .= $char;
        $i++;
      } // if
    } // while
    return $password;
  } // generatePassword

  static public function extractAllMatchingPatterns($string, $pattern) {
    if (is_null($string) || (strlen($string)==0)) return(null);
    $matchedArray = null;
    $count = preg_match_all($pattern, $string, $matchedArray, PREG_SET_ORDER);
    if ($count == 0) return(null);
    return($matchedArray);
  } // extractAllMatchingPatterns

  static public function ifStringMatchesTimestamp($string, $olderThan, $newerThan) {
    /* empty strings do not match */
    if (is_null($string) || ($string == '')) return(false);
    if (!is_numeric($string)) return(false);

    if (!is_numeric($olderThan)) {
      $olderThan = self::convertIso8601ToUnixTimestamp($olderThan, null);
    } // if
    if (!is_numeric($newerThan)) {
      $newerThan = self::convertIso8601ToUnixTimestamp($newerThan, null);
    } // if
    $stringAsInt = intval($string);
    if (!is_null($olderThan)) {
      if ($stringAsInt >= $olderThan) {
CecAppLogger::logDebug("ifStringMatchesTimestamp reject ".$string." newer than ".$olderThan);
        return(false);
      } // if
    } // if
    if (!is_null($newerThan)) {
      if ($stringAsInt <= $newerThan) {
CecAppLogger::logDebug("ifStringMatchesTimestamp reject ".$string." older than ".$newerThan);
        return(false);
      } // if
    } // if
    return(true);
  } // ifStringMatchesTimestamp

  static public function ifStringMatchesPattern($string, $pattern) {
    if (empty($pattern)) return true;
    if (is_array($string)) {
CecAppLogger::logError("ifStringMatchesPattern string should not be array");
CecAppLogger::logVariable($string, "ifStringMatchesPattern.string");
    } // if
    if (!is_numeric($pattern) && !is_string($pattern)) {
CecAppLogger::logError("ifStringMatchesPattern pattern: Delimiter must not be alphanumeric or backslash");
CecAppLogger::logVariable($pattern, "pattern");
    } // if
    return(preg_match($pattern, $string) > 0);
  } // ifStringMatchesPattern

  static public function ucString($string) {
    //This removes all whitespace
    $sreturn="";
    $sa=split(" ",$string);
    foreach ($sa as $key=>$value) {
      $sa[$key]=trim($value);
    } // foreach
    foreach ($sa as $key=>$value) {
      if ($value!="") {
        $sreturn.=ucfirst($value);
        $sreturn.=" ";
      } // if
    } // foreach
    $sreturn=trim($sreturn);
    return $sreturn;
  } // ucString

  static public function getExactMatchPattern($regexp) {
    return(self::convertRegExpToPattern('^'.$regexp.'$'));
  } // getExactMatchPattern

  static public function getStartsWithPattern($regexp) {
    return(self::convertRegExpToPattern('^'.$regexp.'.*'));
  } // getStartsWithPattern

  static public function convertRegExpToPattern($regexp) {
    return('/'.$regexp.'/');
  } // convertRegExpToPattern

  static public function addSlashSegment($base, $segment,
      $sep=self::FILE_PATH_SEP) {
    if (is_null($segment) || ($segment == '') ||
        (is_array($segment) && count($segment)==0)) {
      return($base);
    } // if
    if (is_null($base) || ($base == '')) {
      if (!is_array($segment)) return($segment);
      /* otherwise expand segment into string */
      /* if base is empty do not inject a leading sep */
    } else {
if (is_array($base)) {
CecAppLogger::logVariable($base, "addSlashSegment base is array");
CecAppLogger::logError("addSlashSegment base is array");
} // if
      $base = rtrim($base, $sep);
    } // if
    if (is_array($segment)) {
      $nextSeg = ltrim(array_shift($segment), $sep);
      if (count($segment) == 0) {
        $segment = null;
      } // if
      /* ignore empty strings */
      if (!is_null($nextSeg) && ($nextSeg != '')) {
        if (!is_null($base) && ($base != '')) {
          /* if base is empty do not inject a leading sep */
          $base = $base.$sep;
        } // if
        $base .= $nextSeg;
      } // if
      /* recursion */
      return(self::addSlashSegment($base, $segment, $sep));
    } else {
      $segment = ltrim($segment, $sep);
      if (is_null($segment) || ($segment == '')) {
        /* ignore empty strings */
        return($base);
      } // if
      return($base.$sep.$segment);
    } // else
  } // addSlashSegment

  static public function addSlashSegmentIfNotRepeatLast($base, $segment,
      $sep=self::FILE_PATH_SEP) {
    if (!is_array($segment)) {
      $segmentArray = explode($sep, $segment);
      if (count($segmentArray) > 0) {
        $segment = $segmentArray;
      } // if
    } // if
    $p = rtrim($base, $sep);
    if (!is_array($segment)) {
      $s = $sep.$segment;
      if (strcmp(substr($p, -strlen($s)), $s) == 0) {
        /* $segment is already in the last part of $base */
        return($base);
      } else {
        return(self::addSlashSegment($base, $segment, $sep));
      } // else
    } else {
      /* segment is an array */
      reset($segment);
      $segment2 = $segment;
      while (count($segment2) > 0) {
        $s = $sep.implode($sep, $segment2);
        if (strcmp(substr($p, -strlen($s)), $s) == 0) {
          break;
        } else {
          /* remove the last element and try again */
          array_pop($segment2);
        } // else
      } // while
      if (count($segment2) == count($segment)) {
        return($base);
      } else {
        return(self::addSlashSegment($base, 
          array_slice($segment, count($segment2)), $sep));
      } // else
    } // else
  } // addSlashSegmentIfNotRepeatLast

  static public function removeLastSlashSegment($segments,
      $sep=self::FILE_PATH_SEP) {
    if (empty($segments)) return($segments);
    /* find the last slash */
    $pos = strrpos($segments, $sep);
    /* no slash means the original string is the last segment */
    if ($pos === false) return(null);
    /* return the portion just before the last slash */
    return(substr($segments, 0, $pos));
  } // removeLastSlashSegment

  static public function getLastSlashSegment($segments,
      $sep=self::FILE_PATH_SEP) {
    if (empty($segments)) return($segments);
    /* find the last slash */
    $pos = strrpos($segments, $sep);
    /* no slash means the original string is the last segment */
    if ($pos === false) return($segments);
    /* return the portion after the last slash */
    return(substr($segments, $pos+1));
  } // getLastSlashSegment

  static public function wrapInSlash($str, $sep=self::FILE_PATH_SEP) {
    return($sep.trim($str, $sep).$sep);
  } // wrapInSlash

  static public function appendSlash($str, $sep=self::FILE_PATH_SEP) {
    return(rtrim($str, $sep).$sep);
  } // appendSlash

  static public function splitAtFirstSegment($str, $sep=self::FILE_PATH_SEP) {
    $pos = strpos($str, $sep);
    if ($pos === false) {
      return(array($str, null));
    } // if
    return(array(substr($str, 0, $pos), substr($str, $pos+1)));
  } // splitAtFirstSegment

  static private function _isFilePwdOrParent($file) {
    if ($file == '.' || $file == '..') return(true);
    return(false);
  } // _isFilePwdOrParent

  static private function _ifStringMatchesPattern($file, $fileNamePattern) {
    if (is_null($fileNamePattern)) {
      return(true);
    } // if
    if (!is_array($fileNamePattern)) {
      return(self::ifStringMatchesPattern($file, $fileNamePattern));
    } // if
    foreach($fileNamePattern as $pattern) {
      if(self::ifStringMatchesPattern($file, $pattern)) return(true);
    } // foreach
    return(false);
  } // _ifStringMatchesPattern

  private static function _isDirOrLinkDir($path, $followLink=false) {
    if (!$followLink) {
      return @is_dir($path);
    } // if
    if (@is_dir($path)) return true;
    if (!@is_link($path)) return false;
    return is_dir(readlink($path));
  } // _isDirOrLinkDir

  static private function _getFilesInDir($dirName, $returnFullFilePath=true,
      $includeHiddenFiles=false, $fileNamePattern=null,
      $timeStampPatternOlderThan=null,
      $timeStampPatternNewerThan=null, 
      $recursion=false, $excludeDir=true, $returnStat=false, $subdir=null) {
    /* glob does not do exactly what we need */
    $results = array();
    if (!self::_isDirOrLinkDir($dirName)) {
      return($results);
    } // if
    $dir = opendir($dirName);
    while (($file = readdir($dir)) !== false) {
      if (!self::_isFilePwdOrParent($file)) {
        if (!$includeHiddenFiles && ($file[0] == '.')) {
          continue;
        } // if
        if (!self::_ifStringMatchesPattern($file, $fileNamePattern)) {
          continue;
        } // if
        $fullFilePath = self::addSlashSegment($dirName, $file);
        if (self::_isDirOrLinkDir($fullFilePath) && $recursion) {
          /* recursion */
CecAppLogger::logDebug("recursion into dir ".$file);
          $subResults = self::_getFilesInDir($fullFilePath,
            $returnFullFilePath, $includeHiddenFiles, $fileNamePattern,
            $timeStampPatternOlderThan, $timeStampPatternNewerThan, 
            $recursion, $excludeDir, $returnStat,
            self::addSlashSegment($subdir, $file));
          if (is_array($subResults)) {
            $results = array_merge($results, $subResults);
          } // if
        } else if (self::_isDirOrLinkDir($fullFilePath) && $excludeDir) {
          continue;
        } else {
          if (!is_null($timeStampPatternOlderThan)
                  || !is_null($timeStampPatternNewerThan)) {
            if (!self::ifStringMatchesTimestamp($file, $timeStampPatternOlderThan,
                  $timeStampPatternNewerThan)) {
              continue;
            } // if
          } // else
          $resultsIndex = self::addSlashSegment($subdir, $file);
          if ($returnStat) {
            $fileStat = stat($fullFilePath);
            if ($returnFullFilePath) {
              $fileStat[self::FILE_SYSTEM_ATTR_NAME] = $fullFilePath;
            } else {
              $fileStat[self::FILE_SYSTEM_ATTR_NAME] = $file;
            } // else
            $results[$resultsIndex] = $fileStat;
          } else if ($returnFullFilePath) {
            $results[$resultsIndex] = $fullFilePath;
          } else {
            $results[$resultsIndex] = $file;
          } // else
        } // else
      } // if
    } // while
    closedir($dir);
    /* sort in alphabetical order */
    asort($results);
    return($results);
  } // _getFilesInDir

  static public function getFilesInDir($dirName, $returnFullFilePath=true,
      $fileNamePattern=null, $timeStampPatternOlderThan=null,
      $timeStampPatternNewerThan=null, $recursion=false, $excludeDir=false) {
    return(self::_getFilesInDir($dirName, $returnFullFilePath, false,
      $fileNamePattern, $timeStampPatternOlderThan,
      $timeStampPatternNewerThan, $recursion, $excludeDir, false, null));
  } // getFilesInDir

  static public function getFileStatsInDir($dirName, $returnFullFilePath=true,
      $fileNamePattern=null, $timeStampPatternOlderThan=null,
      $timeStampPatternNewerThan=null, $recursion=false, $excludeDir=false) {
    /*
       matches numeric file names that are timestamps
     */
    return(self::_getFilesInDir($dirName, $returnFullFilePath, false,
      $fileNamePattern, $timeStampPatternOlderThan,
      $timeStampPatternNewerThan, $recursion, $excludeDir, true, null));
  } // getFileStatsInDir

  static public function getNextDirInList($dirList, $currentDir, $pattern=null) {
    if (empty($dirList)) return(null);
    $found = false;
    foreach($dirList as $dir) {
      /* if a pattern is given then the directory name must match the pattern */
      if (!empty($pattern)) {
        if (!self::ifStringMatchesPattern($dir, $pattern)) continue;
      } // if

      /* if starting with nothing return the first one */
      if (empty($currentDir)) {
        return($dir);
      }  // if
      /* not starting with nothing */
      /* if already found the current directory return the first matching one afterwards */
      if ($found) {
        return($dir);
      }  // if
      /* check if this is the current dir */
      if ($dir == $currentDir) {
        $found = true;
      } // if
    } // foreach
    /* if the current dir is the last matching dir */
    return(null);
  } // getNextDirInList

  static public function isDirEmpty($dirName, $nonDirIsEmpty=false) {
    if (!self::_isDirOrLinkDir($dirName)) {
      return($nonDirIsEmpty);
    } // if
    $dir = opendir($dirName);
    while (($file = readdir($dir)) !== false) {
      if (!self::_isFilePwdOrParent($file)) {
        /* found one entry: not empty */
        return(false);
      } // if
    } // while
    closedir($dir);
    /* found nothing */
    return(true);
  } // isDirEmpty

  static public function removeDirIfEmpty($dir) {
    if (empty($dir)) {
CecAppLogger::logDebug("removeDirIfEmpty: directory name is empty");
      return(false);
    } // if
    try {
      if (!self::isDirEmpty($dir, false)) {
CecAppLogger::logDebug("removeDirIfEmpty: ".$dir." is not empty or not a directory");
        return(false);
      } // if
      $success = @rmdir($dir);
      if ($success === false) {
CecAppLogger::logDebug("removeDirIfEmpty: failed to removed dir ".$dir
  ." error=".$php_errormsg);
        return(false);
      } else {
CecAppLogger::logDebug("removeDirIfEmpty: removed dir ".$dir);
      } // else
    } catch (Exception $e) {
CecAppLogger::logDebug("removeDirIfEmpty: rmdir ".$dir." exception "
  .$e->getMessage());
      return(false);
    } // catch
    return(true);
  } // removeDirIfEmpty

  static public function removeDir2($dirName) {
    /* this function is superceded by removeDir */
    if (!is_dir($dirName)) {
      return(false);
    } // if
    $fileList = self::getFilesInDir($dirName, true);
    foreach($fileList as $file) {
      if (is_file($file)) {
        try {
          @unlink($file);
          if (!$status) {
CecAppLogger::logError("unlink ".$file." failed. error=".$php_errormsg);
            return(false);
          } // if
        } catch (Exception $e) {
CecAppLogger::logError("removeDir failed to unlink ".$file);
          return(false);
        } // catch
      } else if (is_dir($file)) {
        self::removeDir($file);
      } // else
    } // foreach
    try {
      $status = @rmdir($dirName);
      if (!$status) {
CecAppLogger::logError("rmdir ".$dirName." failed. error=".$php_errormsg);
        return(false);
      } // if
    } catch (Exception $e) {
CecAppLogger::logError("removeDir failed to rmdir ".$dirName);
      return(false);
    } // catch
    return(true);
  } // removeDir2

  static private function _mkdir($dir) {
    if (file_exists($dir)) {
      if (is_dir($dir)) {
CecAppLogger::logDebug("_mkdir: ".$dir." already exists");
        return(true);
      } else {
CecAppLogger::logError("_mkdir: ".$dir." exists but is not a directory");
        return(false);
      } // else
    } // if
    $status = @mkdir($dir, CecApplicationConfig::FILE_SYSTEM_UMODE, true);
    if (!$status) {
      /* check for race condition where another worker just created the dir */
      @clearstatcache();
      if (!is_dir($dir)) {
        $msg = "mkdir ".$dir." failed.";
        if (!empty($php_errormsg)) {
          $msg .= " Error: ".$php_errormsg;
        } // if
CecAppLogger::logError($msg);
      } // if
    } // if
    return($status);
  } // _mkdir

  static public function createDirectory($dir) {
    @clearstatcache();
    $attempts = 0;
    while ($attempts < self::MAX_MKDIR_ATTEMPTS) {
      $attempts++;
      if (is_dir($dir)) return(true);
      if (self::_mkdir($dir)) {
        @chown($dir, CecApplicationConfig::FILE_SYSTEM_USER);
        @chgrp($dir, CecApplicationConfig::FILE_SYSTEM_GROUP);
        return(true);
      } else {
        /* check for race condition where another process created it first */
        if (is_dir($dir)) return(true);
      } // else
      /* randomly sleeps up to one second */
      usleep(rand(0, 1000000));
    } // while
CecAppLogger::logError("Cannot create directory ".$dir
  ." after ".self::MAX_MKDIR_ATTEMPTS." attempts.\n");
    return(false);
  } // createDirectory

  /*
   *  Recursively delete directory
   */
  static public function deleteDirectory($dirName) {
    if (!is_dir($dirName)) return;
    $dir = opendir($dirName);
    while (($file = readdir($dir)) !== false) {
      if (self::_isFilePwdOrParent($file)) continue;
      $fullFilePath = self::addSlashSegment($dirName, $file);
      if (is_dir($file)) {
        self::deleteDirectory($fullFilePath);
      } else {
        @unlink($fullFilePath);
      } // else
    } // while
    rmdir($dirName);
  } // deleteDirectory

  static public function deleteFiles($filePathArray, $deleteParentDirIfEmpty=false) {
    $success = true;
    if (empty($filePathArray)) return($success);
    $filePathArray = (array)$filePathArray;
    foreach($filePathArray as $filePath) {
      if (empty($filePath)) continue;
      if (is_file($filePath)) {
        $status = @unlink($filePath);
        if (!$status) {
CecAppLogger::logError("deleteFile failed: ".$filePath);
          $success = false;
        } else {
          if ($deleteParentDirIfEmpty !== false) {
            $dir = self::removeLastSlashSegment($filePath);
            if (self::isDirEmpty($dir, false)) {
              /* ignore failure */
CecAppLogger::logDebug("deleteDir: ".$dir);
              $status = @rmdir($dir);
              if (!$status) {
CecAppLogger::logDebug("failed to delete dir ".$dir);
              } // if
            } // if
          } // if
        } // else
      } // if
    } // foreach
    return($success);
  } // deleteFiles

  static public function createDirectoryOrTmp($dir) {
    $status = self::createDirectory($dir);
    if ($status) {
      return($dir);
    } // if

    /* create subdirectory under tmp directory */
    $dir2 = self::addSlashSegment(CecApplicationConfig::TMP_DIRECTORY, $dir);
CecAppLogger::logDebug("Failed to create directory ".$dir
  ." now try ".$dir2);
    $status = self::createDirectory($dir2);
    if ($status) {
      return($dir2);
    } // if

    /* return tmp directory instead of failing */
    $dir3 = CecApplicationConfig::TMP_DIRECTORY;
CecAppLogger::logDebug("Failed to create directory ".$dir2
  ." now try ".$dir3);
    return($dir3);
  } // createDirectoryOrTmp

  static public function setPermissions($file) {
   if (!is_file($file) && !is_dir($file)) return;
   chmod($file, CecApplicationConfig::FILE_SYSTEM_PMODE);
   chown($file, CecApplicationConfig::FILE_SYSTEM_USER);
   chgrp($file, CecApplicationConfig::FILE_SYSTEM_GROUP);
  } // setPermissions

  static public function getFileNameExtension($fileName, $lastExt=true) {
    $lastPart = self::getLastSlashSegment($fileName);
    if (is_null($lastPart) || ($lastPart === '')) return(null);
    $parts = explode(self::FILE_EXTENSION_SEP, $lastPart);
    /* must have at least 2 parts */
    $c = count($parts);
    if ($c < 2) {
      return(null);
    } // if
    if ($lastExt) {
      return($parts[$c-1]);
    } else {
      unset($parts[0]);
      return(implode(self::FILE_EXTENSION_SEP, $parts));
    } // else
  } // getFileNameExtension

  static public function getFileNameWithoutExtension($fileName) {
    $extension = self::getFileNameExtension($fileName);
    if (strlen($extension) == 0) {
      return($fileName);
    } else {
      return(substr($fileName, 0, strlen($fileName)-strlen($extension)-1));
    } // else
  } // getFileNameWithoutExtension

  static public function removeFileNameExtension($fileName, $extension=null) {
    $fn = self::getFileNameWithoutExtension($fileName);
    if (is_null($extension)) {
      /* remove any extension */
      return($fn);
    } // if
    /* remove only specific extension */
    $ext = self::getFileNameExtension($fileName);
    if (is_array($extension)) {
      if (in_array($ext, $extension)) {
        /* return without the extension */
        return($fn);
      } else {
        return($fileName);
      } // else
    } else if (strcmp($ext, $extension) == 0) {
      /* return without the extension */
      return($fn);
    } else {
      /* return original with extension */
      return($fileName);
    } // else
  } // removeFileNameExtension

  static public function addFileNameExtension($fileName, $extension,
      $doNotRepeatExtension=true) {
    if (is_null($fileName) || ($fileName == '')) {
      return($fileName);
    } // if
    if ($doNotRepeatExtension) {
      $currentExt = self::getFileNameExtension($fileName);
      if (!is_null($currentExt) && strcmp($currentExt, $extension) == 0) {
        return($fileName);
      } // if
    } // if
    return(self::addSlashSegment($fileName, $extension, self::FILE_EXTENSION_SEP));
  } // addFileNameExtension

  static public function getMethodParamAndBody($className, $methodName) {
    try {
      $method = new ReflectionMethod($className, $methodName);
    } catch (Exception $e) {
      return(null);
    } // catch
    $params = $method->getParameters();
    $sourceFile = $method->getFileName();
    $startLine = $method->getStartLine();
    $endLine = $method->getEndLine();
    $lineCount = $endLine - $startLine + 1;

    $commandLine = sprintf("head -%d %s |tail -n %d", $endLine, $sourceFile, 
      $lineCount);
    $result = CecCliUtil::execCommandLineSync($commandLine);
    $function = implode("\n", $result[CecCliUtil::EXEC_OUTPUT]);

    $firstChar = strpos($function, "{");
    if (!$firstChar) {
      $firstChar = 0;
    } else {
      $firstChar += 1;
    } // else

    $lastChar = strrpos($function, "}");
    if (!$lastChar) {
      $length = strlen($function) - $firstChar;
    } else {
      $length = $lastChar - 1 - $firstChar;
    } // else

    return(Array($params, substr($function, $firstChar, $length)));
  } // getMethodParamAndBody

  public static function unquoteString($str, $removeSlash=false) {
    if ($removeSlash) {
      $str = stripslashes($str);
    } // if
    $startIndex = 0;
    $endIndex = strlen($str)-1;
    while ($startIndex < $endIndex) {
      $leftCh = $str[$startIndex];
      $rightCh = $str[$endIndex];
      if ($leftCh == $rightCh) {
        if (strpos(self::QUOTE_CHARACTER_SET, $leftCh) !== false) {
          /* found a pair of quote */
          $startIndex++;
          $endIndex--;
          continue;
        } // if
      } // if
      break;
    } // while
    return(trim(substr($str, $startIndex, $endIndex-$startIndex+1)));
  } // unquoteString

  public static function quoteString($str, $escapeCh=true,
      $quoteChar=self::DOUBLE_QUOTE) {
    if (!is_null($str) && $escapeCh) {
      $str = addslashes($str);
    } // if
    return($quoteChar.$str.$quoteChar);
  } // quoteString

  public static function booleanToString($bool) {
    if ($bool === true) return(self::STR_TRUE);
    if ($bool === false) return(self::STR_FALSE);
    return($bool);
  } // booleanToString

  public static function stringToBoolean($str) {
    if (strcmp($str, self::STR_TRUE)==0) return(true);
    if (strcmp($str, self::STR_FALSE)==0) return(false);
    return(null);
  } // stringToBoolean

  public static function stringStartsWith($longStr, $shortStr) {
    if (empty($longStr)) return(false);
    if (is_array($longStr)) {
CecAppLogger::logError($longStr, "stringStartsWith.longStr should not be an array");
      return;
    } // if
    if (is_array($shortStr)) {
CecAppLogger::logError($shortStr, "stringStartsWith.shortStr should not be an array");
      return;
    } // if
    return(strcmp(substr($longStr, 0, strlen($shortStr)), $shortStr)==0);
  } // stringStartsWith

  public static function stringEndsWith($longStr, $shortStr) {
    if (empty($longStr)) return(false);
    return(strcmp(substr($longStr, -strlen($shortStr)), $shortStr)==0);
  } // stringEndsWith

  public static function companyCopyrightString($htmlMode=false) {
    if ($htmlMode) {
      $str = '&copy;';
    } else {
      $str = '(C)';
    } // else
    return($str." ".date('Y')." - ".CecApplicationConfig::COMPANY_FULL_NAME);
  } // companyCopyrightString

  public static function sendEmail($msg, $subject, $receiverEmailAddr,
      $fromEmailAddr=null,
      $fromSenderName=self::DEFAULT_EMAIL_SENDER_NAME,
      $replyToEmailAddr=null, $copyRight=true, $attachmentBinaryString=null,
      $attachmentContentType=null, $attachmentFileName=null) {
    if (is_null($fromEmailAddr)) {
      $fromEmailAddr = sprintf(self::DEFAULT_EMAIL_SENDER_ADDRESS_SPRINTF,
        CecApplicationConfig::COMPANY_DOMAIN_NAME);
    } // if
    if ($msg!=''|| $msg!=NULL) {
      if (!is_null(self::DEFAULT_EMAIL_WRAP_WIDTH)) {
        $msg = wordwrap($msg, self::DEFAULT_EMAIL_WRAP_WIDTH, "\n");
      } // if
    } // if
    if (empty($fromSenderName)) {
      $fullSubject = null;
    } else {
      $fullSubject = $fromSenderName." : ";
    } // else
    $fullSubject .= $subject;
    $body = $msg."\n";
    if ($copyRight) {
      $body .= self::companyCopyrightString(false)."\n";
    } // if
    $tr = new Zend_Mail_Transport_Sendmail('-f '.$fromEmailAddr);
    Zend_Mail::setDefaultTransport($tr);
    $mail = new Zend_Mail();
    if (is_array($receiverEmailAddr)) {
      $receiverEmailAddr = implode(',', $receiverEmailAddr);
    } // if
    $mail->addTo($receiverEmailAddr, '');
    $mail->setFrom($fromEmailAddr, $fromSenderName);
    $mail->setSubject($fullSubject);
    $mail->setBodyText($body);
    if (!empty($replyToEmailAddr)) {
      $mail->addHeader('Reply-To',$replyToEmailAddr);
    } // if
    if (!is_null($attachmentBinaryString)) {
      $mail->setType(Zend_Mime::MULTIPART_RELATED);
      $at = $mail->createAttachment($attachmentBinaryString, $attachmentContentType,
        Zend_Mime::DISPOSITION_INLINE, Zend_Mime::ENCODING_BASE64);
      if (!is_null($attachmentFileName)) {
        $at->filename = $attachmentFileName;
      } // if
    } // if

    $mail->send();
CecAppLogger::logNotice("sent email to ".$receiverEmailAddr
  ."\nfrom: ".$fromSenderName."<".$fromEmailAddr.">"
  ."\nsubject: ".$fullSubject
  ."\nbody: ".$body);
  } // sendEmail

  static public function extractFirstNestedArrayValue($nestedArray) {
    if (is_null($nestedArray)) {
      return(null);
    } // if
    if (!is_array($nestedArray)) {
      return($nestedArray);
    } // if
    reset($nestedArray);
    return(self::extractFirstNestedArrayValue(current($nestedArray)));
  } // extractFirstNestedArrayValue

  static public function getNestedArrayElement($arr, $keyArray) {
    if (empty($arr)) return(null);
    if (is_array($keyArray)) {
      foreach($keyArray as $key) {
        if (!isset($arr[$key])) return(null);
        $arr = $arr[$key];
      } // foreach
      return($arr);
    } else {
      if (!isset($arr[$keyArray])) return(null);
      return($arr[$keyArray]);
    } // else
  } // getNestedArrayElement

  static public function formatInteger($number,
      $thousandSep=self::CHAR_THOUSAND_SEPARATOR) {
    if (is_array($number)) {
CecAppLogger::logError($number, "formatInteger received an array");
      return(null);
    } // if
    if (is_null($number)) return(null);
    return(number_format($number, 0, self::CHAR_DECIMAL_POINT,
      $thousandSep));
  } // formatInteger

  static public function formatDouble($number, $decimalPlaces,
      $thousandSep=self::CHAR_THOUSAND_SEPARATOR) {
    if (is_null($number)) return(null);
    return(number_format($number, $decimalPlaces,
      self::CHAR_DECIMAL_POINT, $thousandSep));
  } // formatDouble

  static private function _formatDateTime($format, $value=null, $utc=false) {
    if ($utc) {
      $tz = date_default_timezone_get();
      date_default_timezone_set('UTC');
    } // if
    if (is_null($value)) {
      $str = date($format);
    } else {
      $str = date($format, $value);
    } // else
    if ($utc) {
      date_default_timezone_set($tz);
    } // if
    return($str);
  } // _formatDateTime

  static public function formatDateTime($value=null) {
    return(self::_formatDateTime('r', $value));
  } // formatDateTime

  static public function formatDefaultDateTime($value=null) {
    return(self::_formatDateTime('Y-m-d H:i:s', $value));
  } // formatDefaultDateTime

  static public function formatLocalDate($value=null) {
    return(self::_formatDateTime('m/d/Y', $value));
  } // formatLocalDate

  static public function formatZDateTime($value=null) {
    return(self::_formatDateTime('Y-m-d\TH:i:s', $value, true).'Z');
  } // formatZDateTime

  static public function formatElapsedSeconds($elapsed) {
    $second = $elapsed % 60;
    $minute = $elapsed/60;
    $hour = $minute/60;
    $minute = $minute % 60;
    return(sprintf("%0d:%02d:%02d", $hour, $minute, $second));
  } // formatElapsedSeconds

  static public function stringToTime($str) {
    if (empty($str)) return(false);
    $dt = strtotime($str);
    if ($dt !== false) return($dt);
    /* repair */
    /* remove extra time zone */
    $str = preg_replace(self::PATTERN_TIME_ZONE_OFFSET, '', $str);
    /* replace UT by UTC */
    $str = preg_replace(self::PATTERN_TIME_ZONE_UT, self::TIME_ZONE_UTC, $str);
    return(strtotime($str));
  } // stringTotime

  static public function convertIso8601ToUnixTimestamp($value,
      $valueIfFalse=0) {
    if (is_null($value) || ($value == '')) {
      return($valueIfFalse);
    } // if
/*
    $value = str_replace("T", " ", $value);
    $value = substr($value, 0, strpos($value, "."));
*/
    $value=strtotime($value);
    if ($value===false) {
      return($valueIfFalse);
    } else {
      return($value);
    } // else
  } // convertIso8601ToUnixTimestamp

  static public function titleCase($title) {
    $whitespace = ' ';
    $smallWordArray = Array(
      /* English */
      'of','a','the','and','an','or','nor','but','is','if','then',
      'else','when', 'at','from','by','on','off','for','in','out',
      'over','to','into','with',
      /* French */
      'de','la',
    ); // Array
    $wordSepArray = Array(' ','/','-',':');
    $wordArray = explode($whitespace, $title);
    foreach($wordArray as $key => $word) {
      if ($key > 0) {
        $lowerCaseWord = strtolower($word);
        if(in_array($lowerCaseWord, $smallWordArray)) {
          $wordArray[$key] = $lowerCaseWord;
          continue;
        } // if
      } // if
      /* candidate for ucfirst */
      $word = ucfirst($word);
      foreach($wordSepArray as $wordSep) {
        if (strpos($word, $wordSep)!==false) {
          $word = implode($wordSep,
            array_map('ucfirst', explode($wordSep, $word)));
        } // if
      } // foreach
      $wordArray[$key] = $word;
    } // foreach
    return(implode($whitespace, $wordArray));
  } // titleCase

  static private function lookUpDataArrayByFieldsByCase(&$dataArray, 
      $matchFieldNameValueArray, &$returnArray, $caseInsensitive,
      $removeFromOriginal) {
    foreach($dataArray as $key => $data) {
      $match = true;
      foreach($matchFieldNameValueArray as $fieldName => $fieldValue) {
        if ($caseInsensitive) {
          $cmp = strcasecmp($data[$fieldName], $fieldValue);
        } else {
          if (is_int($fieldValue)) {
            $cmp = (intVal($data[$fieldName]) == $fieldValue);
          } else if (is_float($fieldValue)) {
            $cmp = (floatVal($data[$fieldName]) == $fieldValue);
          } else if (is_double($fieldValue)) {
            $cmp = (doubleVal($data[$fieldName]) == $fieldValue);
          } else {
            $cmp = strcmp($data[$fieldName], $fieldValue);
          } // else
        } // else
        if ($cmp !== 0) {
          $match = false;
          break;
        } // if
      } // foreach
      if (!$match) continue;
      $returnArray[] = $data;
      if ($removeFromOriginal) {
        unset($dataArray[$key]);
      } // if
    } // foreach
  } // _lookUpDataArrayByFieldByCase

  static public function lookUpDataArrayByFields(&$dataArray,
      $matchFieldNameValueArray, $removeFromOriginal=false,
      $caseInsensitive=null) {
    if (empty($dataArray)) return(null);
    if (!is_array($matchFieldNameValueArray)) return(null);
    $returnArray = Array();
    if (is_null($caseInsensitive)) {
      self::lookUpDataArrayByFieldsByCase($dataArray, 
        $matchFieldNameValueArray, $returnArray, false,
        $removeFromOriginal);
      if (empty($returnArray)) {
        self::lookUpDataArrayByFieldsByCase($dataArray,
          $matchFieldNameValueArray, $returnArray, true,
          $removeFromOriginal);
      } // if
    } else {
      self::lookUpDataArrayByFieldsByCase($dataArray, 
        $matchFieldNameValueArray, $returnArray, $caseInsensitive,
        $removeFromOriginal);
    } // else
    return($returnArray);
  } // lookUpDataArrayByFields

  static public function indexByValueField($dataArray, $keyField,
      $valueField=null) {
    if (!is_array($dataArray)) return($dataArray);
    $newArray = Array();
    foreach($dataArray as $data) {
      if (!isset($data[$keyField])) continue;
      if (is_null($valueField)) {
        $newArray[$data[$keyField]] = $data;
      } else if (isset($data[$valueField])) {
        $newArray[$data[$keyField]] = $data[$valueField];
      } else {
        $newArray[$data[$keyField]] = null;
      } // else
    } // foreach
    return($newArray);
  } // indexByValueField

  static public function projectValueField($data, $fieldName) {
    if (!is_array($fieldName)) {
      /* project one field */
      if (!isset($data[$fieldName])) return(null);
      return($data[$fieldName]);
    } // if

    /* project multiple fields */
    $newData = Array();
    foreach($fieldName as $fn) {
      if (!isset($data[$fn])) continue;
      $newData[$fn] = $data[$fn];
    } // foreach
    if (empty($newData)) return(null);
    return($newData);
  } // projectValueField

  static public function projectValueFieldOfArray($dataArray, $fieldName) {
    if (!is_array($dataArray)) return($dataArray);
    $newArray = Array();
    foreach($dataArray as $data) {
      $newData = self::projectValueField($data, $fieldName);
      if (!is_null($newData)) {
        $newArray[] = $newData;
      } // if
    } // foreach
    return($newArray);
  } // projectValueFieldOfArray

  static public function replaceArrayKeys($arr, $reKeyMap, $keysAreFromXml=false) {
    if (!is_array($arr)) return($arr);
    $returnArray = Array();
    foreach($arr as $key => $value) {
      if (isset($reKeyMap[$key])) {
        $returnArray[$reKeyMap[$key]] = $value;
        continue;
      } // if
      if ($keysAreFromXml) {
        $key = str_replace(self::XML_ATTR_NAME_SPACE_REPLACEMENT, ' ', $key);
        if (isset($reKeyMap[$key])) {
          $returnArray[$reKeyMap[$key]] = $value;
          continue;
        } // if
      } // if
      $returnArray[$key] = $value;
    } // foreach
    return($returnArray);
  } // replaceArrayKeys

  static public function convertToLegalFileName($str) {
    if (is_null($str)) return(null);
    return(preg_replace(self::ILLEGAL_FILE_NAME_CHARACTERS,
      self::ILLEGAL_FILE_NAME_CHAR_REPLACEMENT, $str));
  } // convertToLegalFileName

  static public function generateFilePathInDirectory($directory, $filePath) {
    if (empty($filePath)) return($directory);
    if (is_array($filePath)) return null;
    if (!is_string($filePath)) {
      $filePath = strVal($filePath);
    } // if
    if ($filePath[0] == self::FILE_PATH_SEP) {
      /* absolute path: confirm it is in directory */
      if (strncmp($filePath, $directory, strlen($directory)) != 0) {
        return(false);
      } // if
      return($filePath);
    } else {
      /* relative path: do not allow it to go up */
      if (strpos($filePath, self::PARENT_DIRECTORY_PATH) !== false) {
        return(false);
      } // if
      return(self::addSlashSegment($directory, $filePath));
    } // else
  } // generateFilePathInDirectory

  static public function isFilePathAbsolute($str) {
    if (is_null($str)) return(true);
    if (strlen($str) == 0) return(true);
    return(($str[0] == self::FILE_PATH_SEP));
  } // isFilePathAbsolute

  static public function rawTextToMultilineHtml($str) {
    if (is_array($str)) {
      $str = implode("\n", $str);
    } // if
    return(nl2br(htmlentities($str)));
  } // rawTextToMultilineHtml

  static public function generateTempnam($prefix=null, $suffix=null) {
    return(tempnam(CecApplicationConfig::TMP_DIRECTORY, $prefix).$suffix);
  } // generateTempnam

  static public function stringToTempFile($str) {
    $tf = self::generateTempnam();
    $fh = fopen($tf, 'w');
    if ($fh === false) return(false);
    file_put_contents($tf, $str);
    fclose($fh);
    return($tf);
  } // stringToTempFile

  static public function duSize($dir) {
    if (empty($dir)) return(null);
    $commandLine = sprintf("du -cb %s", $dir);
    $result = CecCliUtil::execCommandLineSync($commandLine);
    if (!CecCliUtil::isExecutionResultSuccess($result)) {
      return(0);
    } // if
    if (is_array($result[CecCliUtil::EXEC_OUTPUT])) {
      $lastLine = end($result[CecCliUtil::EXEC_OUTPUT]);
    } else {
      $lastLine = $result[CecCliUtil::EXEC_OUTPUT];
    } // else
    $count = sscanf($lastLine, "%d %s", $dsize, $dummy);
    if ($count != 2) {
CecAppLogger::logError($result, "duSize.result for ".$dir);
      return(null);
    } // if
    return($dsize);
  } // duSize

  static public function getObjectInstanceID($obj) {
    $lines = explode("\n", trim(Zend_Debug::dump($obj)));
    $id = preg_replace('/ .*/', '', preg_replace('/.*#/', '', current($lines)));
    return($id);
  } // getObjectInstanceID

  static public function formatFileStatMode($mode) {
    if ((is_null($mode)) || !is_int($mode)) {
      return('----------');
    } // if
    $chArray = Array();
    /* other */
    $chArray[] = (($mode % 2)==1?'x':'-');
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'w':'-');
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'r':'-');
    /* group */
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'x':'-');
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'w':'-');
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'r':'-');
    /* user */
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'x':'-');
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'w':'-');
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'r':'-');

    $mode = floor($mode/2);
    /* sticky bit */
    $mode = floor($mode/2);
    /* set group id on execution */
    $mode = floor($mode/2);
    /* set user id on execution */
    $mode = floor($mode/2);
    /* pipe */
    $mode = floor($mode/2);
    /* character special */
    $mode = floor($mode/2);
    $chArray[] = (($mode % 2)==1?'d':'-'); /* directory */
    return(implode('', array_reverse($chArray, false)));
  } // formatFileStatMode

  static public function flattenArray($arr, $glue=",") {
    if (!is_array($arr)) return($arr);
    foreach($arr as $key => &$value) {
      if (is_array($value)) {
        /* recursion */
        $value = self::flattenArray($value, $glue);
      } // if
    } // foreach
    return(implode($glue, $arr));
  } // flattenArray

  static public function deleteEmptyValues($arr, $zeroIsEmpty=false) {
    if (!is_array($arr)) return;
    $returnArray = Array();
    foreach($arr as $key => $value) {
      if ($zeroIsEmpty && empty($value)) continue;
      if (is_null($value) || ($value == '')) continue;
      $returnArray[$key] = $value;
    } // foreach
    return($returnArray);
  } // deleteEmptyValues

  static public function createEmptyArrayFromKeyArray($keyArray, $value=null) {
    $returnArray = Array();
    foreach($keyArray as $key) {
      $returnArray[$key] = $value;
    } // foreach
    return($returnArray);
  } // createEmptyArrayFromKeyArray

  static public function convertListIntoArray($arr, $separator, $trimVal=true) {
    if (empty($arr)) {
      return(null);
    } // if
    $fArray = explode($separator, $arr);
    if (!$trimVal) {
      return($fArray);
    } // if
    $returnArray = Array();
    foreach($fArray as $f) {
      $returnArray[] = trim($f);
    } // foreach
    return($returnArray);
  } // convertListIntoArray

  static public function removeDir($filePath){
    @clearstatcache();
CecAppLogger::logDebug("removeDir ".$filePath);
    if (!is_dir($filePath)) {
CecAppLogger::logDebug("Skip removeDir of non-existent path ".$filePath);
      return(true);
    } // if
    if (!is_writeable($filePath) && is_dir($filePath)){
      @chmod($filePath, self::FILE_PERMISSION_RWXRWXRWX);
      if (!$status) {
CecAppLogger::logError("Failed to chmod ".$filePath." to ".self::FILE_PERMISSION_RWXRWXRWX
  ." error=".$php_errormsg);
        return(false);
      } // if
    } // if
    $handle = @opendir($filePath);
    if (!$handle) {
CecAppLogger::logError("Failed to opendir ".$filePath." error=".$php_errormsg);
      return(false);
    } // if
    while($tmp=@readdir($handle)){
      if ($tmp!='..' && $tmp!='.' && $tmp!=''){
        $f = self::addSlashSegment($filePath, $tmp);
        if (is_writeable($f) && is_file($f)){
          $status = @unlink($f);
          if (!$status) {
CecAppLogger::logError("Failed to unlink ".$f." error=".$php_errormsg);
            return(false);
          } // if
        } elseif (!is_writeable($f) && is_file($f)){
          $status = @chmod($f, self::FILE_PERMISSION_RWRWRW);
          if (!$status) {
CecAppLogger::logError("Failed to chmod ".$f." to ".self::FILE_PERMISSION_RWRWRW
 ." error=".$php_errormsg);
            return(false);
          } // if
          $status = @unlink($f);
          if (!$status) {
CecAppLogger::logError("Failed to unlink ".$f." error=".$php_errormsg);
            return(false);
          } // if
        } // else
       
        if (is_writeable($f) && is_dir($f)){
          $status = self::removeDir($f);
          if (!$status) return(false);
        } elseif (!is_writeable($f) && is_dir($f)){
          $status = @chmod($f, self::FILE_PERMISSION_RWXRWXRWX);
          if (!$status) {
CecAppLogger::logError("Failed to chmod ".$f." to ".self::FILE_PERMISSION_RWXRWXRWX
 ." error=".$php_errormsg);
            return(false);
          } // if
           $status = self::removeDir($f);
           if (!$status) return(false);
        } // elseif
      } // if
    } // while
    @closedir($handle);
    $status = @rmdir($filePath);
    if (!$status) {
CecAppLogger::logError("Failed to rmdir ".$filePath." error=".$php_errormsg);
      return(false);
    } // if
    @clearstatcache();
    if (!is_dir($filePath)){
      return(true);
    } else{
CecAppLogger::logError("removeDir directory ".$filePath." still exists");
      return(false);
    } // else
  } // removeDir

  static public function fileCopy($src, $dest, $overwriteOK=true) {
    @clearstatcache();
    if (is_dir($dest)) {
      $dest = self::addSlashSegment($dest, @basename($src));
    } // if
    if (!$overwriteOK) {
      if (is_file($dest)) {
CecAppLogger::logDebug("fileCopy destination file exists: ".$dest);
        return(false);
      } // if
    } // if
    return(@copy($src, $dest));
  } // fileCopy

  static public function fileCopyDeep($src, $dest) {
    @clearstatcache();
CecAppLogger::logDebug("fileCopyDeep from ".$src." to ".$dest);
    if (is_dir($src)) {
      $status = self::_mkdir($dest);
      if (!$status) {
        return(false);
      } // if
      $objects = @scandir($src);
      if (sizeof($objects) > 0) {
        foreach($objects as $file) {
          if ($file == "." || $file == "..") {
            continue;
          } // if
          $s = self::addSlashSegment($src, $file);
          $d = self::addSlashSegment($dest, $file);
          if (is_dir($s)) {
            self::fileCopyDeep($s, $d);
          } else {
            $status = @copy($s, $d);
            if (!$status) {
CecAppLogger::logError("Failed to copy ".$s." to ".$d." error=".$php_errormsg);
              return(false);
            } // if
          } // else
        } // foreach
      } // if
      return(true);
    } elseif (is_file($src)) {
      $status = @copy($src, $dest);
      if (!$status) {
CecAppLogger::logError("Failed to copy ".$src." to ".$dest." error=".$php_errormsg);
        return(false);
      } // if
      return($status);
    } else {
      return(false);
    } // else
  } // fileCopyDeep

  static public function fileMove($sourceDir, $destinationDir, $relativeFilePath) {
CecAppLogger::logDebug("fileMove from ".$sourceDir." / ".$relativeFilePath
  ." to ".$destinationDir);
    @clearstatcache();
    if (strcmp($sourceDir, $destinationDir)==0) {
CecAppLogger::logDebug("Skipped copying back to source directory ".$sourceDir);
      return(true);
    } // if
    if (!@is_dir($sourceDir)) return(true);

    $srcStat = @stat($sourceDir);
    if (!$srcStat) {
CecAppLogger::logError("Cannot stat ".$sourceDir." error=".$php_errormsg);
      return(false);
    } // if
    $sourceDir = self::addSlashSegment($sourceDir, $relativeFilePath);
    if (!@is_dir($sourceDir) && !@is_file($sourceDir)) {
CecAppLogger::logDebug("Skip copying non-existent path ".$sourceDir);
      return(true);
    } // if
    $targetDir = self::addSlashSegment($destinationDir, $relativeFilePath);
    $targetParentDir = self::removeLastSlashSegment($targetDir);
    if (!@is_dir($targetParentDir)) {
      $status = self::_mkdir($targetParentDir);
      if (!$status) {
        return(false);
      } // if
    } // if
    $destStat = @stat($targetParentDir);
    if (!$destStat) {
CecAppLogger::logError("Cannot stat ".$targetParentDir." error=".$php_errormsg);
      return(false);
    } // if
    if ($srcStat['dev'] == $destStat['dev']) {
CecAppLogger::logDebug("Move same device directory ".$sourceDir." to ".$targetDir);
      $status = @rename($sourceDir, $targetDir);
      if (!$status) {
CecAppLogger::logError("Failed to rename ".$sourceDir." to ".$targetDir." error=".$php_errormsg);
      } // if
      return($status);
    } // if

CecAppLogger::logDebug("Move cross-device directory ".$sourceDir." to ".$targetDir);
    $status = self::fileCopyDeep($sourceDir, $targetDir);
    if (!$status) {
CecAppLogger::logError("Copy failed from ".$sourceDir." to ".$targetDir);
      return(false);
    } // if
    $status = CecUtil::removeDir($sourceDir);
    if (!$status) {
CecAppLogger::logError("removeDir failed ".$sourceDir);
      return(false);
    } // if
    return(true);
  } // fileMove

  static public function isArrayInAnotherArray($haystackArray, $needleArray,
      $andMode=true) {
    $needleArray = (array)$needleArray;
    foreach($needleArray as $needle) {
      if (!in_array($needle, $haystackArray)) {
        if ($andMode) return(false);
      } else {
        if (!$andMode) return(true);
      } // else
    } // foreach
    if ($andMode) {
      return(true);
    } else {
      return(false);
    } // else
  } // isArrayInAnotherArray

  static public function isStringInArrayOfWildcard($needle, $haystack,
      $caseSensitive=false) {
    if (empty($needle)) return(false);
    if (empty($haystack)) return(false);
    $flags = FNM_PATHNAME | FNM_PERIOD;
    if (!$caseSensitive) {
      $flags |= FNM_CASEFOLD;
    } // else
    foreach($haystack as $hay) {
      if (fnmatch($hay, $needle, $flags)) return(true);
    } // foreach
    return(false);
  } // isStringInArrayOfWildcard

  static public function prepareForSsh($dir=self::DIR_HOME_SSH) {
    if (is_dir($dir)) {
CecAppLogger::logDebug("prepareForSsh dir exists ".$dir);
      return(true);
    } // if
    if (!self::_mkdir($dir)) {
CecAppLogger::logDebug("mkdir failed for ".$dir);
      return(false);
    } // if
    if (@chmod($dir, self::FILE_PERMISSION_RWX)) {
CecAppLogger::logDebug("prepareForSsh successded for ".$dir);
      return(true);
    } else {
CecAppLogger::logDebug("chmod failed for ".$dir);
      return(false);
    } // else
  } // prepareForSsh

  static public function dateTimeSanityCheck($datetimestr) {
    if (empty($datetimestr)) return(false);
    if (!is_numeric($datetimestr)) {
      $timestamp = self::stringToTime($datetimestr);
    } else {
      $timestamp = intval($datetimestr);
    } // else
    if ($timestamp === false) return(false);
    if (empty($timestamp)) return(false);
    /* time cannot be in the future */
    if($timestamp > time()) return(false);
    /* TODO: what about the past? */
    return(true);
  } // dateTimeSanityCheck

  static public function getFilePathInfo($filePath) {
    /*
      returns an array of (
        FILE_PATH_INFO_DIRNAME
        FILE_PATH_INFO_BASENAME
        FILE_PATH_INFO_EXTENSION
        FILE_PATH_INFO_FILENAME
      )
    */
    $info = @pathinfo($filePath);
    if (!isset($info[self::FILE_PATH_INFO_FILENAME])) {
      $info[self::FILE_PATH_INFO_FILENAME] =
        self::getFileNameWithoutExtension($info[self::FILE_PATH_INFO_BASENAME]);
    } // if
    return $info;
  } // getFilePathInfo

  static public function generatePathFromFilePathInfo($filePath) {
    if (!isset($filePath[self::FILE_PATH_INFO_EXTENSION])) {
      throw new Exception("missing ".self::FILE_PATH_INFO_EXTENSION);
    } // if
    return $filePath[self::FILE_PATH_INFO_DIRNAME]
      .self::FILE_PATH_SEP
      .$filePath[self::FILE_PATH_INFO_FILENAME]
      .self::FILE_EXTENSION_SEP
      .$filePath[self::FILE_PATH_INFO_EXTENSION];
  } // generatePathFromFilePathInfo

  static public function ensurePathIsDirectory($dir) {
    if (empty($dir)) return(false);
    @clearstatcache();
    if (@is_dir($dir)) return(true);
    if (@file_exists($dir)) {
      return(false);
    } // if
    return(self::createDirectory($dir));
  } // ensurePathIsDirectory

  static public function createFile($filePath) {
    if (!is_file($filePath)) {
      @touch($filePath);
    } // if
  } // createFile

  static public function diffArray($array1, $array2) {
    if (is_array($array2)) {
      if (is_array($array1)) {
        return array_diff($array1, $array2);
      } else {
        return null;
      } // else
    } else {
      return $array1;
    } // else
  } // diffArray

  public static function copyArray(&$target, $source) {
    if (is_null($target) || empty($source)) return;
    foreach($source as $key => $value) {
      $target[$key] = $value;
    } // foreach
  } // copyArray

  static public function generateRandomCharacters($charCount) {
    $chArray = Array();
    for ($i=0; $i<$charCount; $i++) {
      $chArray[] = chr(ord('A')+rand(0,25));
    } // for
    return implode('',$chArray);
  } // generateRandomCharacters

  public static function strtofloat($value) {
    if (is_null($value)) return null;
    if (is_float($value)) return $value;
    if (is_int($value)) return $value;
    if (is_string($value)) {
      $LocaleInfo = localeconv();
      $value = str_replace($LocaleInfo["mon_thousands_sep"] , "", trim($value));
      $value = str_replace($LocaleInfo["mon_decimal_point"] , ".", $value);
    } // if
    return floatVal($value);
  } // strtofloat

  public static function strtodouble($value) {
    if (is_null($value)) return null;
    if (is_double($value)) return $value;
    if (is_int($value)) return $value;
    if (is_string($value)) {
      $LocaleInfo = localeconv();
      $value = str_replace($LocaleInfo["mon_thousands_sep"] , "", trim($value));
      $value = str_replace($LocaleInfo["mon_decimal_point"] , ".", $value);
    } // if
    return doubleVal($value);
  } // strtodouble

  public static function getMimeType($filePath) {
    return @mime_content_type($filePath);
  } // getMimeType

} // CecUtil
?>
