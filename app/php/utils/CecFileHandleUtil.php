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
/* CecFileHandleUtil.php */
$rDir = '';
require_once($rDir.'cec/php/CecAppLogger.php');
require_once($rDir.'cec/php/utils/CecSystemUtil.php');
require_once($rDir.'cec/php/utils/CecCompressorUtil.php');
require_once($rDir.'cec/php/utils/CecUtil.php');

class CecFileHandleUtil {

  const FILE_NAME_PREFIX = 't';
  const READ_BUFFER_SIZE = 4096;

  const DEFAULT_CSV_LENGTH = 0;
  const DEFAULT_CSV_DELIMITER = ',';
  const DEFAULT_CSV_ENCLOSURE = '"';
  const DEFAULT_CSV_ESCAPE = '\\';

  const USE_GZ_FOR_GLOBAL_LOG = false;

  private $fullFileName;
  private $fileExtension;
  private $fileHandle;
  private $compressor;
  private $isOpenWithFileHandle;
  private $deleteOnClose;

  public function __construct() {
    $this->_reset();
  } // __construct

  private function _reset() {
    $this->fullFileName = null;
    $this->fileExtension = null;
    $this->fileHandle = null;
    $this->compressor = null;
    $this->isOpenWithFileHandle = false;
    $this->deleteOnClose = null;
  } // _reset

  public function __destruct() {
    $this->close();
  } // __destruct

  public function hasValidFileHandle() {
    if (empty($this->fileHandle)) return(false);
    if ($this->fileHandle === false) return(false);
    return(true);
  } // hasValidFileHandle

  public function getFullFileName() {
    return($this->fullFileName);
  } // getFullFileName

  public function getFileName() {
    return(CecUtil::getLastSlashSegment($this->fullFileName));
  } // getFileName

  public function getUncompressedFileName() {
    return(CecUtil::removeFileNameExtension(self::getFileName(),
      CecUtil::EXTENSION_GZ));
  } // getUncompressedFileName

  public function open($fullFileName, $openMode, $compressor=null, $deleteOnClose=false) {
    $this->deleteOnClose = $deleteOnClose;
    if (is_null($fullFileName) || ($fullFileName=='')) {
      /* create a temporary file which will be freed on termination */
      $this->fileHandle = tmpfile();
      $this->fullFileName = null;
      $this->fileExtension = null;
    } else {
      $this->fullFileName = $fullFileName;
      $this->fileExtension = CecUtil::getFileNameExtension($fullFileName);
      if (!is_null($compressor)) {
        /* specific compressor required */
        switch($compressor) {
        case CecCompressorUtil::COMPRESSOR_GZCOMPRESS:
          $this->fileHandle = @gzopen($fullFileName, $openMode);
          $this->compressor = CecCompressorUtil::COMPRESSOR_GZCOMPRESS;
          break;
        case CecCompressorUtil::COMPRESSOR_NOOP:
        default:
          $this->fileHandle = @fopen($fullFileName, $openMode);
          $this->compressor = CecCompressorUtil::COMPRESSOR_NOOP;
        } // switch
      } else {
        /* choose compressor based on file extension */
        switch($this->fileExtension) {
        case CecUtil::EXTENSION_GZ:
          $this->fileHandle = @gzopen($fullFileName, $openMode);
          $this->compressor = CecCompressorUtil::COMPRESSOR_GZCOMPRESS;
          break;
        default:
          $this->fileHandle = @fopen($fullFileName, $openMode);
          $this->compressor = CecCompressorUtil::COMPRESSOR_NOOP;
        } // switch
      } // else
    } // else
    return($this->fileHandle);
  } // open

  public static function setGlobalLoggerFilePath($fullFileName) {
    if (is_null($fullFileName) || ($fullFileName == '')) {
      CecAppLogger::setLogFilePath($fullFileName);
    } else if (self::USE_GZ_FOR_GLOBAL_LOG) {
      $fullFileName = CecUtil::addFileNameExtension($fullFileName,
        CecUtil::EXTENSION_GZ);
      CecAppLogger::setLogFilePath($fullFileName, @gzopen($fullFileName, 'a'));
    } else {
      CecAppLogger::setLogFilePath($fullFileName);
    } // else
  } // setGlobalLoggerFilePath

  public function openFileHandle($fileHandle) {
    $this->fileHandle = $fileHandle;
    $this->fullFileName = null;
    $this->fileExtension = null;
    $this->compressor = CecCompressorUtil::COMPRESSOR_NOOP;
    $this->isOpenWithFileHandle = true;
  } // openFileHandle

  public function close() {
    if (empty($this->fileHandle)) return;
    if (!$this->isOpenWithFileHandle) {
      try {
        if ($this->compressor == CecCompressorUtil::COMPRESSOR_GZCOMPRESS) {
          @gzclose($this->fileHandle);
        } else {
          @fclose($this->fileHandle);
        } // else
      } catch (Exception $e) {
        echo("CecFileHandleUtil.close failed");
      } // catch
    } // if
    if ($this->deleteOnClose === true) {
      CecUtil::deleteFiles($this->fullFileName);
    } // if
    $this->_reset();
  } // close

  public function echoFile($rewind=true) {
    if (empty($this->fileHandle)) return;
    if ($rewind) {
      $this->rewind();
    } // if
    while (!feof($this->fileHandle)) {
      $buf=fread($this->fileHandle, self::READ_BUFFER_SIZE);
      echo($buf);
      ob_flush();
      flush();
    } // while
  } // echoFile

  public function fileGetContents($filePath) {
    $this->fullFileName = $filePath;
    $this->fileExtension = CecUtil::getFileNameExtension($filePath);

    if ($this->fileExtension != CecUtil::EXTENSION_GZ) {
      $this->compressor = CecCompressorUtil::COMPRESSOR_NOOP;
      return(file_get_contents($filePath));
    } else {
      $this->compressor = CecCompressorUtil::COMPRESSOR_GZCOMPRESS;
      /* gunzip into temp file */
      $tmpFile = CecUtil::generateTempnam();
      $cmd = sprintf(CecSystemUtil::CMD_GUNZIP_FILE_TO_FILE_SPRINTF,
        $filePath, $tmpFile);
      CliUtil::execCommandLineSync($cmd);
      /* read temp file */
      $result = file_get_contents($tmpFile);
      /* remove temp file */
      unlink($tmpFile);
      return($result);
    } // else
  } // fileGetContents

  public function filePutContents($filePath, $contents) {
    $fileExtension = CecUtil::getFileNameExtension($filePath);
    if ($this->fileExtension != CecUtil::EXTENSION_GZ) {
      file_put_contents($filePath, $contents);
    } else {
      $fileWithoutExtension = CecUtil::getFileNameWithoutExtension($filePath);
      file_put_contents($fileWithoutExtension, $contents);
      /* gzip replaces the original file with the .gz file */
      $cmd = sprintf(CecSystemUtil::CMD_GZIP_FILE_SPRINTF, $fileWithoutExtension);
      CliUtil::execCommandLineSync($cmd);
    } // else
  } // filePutContents

  public function getcsv($length=self::DEFAULT_CSV_LENGTH,
      $delimiter=self::DEFAULT_CSV_DELIMITER,
      $enclosure=self::DEFAULT_CSV_ENCLOSURE,
      $escape=self::DEFAULT_CSV_ESCAPE) {
    if (empty($this->fileHandle)) return(false);
    return(fgetcsv($this->fileHandle, $length, $delimiter, $enclosure));
  } // getcsv

  public function putcsv($fields, $delimiter=self::DEFAULT_CSV_DELIMITER,
      $enclosure=self::DEFAULT_CSV_ENCLOSURE) {
    if (empty($this->fileHandle)) return(false);
    return(fputcsv($this->fileHandle, $fields, $delimiter, $enclosure));
  } // putcsv

  public function gets() {
    if (empty($this->fileHandle)) return(false);
    return(fgets($this->fileHandle));
  } // gets

  public function puts($contents) {
    if ($contents === false) {
CecAppLogger::logError("puts parameter cannot be equal to false");
      return;
    } // if
    return($this->write($contents));
  } // puts

  public function read($length) {
    if (empty($this->fileHandle)) return(false);
    return(fread($this->fileHandle, $length));
  } // read

  public function write($contents) {
    if (empty($this->fileHandle)) return(false);
    return(fwrite($this->fileHandle, $contents));
  } // write

  public function rewind() {
    return($this-> seek(0));
  } // rewind

  public function seek($positionFromStart) {
    if (empty($this->fileHandle)) return(false);
    $status = fseek($this->fileHandle, $positionFromStart);
    if ($status === 0) return(true);
    return(false);
  } // seek

  public function eof() {
    if (empty($this->fileHandle)) return(true);
    return(feof($this->fileHandle));
  } // eof

  public function fileSize() {
    if (!$this->hasValidFileHandle()) return(false);
    $stat = fstat($this->fileHandle);
    if ($stat === false) return(false);
    if (isset($stat[CecUtil::FILE_SYSTEM_ATTR_SIZE])) {
      return($stat[CecUtil::FILE_SYSTEM_ATTR_SIZE]);
    } else {
      return(null);
    } // else
  } // fileSize

  static public function getCatCommandLine($filePath) {
    $fileExtension = CecUtil::getFileNameExtension($filePath);
    if ($fileExtension == CecUtil::EXTENSION_GZ) {
      return(sprintf(CecSystemUtil::CMD_GUNZIP_CAT_SPRINTF, $filePath));
    } else {
      return(sprintf(CecSystemUtil::CMD_CAT_SPRINTF, $filePath));
    } // else
  } // getCatCommandLine

  static public function getTailCommandLine($filePath, $lineCount) {
    $fileExtension = CecUtil::getFileNameExtension($filePath);
    if ($fileExtension == CecUtil::EXTENSION_GZ) {
      return(sprintf(CecSystemUtil::CMD_GUNZIP_TAIL_SPRINTF,
        $filePath, $lineCount));
    } else {
      return(sprintf(CecSystemUtil::CMD_TAIL_SPRINTF,
        $lineCount, $filePath));
    } // else
  } // getTailCommandLine

  public function readPartOfFile($startOffset, $readByteCount=null) {
    if (!$this->hasValidFileHandle()) {
      return(false);
    } // if
    $fsize = $this->fileSize();
    if ($startOffset+$readByteCount > $fsize) {
CecAppLogger::logError("readPartOfFile file only has ".$fsize
  ." bytes and cannot read from ".$startOffset." for ".$readByteCount." bytes");
      return(false);
    } // if
    $status = fseek($this->fileHandle, $startOffset);
    if ($status < 0) {
CecAppLogger::logError("readPartOfFile failed to fseek ".$this->getFileName()
  ." to ".$startOffset);
      return(false);
    } // if
    $count = 0;
    $str = null;
    while (!feof($this->fileHandle)) {
      $readCount = self::READ_BUFFER_SIZE;
      if (!is_null($readByteCount)) {
        $remainCount = $readByteCount - $count;
        if ($remainCount < self::READ_BUFFER_SIZE) {
          $readCount = $remainCount;
        } // if
      } // if
      $buf = fread($this->fileHandle, $readCount);
CecAppLogger::logVariable($buf, "fread byte count=".$readCount);
      if (!$buf) {
CecAppLogger::logError("readPartOfFile failed to read ".$this->getFileName()
  ." after ".$count." bytes");
        return(false);
      } // if
      $str .= $buf;
      $count += strlen($buf);
      if ($count >= $readByteCount) break;
    } // while
    /* double-check */
    if (!is_null($readByteCount)) {
      if (strlen($str) != $readByteCount) {
CecAppLogger::logError("readPartOfFile read ".$count." bytes from "
  .$this->getFileName()." instead of ".$readByteCount);
      return(false);
      } // if
    } // if
    return($str);
  } // readPartOfFile

  public function readLines($lineCount) {
    if (!$this->hasValidFileHandle()) {
      return(false);
    } // if
    if ($lineCount == 0) return('');
    $str = null;
    $count = 0;
    while (!feof($this->fileHandle)) {
      $line = $this->gets();
      if ($line === false) {
CecAppLogger::logError("readLines failed after reading ".$count
  ." lines from ".$this->getFileName());
      } // if
      $str .= $line;
      $count++;
      if ($count >= $lineCount) break;
    } // while
    return($str);
  } // readLines

  public function tell() {
    if (empty($this->fileHandle)) return(null);
    return(@ftell($this->fileHandle));
  } // tell

} // CecFileHandleUtil
?>
