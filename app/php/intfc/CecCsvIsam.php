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
/* CecCsvIsam.php */
$rDir = '';
require_once($rDir.'cec/php/intfc/CecCsvFile.php');

class CecCsvIsam extends CecCsvFile {

  const FIELD_INDEX_TABLE = 'indexTable';
  const FIELD_IS_UNIQUE_KEY = 'isUniqueKey';

  const FIELD_CASE_INSENSITIVE = 'caseInsensitive';
  const FIELD_CONTENTS = 'contents';
  const FIELD_FILE_POSITION = 'filePosition';

  const PROGRESS_EVERY_N_LINES = 10000;

  protected $filePath;
  protected $indexTable;
  protected $hasHeaderRow;
  protected $headerByIndex;
  protected $headerByName;

  public function __construct($maxLineLength=self::MAX_LINE_LENGTH,
      $delimiter=self::DELIMITER, $enclosure=self::ENCLOSURE,
      $nullFieldPattern=null) {
    parent::__construct($maxLineLength, $delimiter, $enclosure,
      $nullFieldPattern);
    $this->filePath = null;
  } // __construct

  static private function logDebug($str) {
    // CecLogger::logInfo($str);
  } // logDebug

  public function openFile($filePath, $hasHeaderRow=false, $fileHandle=null) {
    $this->filePath = $filePath;
    if (is_null($fileHandle)) {
      $fileHandle = fopen($filePath, "r");
      $doNotCloseFileHandle = true;
    } else {
      $doNotCloseFileHandle = false;
    } // else
    if ($fileHandle === false) {
      return(false);
    } // if

    $this->setFileHandle($fileHandle, $doNotCloseFileHandle);
    $this->hasHeaderRow = $hasHeaderRow;
    if ($hasHeaderRow) {
      $this->buildHeader();
    } else {
    if (is_null($this->fileHandle)) return(null);
      $this->headerByIndex = null;
      $this->headerByName = null;
    } // else
    return(true);
  } // openFile

  protected function buildHeader() {
    $this->headerByIndex = $this->getOneRow();
    if (is_null($this->headerByIndex)) return;
    $this->headerByName = array_flip($this->headerByIndex);
  } // buildHeader

  public function columnNameToIndex($columnName) {
    if (is_null($this->headerByName)) return(null);
    if (!isset($this->headerByName[$columnName])) return(null);
    return($this->headerByName[$columnName]);
  } // columnNameToIndex

  public function columnIndexToName($columnIndex) {
    if (is_null($this->headerByIndex)) return(null);
    if (!isset($this->headerByIndex[$columnIndex])) return(null);
    return($this->headerByIndex[$columnIndex]);
  } // columnIndexToName

  protected function getOneRow() {
    return($this->readOneCsvRow());
  } // getOneRow

  static private function makeCaseInsensitive($value) {
    return(strtolower($value));
  } // makeCaseInsensitive

  private function seekToFirstDataRow() {
    if (is_null($this->fileHandle)) return(false);
    fseek($this->fileHandle, 0, SEEK_SET);
    if ($this->hasHeaderRow) {
      /* throw away first row */
      $this->getOneRow();
    } // if
    return(ftell($this->fileHandle));
  } // seekToFirstDataRow

  protected function buildIndex($keyColumn, $isUniqueKey=true, 
      $cacheFileContent=false, $caseInsensitive=false,
      $showProgressEveryNLines=self::PROGRESS_EVERY_N_LINES) {
    $lookupTable = Array();
    if (is_null($this->fileHandle)) {
      return;
    } // if
    $filePosition = $this->seekToFirstDataRow();
    $lineCount = 0;
    while($row = $this->getOneRow()) {
      $lineCount++;
      if (!is_null($showProgressEveryNLines)) {
        if (($lineCount % $showProgressEveryNLines)==0) {
          self::logDebug("Indexed ".$lineCount." lines of ".$this->filePath);
        } // if
      } // if

      /* do not handle rows without key */
      if (!isset($row[$keyColumn])) continue;
      if ($caseInsensitive) {
        $key = self::makeCaseInsensitive($row[$keyColumn]);
      } else {
        $key = $row[$keyColumn];
      } // else

      $entry = Array(
        self::FIELD_FILE_POSITION => $filePosition,
      );
      /* record the file position where the next record starts */
      $filePosition = ftell($this->fileHandle);
      if (!$filePosition) continue;
      if ($cacheFileContent) {
        $entry[self::FIELD_CONTENTS] = $row;
      } // if

      if ($isUniqueKey) {
        if (isset($lookupTable[$key])) {
          /* duplicate key error */
          self::logDebug("duplicate key error:".$key);
          continue;
        } // if
        $lookupTable[$key] = $entry;
      } else {
        if (!isset($lookupTable[$key])) {
          $lookupTable[$key] = Array();
        } // if
        $lookupTable[$key][] = $entry;
      } // else
    } // while
    if (!isset($this->indexTable)) {
      $this->indexTable = Array();
    } // if
    $this->indexTable[$keyColumn] = Array (
      self::FIELD_IS_UNIQUE_KEY => $isUniqueKey,
      self::FIELD_INDEX_TABLE => $lookupTable,
      self::FIELD_CASE_INSENSITIVE => $caseInsensitive,
    ); // Array
  } // buildIndex

  public function selectByIndex($keyColumn, $key) {
    if (is_null($this->fileHandle)) return(null);
    if (empty($key)) return(null);
    if (!isset($this->indexTable[$keyColumn])) return(null);
    if ($this->indexTable[$keyColumn][self::FIELD_CASE_INSENSITIVE]) {
      $key = self::makeCaseInsensitive($key);
    } // if

    $lookupTable = $this->indexTable[$keyColumn][self::FIELD_INDEX_TABLE];
    self::logDebug("selectByIndex found lookupTable of keyColumn ".$keyColumn);


    if (!isset($lookupTable[$key])) {
      self::logDebug("selectByIndex cannot find key ".$key
        ." in lookupTable with count ".count($lookupTable));
      return(null);
    } // if
    $entry = $lookupTable[$key];
    // CecLogger::logVariable($entry, "selectByIndex found entry of key ".$key);

    if ($this->indexTable[$keyColumn][self::FIELD_IS_UNIQUE_KEY]) {
      /* unique key */
      if (isset($entry[self::FIELD_CONTENTS])) {
        self::logDebug("selectByIndex returns unique key content from memory.");
        /* return from memory */
        return($entry[self::FIELD_CONTENTS]);
      } else {
        /* return from disk */
        fseek($this->fileHandle, $entry[self::FIELD_FILE_POSITION], SEEK_SET);
        self::logDebug("selectByIndex returns unique key content from disk.");
        return($this->getOneRow());
      } // else
    } else {
      /* non-unique key */
      $returnArray = Array();
      foreach($entry as $e) {
        if (isset($e[self::FIELD_CONTENTS])) {
          self::logDebug("selectByIndex returns non-unique key content from memory.");
          /* return from memory */
          $returnArray[] = $e[self::FIELD_CONTENTS];
        } else {
          /* return from disk */
          fseek($this->fileHandle, $e[self::FIELD_FILE_POSITION], SEEK_SET);
          self::logDebug("selectByIndex returns non-unique key content from disk at "
            .$e[self::FIELD_FILE_POSITION]);
          $returnArray[] = $this->getOneRow();
        } // else
      } //  foreach
      self::logDebug("selectByIndex returns item count=".count($returnArray));
      return($returnArray);
    } // else
  } // selectByIndex

  public function replaceKeyIndexByKeyNames($row) {
    if (is_null($row)) return(null);
    if (!is_array($row)) return($row);
    $returnRow = Array();
    foreach($row as $columnIndex => $value) {
      $columnName = $this->columnIndexToName($columnIndex);
      if (is_null($columnName)) {
        /* TODO: error handling */
        return(null);
      } // if
      $returnRow[$columnName] = $value;
    } // foreach
    return($returnRow);
  } // replaceKeyIndexByKeyNames

} // CecCsvIsam
?>
