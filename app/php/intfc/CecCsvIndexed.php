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
/* CecCsvIndexed.php */
$rDir = '';
require_once($rDir."cec/php/intfc/CecCsvFile.php");
require_once($rDir."cec/php/utils/CecHttpUtil.php");
require_once($rDir."cec/php/utils/CecUtil.php");

class CecCsvIndexed extends CecCsvFile {

  const MAX_LINE_LENGTH = 4096;
  const DELIMITER = ',';
  const ENCLOSURE = '"';
  const DEFAULT_FLAG_TRIM_HEADER = true;
  const CFG_AUTO_DETECT_LINE_ENDINGS = 'auto_detect_line_endings';

  protected $fileContent;
  protected $filePath;
  private $columnNames;
  private $trimColumnNames;
  private $exactColumnCount;
  private $mimeType;
  private $auto_detect_line_endings;

  public function __construct($filePath, $maxLineLength=self::MAX_LINE_LENGTH,
      $delimiter=self::DELIMITER, $enclosure=self::ENCLOSURE, $readAll=true,
      $trimColumnNames=self::DEFAULT_FLAG_TRIM_HEADER, $nullFieldPattern=null,
      $exactColumnCount=true, $mimeType=null) {
    parent::__construct($maxLineLength, $delimiter, $enclosure, $nullFieldPattern);
    $this->auto_detect_line_endings = ini_get(self::CFG_AUTO_DETECT_LINE_ENDINGS);
    ini_set(self::CFG_AUTO_DETECT_LINE_ENDINGS, true);
    $this->exactColumnCount = $exactColumnCount;
    $this->filePath = $filePath;
    if (is_null($mimeType)) {
      $this->mimeType = CecUtil::getMimeType($filePath);
    } else {
      $this->mimeType = $mimeType;
    } // else
    $fileHandle = $this->openFileHandle($filePath, 'r');
    if (!$fileHandle) return;
    $this->setFileHandle($fileHandle, false);
    $this->trimColumnNames = $trimColumnNames;
    if ($readAll) {
      $this->readAllFileContent();
      $this->closeFile();
    } else {
      $this->readHeader();
    } // else
  } // __construct

  public function __destruct() {
    ini_set(self::CFG_AUTO_DETECT_LINE_ENDINGS, $this->auto_detect_line_endings);
  } // __destruct

  protected function openFileHandle($filePath, $mode) {
    if ($this->mimeType == CecHttpUtil::HTTP_HEADER_VALUE_APPLICATION_OCTET_STREAM) {
      throw new Exception("Indeterminant mime type ".$this->mimeType);
    } else if ($this->mimeType == CecHttpUtil::MIME_TYPE_GZIP) {
      $fh = @gzopen($filePath, $mode);
    } else {
      $fh = @fopen($filePath, $mode);
    } // else
    return $fh;
  } // openFileHandle

  protected function closeFileHandle($fileHandle) {
    if ($this->mimeType == CecHttpUtil::MIME_TYPE_GZIP) {
      @gzclose($fileHandle);
    } else {
      @fclose($fileHandle);
    } // else
  } // closeFileHandle

  public function getFilePath() {
    return($this->filePath);
  } // getFilePath

  public function getFileContent() {
    return($this->fileContent);
  } // getFileContent

  protected function readHeader() {
    $row = $this->readOneCsvRow();
    if (!$row) {
      $this->columnNames = null;
      return(false);
    } // if
    $this->setColumnNames($row);
    return(true);
  } // readHeader

  public function getColumnNames() {
    return($this->columnNames);
  } // getColumnNames

  protected function processColumnName($col) {
    return($col);
  } // processColumnName

  public function setColumnNames($columnNames) {
    $this->columnNames = Array();
    foreach($columnNames as $col) {
      if ($this->trimColumnNames) {
        $col = trim($col);
      } // if
      $this->columnNames[] = $this->processColumnName($col);
    } // foreach
  } // setColumnNames

  protected function readAllFileContent() {
    $hasHeader = $this->readHeader();
    if (!$hasHeader) {
      return;
    } // if
    $this->fileContent = Array();
    $this->_readData($this->fileContent, true);
  } // readAllFileContent

  public function readOneDataRow() {
    $dataArray = Array();
    $this->_readData($dataArray, false);
    if (count($dataArray) == 0) return(false);
    return($dataArray[0]);
  } // readOneDataRow

  protected function postProcessReadData($data) {
    /* hook for changing the read data before storing */
    return($data);
  } // postProcessReadData

  private function _readData(&$dataArray, $readAll=true) {
    $columnCount = count($this->columnNames);
    while($row = $this->readOneCsvRow()) {
      $fieldCount = count($row);
      if ($this->exactColumnCount) {
        if ($fieldCount != $columnCount) {
          continue;
        } // if
      } // if
      $columnCount = count($this->columnNames);
      $indexedData = Array();
      for($i=0; $i<$fieldCount; $i++) {
        $field = $row[$i];
        if ($i < $columnCount) {
          $columnName = $this->columnNames[$i];
        } else {
          $columnName = $i;
        } // else
        $indexedData[$columnName] = $field;
      } // foreach
      if ($fieldCount < $columnCount) {
        for($j=$fieldCount; $j<$columnCount; $j++) {
          $columnName = $this->columnNames[$j];
          $indexedData[$columnName] = null;
        } // for
      } // if

      $indexedData = $this->postProcessReadData($indexedData);
      if (!is_null($indexedData)) {
        $dataArray[] = $indexedData;
      } // if
      if (!$readAll) return;
    } // while
  } // _readData

} // CecCsvIndexed
?>
