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
/* CecUploadFileUnzipper */
$rDir = '';
require_once($rDir.'cec/php/utils/CecCompressorUtil.php');
require_once($rDir.'cec/php/utils/CecHttpUtil.php');
require_once($rDir.'cec/php/utils/CecUtil.php');

class CecUploadFileUnzipper {

  const DECOMPRESS_DIR_EXTENSION = 'unzip';
  const TMP_DIRECTORY = '/tmp';

  private $tmpDir;
  private $dataFile;
  private $unzipDir;
  private $filePathArray;
  private $originalName;
  private $filePathIndex;
  private $mimeType;

  public function __construct($tmpDir=self::TMP_DIRECTORY) {
    $this->tmpDir = $tmpDir;
    $this->reset();
  } // __construct

  public function __destruct() {
    $this->cleanUp();
  } // __destruct

  public function reset() {
    $this->dataFile = null;
    $this->unzipDir = null;
    $this->filePathArray = null;
    $this->originalName = null;
    $this->filePathIndex = null;
    $this->mimeType = null;
  } // reset

  private function unzipFile($tmpName, $outputDir=null) {
    if (is_null($outputDir)) {
      $outputDir= $tmpName;
    } // if
    $outputDir .= '.'.self::DECOMPRESS_DIR_EXTENSION;
    CecUtil::createDirectory($outputDir);
    CecCompressorUtil::decompressFile($tmpName, 
      CecCompressorUtil::COMPRESSOR_ZIP, $outputDir);
    return $outputDir;
  } // unzipFile

  public function openUploadFile($dataFile) {
    $this->reset();
    if (empty($dataFile)) return;
    $this->dataFile = $dataFile;
    $this->originalName = $dataFile[CecHttpUtil::MIME_FILE_ATTR_NAME];
    $mimeType = $dataFile[CecHttpUtil::MIME_FILE_ATTR_TYPE];
    if ($mimeType == CecHttpUtil::MIME_TYPE_ZIP) {
      $this->unzipDir = $this->unzipFile(
        $dataFile[CecHttpUtil::MIME_FILE_ATTR_TMP_NAME]);
      $this->filePathArray = CecUtil::getFilesInDir($this->unzipDir, true,
        false, null, null, null, true, true);
      $this->mimeType = null;
    } else {
      $tmpName = $dataFile[CecHttpUtil::MIME_FILE_ATTR_TMP_NAME];
      $this->filePathArray = Array($tmpName);
      $this->mimeType = $dataFile[CecHttpUtil::MIME_FILE_ATTR_TYPE];
    } // else
    $this->filePathIndex = 0;
  } // openUploadFile

  public function openLocalFile($filePath) {
    $this->reset();
    if (empty($filePath)) return;
    $this->filePath = $filePath;
    $this->originalName = $filePath;
    $pathInfo = CecUtil::getFilePathInfo($filePath);
    $ext = $pathInfo[CecUtil::FILE_PATH_INFO_EXTENSION];
    switch($ext) {
    case 'gz':
      $mimeType = CecHttpUtil::MIME_TYPE_GZIP;
      break;
    case 'zip':
      $mimeType = CecHttpUtil::MIME_TYPE_ZIP;
      break;
    case 'txt':
    case 'csv':
      $mimeType = CecHttpUtil::MIME_TEXT_PLAIN;
      break;
    default:
      $mimeType = CecUtil::getMimeType($filePath);
    }
    if ($mimeType == CecHttpUtil::MIME_TYPE_ZIP) {
      $pathInfo = CecUtil::getFilePathInfo($filePath);
      $fileName = CecUtil::convertToLegalFileName(
        $pathInfo[CecUtil::FILE_PATH_INFO_FILENAME]);
      $outputDir = CecUtil::generateFilePathInDirectory($this->tmpDir, $fileName);
      $this->unzipDir = $this->unzipFile($filePath, $outputDir);
      $this->filePathArray = CecUtil::getFilesInDir($this->unzipDir, true,
        null, null, null, true, true);
      $this->mimeType = null;
    } else {
      $this->filePathArray = Array($filePath);
      $this->mimeType = $mimeType;
    } // else
    $this->filePathIndex = 0;
  } // openLocalFile

  /* returns filePath, mimeType */
  public function getNextFile() {
    if (empty($this->filePathArray)) return null;
    if ($this->filePathIndex == 0) {
      $f = current($this->filePathArray);
    } else {
      $f = next($this->filePathArray);
    } // else
    $this->filePathIndex++;
    if (!$f) return null;
    if (!is_null($this->mimeType)) {
      return Array($f, $this->mimeType);
    } else {
      return Array($f, CecUtil::getMimeType($f));
    } // else
  } // getNextFile

  public function cleanUp() {
    if (!empty($this->unzipDir)) {
      CecUtil::deleteDirectory($this->unzipDir);
    } // if
    $this->reset();
  } // cleanUp

} // CecUploadFileUnzipper
