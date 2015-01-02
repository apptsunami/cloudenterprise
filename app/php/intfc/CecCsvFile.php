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
/* CecCsvFile.php */
$rDir = '';

class CecCsvFile {

  const MAX_LINE_LENGTH = 65535;
  const DELIMITER = ',';
  const ENCLOSURE = '"';

  const NULL_VALUE_PATTERN = 'NULL';

  protected $fileHandle;
  protected $maxLineLength;
  protected $delimiter;
  protected $enclosure;
  protected $nullFieldPattern;
  private $doNotCloseFileHandle;

  public function __construct($maxLineLength=self::MAX_LINE_LENGTH,
      $delimiter=self::DELIMITER, $enclosure=self::ENCLOSURE,
      $nullFieldPattern=null) {
    $this->maxLineLength = $maxLineLength;
    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    $this->nullFieldPattern = $nullFieldPattern;
    $this->doNotCloseFileHandle = false;
  } // __construct

  public function __destruct() {
    $this->closeFile();
  } // __destruct

  protected function setFileHandle($fileHandle, $doNotCloseFileHandle=false) {
    $this->fileHandle = $fileHandle;
    $this->doNotCloseFileHandle = $doNotCloseFileHandle;
  } // setFileHandle

  protected function closeFileHandle($fileHandle) {
    @fclose($fileHandle);
  } // closeFileHandle

  protected function closeFile() {
    if (!empty($this->fileHandle) && !($this->doNotCloseFileHandle)) {
      $this->closeFileHandle($this->fileHandle);
    } // else
    unset($this->fileHandle);
    $this->doNotCloseFileHandle = false;
  } // closeFile

  private function _convertNullValues($fields, $searchValue, $replacementValue) {
    if (is_null($this->nullFieldPattern)) {
      return($fields);
    } // if
    $newFields = Array();
    foreach($fields as $key => $value) {
      if ((is_null($searchValue) && is_null($value)) ||
          (strcasecmp($value, $searchValue)==0)) {
        $newFields[$key] = $replacementValue;
      } else {
        $newFields[$key] = $value;
      } // else
    } // foreach
    return($newFields);
  } // _convertNullValues

  public function readOneCsvRow() {
    if (empty($this->fileHandle)) {
      return(null);
    } // if
    $fields = fgetcsv($this->fileHandle, $this->maxLineLength,
      $this->delimiter, $this->enclosure);
    if (!$fields) return(false);
    return($this->_convertNullValues($fields, $this->nullFieldPattern, null));
  } // readOneCsvRow

  public function writeOneCsvRow($fields) {
    $fields = $this->_convertNullValues($fields, null, $this->nullFieldPattern);
    return(fputcsv($this->fileHandle, $fields, $this->delimiter,
      $this->enclosure));
  } // writeOneCsvRow

  public function isEndOfFile() {
    if (empty($this->fileHandle)) return(true);
    return(feof($this->fileHandle));
  } // isEndOfFile

} // CecCsvFile
?>
