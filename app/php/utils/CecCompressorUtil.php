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
/* CecCompressorUtil.php */
$rDir = '';
require_once($rDir.'cec/php/utils/CecCliUtil.php');

class CecCompressorUtil {

  const COMPRESSOR_NOOP = 'noop';
  const COMPRESSOR_GZCOMPRESS = 'gzcompress';
  const COMPRESSOR_GZDEFLATE = 'gzdeflate';
  const COMPRESSOR_LZW = 'lzw';
  const COMPRESSOR_ZIP = 'zip';
  const COMPRESSOR_BZIP = 'bzip';

  const COMPRESSOR_DEFAULT = CecCompressorUtil::COMPRESSOR_NOOP;

  static public function compress($rawStr,
      $compressor=self::COMPRESSOR_DEFAULT) {
    switch($compressor) {
    case self::COMPRESSOR_GZCOMPRESS:
      return(gzcompress($rawStr));
    case self::COMPRESSOR_GZDEFLATE:
      return(gzdeflate($rawStr));
    case self::COMPRESSOR_LZW:
      return(self::_lzwCompress($rawStr));
    default:
      return($rawStr);
    } // switch
  } // compress

  static public function decompress($cookedStr,
      $compressor=self::COMPRESSOR_DEFAULT) {
    switch($compressor) {
    case self::COMPRESSOR_GZCOMPRESS:
      return(gzuncompress($cookedStr));
    case self::COMPRESSOR_GZDEFLATE:
      return(gzinflate($cookedStr));
    case self::COMPRESSOR_LZW:
      return(self::_lzwDecompress($cookedStr));
    default:
      return($cookedStr);
    } // switch
  } // decompress

  static private function _generateGzipFileCmdline($filePath, $outputFile) {
    $filePath = escapeshellarg($filePath);
    if (is_null($outputFile)) {
      return(sprintf(CecSystemUtil::CMD_GZIP_FILE_SPRINTF, $filePath));
    } else {
      return(sprintf(CecSystemUtil::CMD_GZIP_FILE_TO_FILE_SPRINTF, $filePath,
        escapeshellarg($outputFile)));
    } // else
  } // _generateGzipFileCmdline

  static private function _generateGzipDirCmdline($filePath, $outputFile) {
    $filePath = escapeshellarg($filePath);
    if (is_null($outputFile)) {
      return(sprintf(CecSystemUtil::CMD_GZIP_DIR_SPRINTF, $filePath));
    } else {
      return(sprintf(CecSystemUtil::CMD_GZIP_DIR_TO_FILE_SPRINTF, $filePath,
        escapeshellarg($outputFile)));
    } // else
  } // _generateGzipDirCmdline

  static private function _generateZipFileCmdline($filePath, $outputFile, $password) {
    $filePath = escapeshellarg($filePath);
    $outputFile = escapeshellarg($outputFile);
    if (is_null($password)) {
      return(sprintf(CecSystemUtil::CMD_ZIP_FILE_TO_FILE_SPRINTF, $outputFile,
        $filePath));
    } else {
      return(sprintf(CecSystemUtil::CMD_ZIP_FILE_PASSWORD_TO_FILE_SPRINTF,
        $password, $outputFile, $filePath));
    } // else
  } // _generateZipFileCmdline

  static private function _generateZipDirCmdline($filePath, $outputFile, $password) {
    $filePath = escapeshellarg($filePath);
    $outputFile = escapeshellarg($outputFile);
    if (is_null($password)) {
      return(sprintf(CecSystemUtil::CMD_ZIP_DIR_TO_FILE_SPRINTF, $outputFile,
        $filePath));
    } else {
      return(sprintf(CecSystemUtil::CMD_ZIP_DIR_PASSWORD_TO_FILE_SPRINTF,
        $password, $outputFile, $filePath));
    } // else
  } // _generateZipDirCmdline

  static private function _getAllGzFilesInDir($filePath) {
    $pattern = CecUtil::FILE_EXTENSION_SEP.CecUtil::EXTENSION_GZ;
    return(CecUtil::getFilesInDir($filePath, true, $pattern));
  } // _getAllGzFilesInDir

  static private function _getAllZipFilesInDir($filePath) {
    $pattern = CecUtil::FILE_EXTENSION_SEP.CecUtil::EXTENSION_ZIP;
    return(CecUtil::getFilesInDir($filePath, true, $pattern));
  } // _getAllZipFilesInDir

  static public function compressFile($filePath,
      $compressor=self::COMPRESSOR_DEFAULT, $outputFile=null, $password=null) {
    $filePath = escapeshellarg($filePath);
    if (!is_null($outputFile)) $outputFile = escapeshellarg($outputFile);
    switch($compressor) {
    case self::COMPRESSOR_GZCOMPRESS:
      if (@is_dir($filePath)) {
        $cmdLine = self::_generateGzipDirCmdline($filePath, $outputFile);
      } else {
        $cmdLine = self::_generateGzipFileCmdline($filePath, $outputFile);
      } // else
      break;
    case self::COMPRESSOR_ZIP:
      if (@is_dir($filePath)) {
        /* multiple input files */
        $filePath = CecUtil::addSlashSegment($filePath,
          CecUtil::FILE_PATTERN_ANY);
        if (empty($outputFile)) {
          /* separate zip file */
          $filePathArray = self::_getAllZipFilesInDir($filePath);
          if (empty($filePathArray)) return(true);
          $cmdLine = Array();
          foreach($filePathArray as $fileP) {
            /* output to separate files: $outputFile is empty */
            $zipFile = CecUtil::addFileNameExtension($fileP,
              CecUtil::EXTENSION_ZIP);
            $cmdLine[] = self::_generateZipFileCmdline($fileP, $zipFile, $password);
          } // foreach
        } else {
          /* combined zip file */
          $cmdLine = self::_generateZipDirCmdline($filePath, $outputFile, $password);
        } // else
      } else {
        /* single input file */
        if (empty($outputFile)) {
          $outputFile = CecUtil::addFileNameExtension($filePath,
            CecUtil::EXTENSION_ZIP);
        } // if
        $cmdLine = self::_generateZipFileCmdline($filePath, $outputFile, $password);
      } // if
      break;
    case self::COMPRESSOR_NOOP:
    default:
      /* no-op */
      return(true);
    } // switch
    $result = CecCliUtil::execCommandLineSync($cmdLine);
    if (!CecCliUtil::isExecutionResultSuccess($result)) {
      CecAppLogger::logError(CecCliUtil::formatExecResultMessage($cmdLine, $result));
      return(false);
    } // if
    return(true);
  } // compressFile

  static private function _generateGunzipFileCmdline($filePath, $outputPath) {
    $filePath = escapeshellarg($filePath);
    if (is_null($outputPath)) {
      return(sprintf(CecSystemUtil::CMD_GUNZIP_FILE_SPRINTF, $filePath));
    } else {
      return(sprintf(CecSystemUtil::CMD_GUNZIP_FILE_TO_FILE_SPRINTF,
        $filePath, escapeshellarg($outputFile)));
    } // else
  } // _generateGunzipFileCmdline

  static private function _generateGunzipDirCmdline($filePath, $outputPath) {
    $filePath = escapeshellarg($filePath);
    if (is_null($outputPath)) {
      return(sprintf(CecSystemUtil::CMD_GUNZIP_DIR_SPRINTF, $filePath));
    } else {
      return(sprintf(CecSystemUtil::CMD_GUNZIP_DIR_TO_FILE_SPRINTF,
        $filePath, escapeshellarg($outputFile)));
    } // else
  } // _generateGunzipDirCmdline

  static private function _generateUnzipToFileCmdline($filePath, $outputPath, $password) {
    $filePath = escapeshellarg($filePath);
    if (is_null($password)) {
      if (empty($outputPath)) {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_SPRINTF, $filePath));
      } else {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_TO_FILE_SPRINTF,
          $filePath, escapeshellarg($outputFile)));
      } // else
    } else {
      if (empty($outputPath)) {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_PASSWORD_SPRINTF,
          $password, $filePath));
      } else {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_PASSWORD_TO_FILE_SPRINTF,
          $password, $filePath, escapeshellarg($outputFile)));
      } // else
    } // else
  } // _generateUnzipToFileCmdline

  static private function _generateUnzipToDirCmdline($filePath, $outputFile, $password) {
    $filePath = escapeshellarg($filePath);
    if (!is_null($outputFile)) $outputFile = escapeshellarg($outputFile);
    if (is_null($password)) {
      if (empty($outputFile)) {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_SPRINTF, $filePath));
      } else {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_TO_DIR_SPRINTF, 
          $outputFile, $filePath));
      } // else
    } else {
      if (empty($outputFile)) {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_PASSWORD_SPRINTF,
          $password, $filePath));
      } else {
        return(sprintf(CecSystemUtil::CMD_UNZIP_FILE_PASSWORD_TO_DIR_SPRINTF,
          $password, $outputFile, $filePath));
      } // else
    } // else
  } // _generateUnzipToDirCmdline

  static private function _generateGunzipFilePath($filePath, $destDir) {
    /* remove .gz extension */
    $originalFileName = self::_getGzipFileContentPath($filePath);
    if ($originalFileName === false) return(false);
    return(CecUtil::addSlashSegment($destDir, $originalFileName));
  } // _generateGunzipFilePath

  static public function decompressFile($filePath,
      $compressor=self::COMPRESSOR_DEFAULT, $outputDir=null, $password=null) {
    $decompressedFileArray = Array();
    switch($compressor) {
    case self::COMPRESSOR_GZCOMPRESS:
      if (@is_dir($filePath)) {
        /* multiple input files: $outputDir should not be a file but a dir */
        if (!empty($outputDir)) {
          $status = CecUtil::ensurePathIsDirectory($outputDir);
          if (!$status) return(false);
        } // if
        $filePathArray = self::_getAllGzFilesInDir($filePath);
        if (empty($filePathArray)) return(true);
        $cmdLine = Array();
        foreach($filePathArray as $fileP) {
          $gunzipFilePath = self::_getGzipFileContentPath($fileP,
            $outputDir);
          $decompressedFileArray[] = $gunzipFilePath;
          $cmdLine[] = self::_generateGunzipFileCmdline($fileP,
            CecUtil::addSlashSegment($outputDir, $gunzipFilePath), $password);
        } // foreach
      } else {
        /* single input file */
        $gunzipFilePath = self::_getGzipFileContentPath($filePath,
          $outputDir);
        $decompressedFileArray[] = $gunzipFilePath;
        $cmdLine = self::_generateGunzipFileCmdline($filePath,
          CecUtil::addSlashSegment($outputDir, $gunzipFilePath), $password);
      } // else
      break;
    case self::COMPRESSOR_ZIP:
      if (@is_dir($filePath)) {
        /* multiple input files: $outputDir should not be a file but a dir */
        if (!empty($outputDir)) {
          $status = CecUtil::ensurePathIsDirectory($outputDir);
          if (!$status) return(false);
        } // if
        $filePathArray = self::_getAllZipFilesInDir($filePath);
        if (empty($filePathArray)) return(true);
        $cmdLine = Array();
        foreach($filePathArray as $fileP) {
          $fList = self::_getZipFileContentPath($fileP);
          if (is_array($fList)) {
            $decompressedFileArray = array_merge($decompressedFileArray, $fList);
          } // if
          $cmdLine[] = self::_generateUnzipToDirCmdline($fileP, $outputDir,
            $password);
        } // foreach
      } else {
        /* single input file */
        $decompressedFileArray = self::_getZipFileContentPath($filePath);
        $cmdLine = self::_generateUnzipToDirCmdline($filePath, $outputDir,
          $password);
      } // else
      break;
    case self::COMPRESSOR_NOOP:
    default:
      /* no-op */
      return($filePath);
    } // switch
    $result = CecCliUtil::execCommandLineSync($cmdLine);
    if (!CecCliUtil::isExecutionResultSuccess($result)) {
      CecAppLogger::logError(CecCliUtil::formatExecResultMessage($cmdLine, $result));
      return(false);
    } // if
CecAppLogger::logVariable($decompressedFileArray, "decompressedFileArray");
    return($decompressedFileArray);
  } // decompressFile

  static private function _getGzipFileContentPath($filePath) {
    $pathInfo = CecUtil::getFilePathInfo($filePath);
    $dir = $pathInfo[CecUtil::FILE_PATH_INFO_DIRNAME];
    $cmdLine = sprintf(CecSystemUtil::CMD_GUNZIP_LIST_SPRINT, $filePath);
    $result = CecCliUtil::execCommandLineSync($cmdLine);
    if (!CecCliUtil::isExecutionResultSuccess($result)) {
      return(false);
    } // if
    $output = (array)$result[CecCliUtil::EXEC_OUTPUT];
    $lastLine = end($output);
    $parts = explode(' ', $lastLine);
    $contentPath = end($parts);
    return(trim(str_replace($dir, '', $contentPath), CecUtil::FILE_PATH_SEP));
  } // _getGzipFileContentPath

  static private function _getZipFileContentPath($filePath) {
    $cmdLine = sprintf(CecSystemUtil::CMD_UNZIP_LIST_SPRINT, $filePath);
    $result = CecCliUtil::execCommandLineSync($cmdLine);
    if (!CecCliUtil::isExecutionResultSuccess($result)) {
      return(false);
    } // if
    $output = (array)$result[CecCliUtil::EXEC_OUTPUT];
    /* remove the first 3 lines and the last 3 lines */
    $rowCount = count($output);
    for($i=0;$i<=2; $i++) {
      unset($output[$i]);
    } // for
    for($i=1;$i<=2; $i++) {
      unset($output[$rowCount-$i]);
    } // for
    $fileList = Array();
    foreach($output as $line) {
      $parts = explode(' ', $line);
      $fileList[] = end($parts);
    } // foreach
    return($fileList);
  } // _getZipFileContentPath

  static private function _lzwCompress($rawStr) {
    $dictSize = 256;
    $dictionary = array();
    for ($i = 0; $i < 256; $i++) {
      $dictionary[chr($i)] = $i;
    } // for
    $w = "";
    $result = "";
    for ($i = 0; $i < strlen($rawStr); $i++) {
      $c = self::_charAt($rawStr, $i);
      $wc = $w.$c;
      if (isset($dictionary[$wc])) {
        $w = $wc;
      } else {
        if ($result != "") {
          $result .= ",".$dictionary[$w];
        } else {
          $result .= $dictionary[$w];
        } // else
        $dictionary[$wc] = $dictSize++;
        $w = "".$c;
      } // else
    } // for
    if ($w != "") {
      if ($result != "") {
        $result .= ",".$dictionary[$w];
      } else {
        $result .= $dictionary[$w];
      } // else
    } // if
    return $result;
  } // _lzwCompress

  static private function _lzwDecompress($cookedStr) {
    $cookedStr = explode(",", $cookedStr);
    $dictSize = 256;
    $dictionary = array();
    for ($i = 1; $i < 256; $i++) {
      $dictionary[$i] = chr($i);
    }
    $w = chr($cookedStr[0]);
    $result = $w;
    for ($i = 1; $i < count($cookedStr); $i++) {
      $entry = "";
      $k = $cookedStr[$i];
      if (isset($dictionary[$k])) {
        $entry = $dictionary[$k];
      } else if ($k == $dictSize) {
        $entry = $w.self::_charAt($w, 0);
      } else {
        return null;
      } // else
      $result .= $entry;
      $dictionary[$dictSize++] = $w.self::_charAt($entry, 0);
      $w = $entry;
    } // for
    return $result;
  } // _lzwDecompress

  static private function _charAt($string, $index){
    if($index < mb_strlen($string)){
      return mb_substr($string, $index, 1);
    } else{
      return -1;
    } // else
  } // _charAt

} // CecCompressorUtil
?>
