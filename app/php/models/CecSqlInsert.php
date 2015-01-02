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
$rDir = '';
require_once($rDir.'cec/php/models/CecDataTable.php');
require_once($rDir.'cec/php/models/CecSqlStatement.php');
require_once($rDir.'cec/php/utils/CecUtil.php');

class CecSqlInsert extends CecSqlStatement {

  const ON_DUPLICATE_NOOP = 0;
  const ON_DUPLICATE_ASSIGN = 1;
  const ON_DUPLICATE_ADD = 2;
  const ON_DUPLICATE_SUBTRACT = 3;
  const ON_DUPLICATE_CONCAT = 4;

  const FIELD_VALUE_SEP = ",";

  public $ignore;
  public $setLastInsertId;
  protected $originalValuePairArray;
  protected $valuePairArray;
  protected $onDuplicateUpdateArray;
  private $selectKeywordSyntax;

  public function __construct($table, $ignore=1) {
    parent::__construct($table);
    $this->ignore = $ignore;
    $this->originalValuePairArray = Array();
    $this->valuePairArray = Array();
    $this->onDuplicateUpdateArray = Array();
    $this->setLastInsertId = 1;
    $this->selectKeywordSyntax = false;
  } // __construct

  public function enableIgnore() {
    $this->ignore = 1;
  } // enableIgnore

  public function disableIgnore() {
    $this->ignore = 0;
  } // disableIgnore

  public function useSelectKeywordSyntax() {
    $this->selectKeywordSyntax = true;
  } // useSelectKeywordSyntax

  protected function formatBoolean($bool) {
    if (is_array($bool)) {
      $returnArray = Array();
      foreach($bool as $b) {
        $returnArray[] = $this->formatBoolean($b);
      } // foreach
      return($returnArray);
    } else {
      if ($bool === TRUE) return('TRUE');
      if ($bool === FALSE) return('FALSE');
      return($bool);
    } // else
  } // formatBoolean

  protected function formatOnDuplicate($field, $value, $onDuplicateUpdate) {
    $valuesStr = 'VALUES('.$field.')';
    switch($onDuplicateUpdate) {
    case self::ON_DUPLICATE_ASSIGN:
      return($field.'='.$valuesStr);
    case self::ON_DUPLICATE_ADD:
      return($field.'='.$field.'+'.$valuesStr);
    case self::ON_DUPLICATE_SUBTRACT:
      return($field.'='.$field.'-'.$valuesStr);
    case self::ON_DUPLICATE_CONCAT:
      return($field.'=CONCAT('.$field.','.$valuesStr.')');
    case self::ON_DUPLICATE_NOOP:
    default:
      return(null);
    } // switch
  } // formatOnDuplicate

  private function _addToArray($field, $value, $onDuplicateUpdate) {
    $this->valuePairArray[$field] = $this->formatBoolean($value);
    $dup = $this->formatOnDuplicate($field, $value, $onDuplicateUpdate);
    if (!empty($dup)) {
      $this->onDuplicateUpdateArray[] = $dup;
    } // if
  } // _addToArray

  public function addRawFieldValuePair($field, $value,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP) {
    $this->originalValuePairArray[$field] = $value;
    $this->_addToArray($field, CecDataTable::wrapNull($value),
      $onDuplicateUpdate);
  } // addRawFieldValuePair

  public function addStringFieldValuePair($field, $value,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP, $trimValue=true) {
    if ($trimValue && (!is_null($value))) {
      $value = trim($value);
    } // if
    $this->originalValuePairArray[$field] = $value;
    $this->_addToArray($field, CecDataTable::wrapNullAndQuote($value),
      $onDuplicateUpdate);
  } // addStringFieldValuePair

  public function addEnumFieldValuePair($field, $value,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP, $trimValue=true) {
    if ($trimValue) {
      $value = trim($value);
    } // if
    return $this->addStringFieldValuePair($field, $value, $onDuplicateUpdate);
  } // addEnumFieldValuePair

  public function addFloatFieldValuePair($field, $value, $decimals,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP, $emptyStringIsNull=false) {
    if ($emptyStringIsNull && (is_null($value) || ($value==''))) {
      $value = null;
    } else {
      if (is_bool($value)) {
        $value = ($value?1:0);
      } else if (!is_float($value) && !is_int($value)) {
        $value = CecUtil::strtofloat($value);
      } // if
      $value = number_format($value, $decimals, '.', '');
    } // else
    return $this->addStringFieldValuePair($field, $value, $onDuplicateUpdate, true);
  } // addFloatFieldValuePair

  public function addNullableFloatFieldValuePair($field, $value, $decimals,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP) {
    return $this->addFloatFieldValuePair($field, $value, $decimals,
      $onDuplicateUpdate, true);
  } // addNullableFloatFieldValuePair

  public function addNonStringFieldValuePair($field, $value,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP, $trimValue=true) {
    if ($trimValue && !is_null($value)) {
      $value = trim($value);
    } // if
    $this->originalValuePairArray[$field] = $value;
    if ($value==='') $value = null;
    $this->_addToArray($field, CecDataTable::wrapNull($value),
      $onDuplicateUpdate);
  } // addNonStringFieldValuePair

  public function addNowFieldValuePair($field,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP) {
    $this->originalValuePairArray[$field] = CecDataTable::FUNCTION_NOW;
    $this->_addToArray($field, CecDataTable::FUNCTION_NOW, $onDuplicateUpdate);
  } // addNowFieldValuePair

  public function addMD5FieldValuePair($field, $value,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP) {
    $md5 = CecDataTable::md5($value);
    $this->originalValuePairArray[$field] = $md5;
    $this->_addToArray($field, $md5, $onDuplicateUpdate);
  } // addMD5FieldValuePair

  public function addUnixNowFieldValuePair($field,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP) {
    $this->originalValuePairArray[$field] = CecDataTable::FUNCTION_UNIX_NOW;
    $this->_addToArray($field, CecDataTable::FUNCTION_UNIX_NOW, $onDuplicateUpdate);
  } // addUnixNowFieldValuePair

  public function addNullFieldValuePair($field,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP) {
    $this->originalValuePairArray[$field] = null;
    $this->addNonStringFieldValuePair($field, CecDataTable::STR_NULL,
      $onDuplicateUpdate);
  } // addNullFieldValuePair

  public function addDateTimeFieldValuePair($field, $value,
      $onDuplicateUpdate=0, $unixTimestampValue=false) {
    $this->originalValuePairArray[$field] = $value;
    if (!is_null($value) && (trim($value) != '')) {
      if ($unixTimestampValue) {
        $timestamp = $value;
      } else {
        $timestamp = strtotime($value);
        if ($timestamp === FALSE) {
          /* use the date-time value as is */
          $this->addRawFieldValuePair($field, $value, $onDuplicateUpdate);
          return;
        } // if
      } // else
      $value = date(DATE_ATOM, $timestamp);
    } // if
    $this->addStringFieldValuePair($field, $value, $onDuplicateUpdate);
  } // addDateTimeFieldValuePair

  public function addBooleanFieldValuePair($field, $value,
      $onDuplicateUpdate=self::ON_DUPLICATE_NOOP) {
    $this->originalValuePairArray[$field] = $value;
    $this->_addToArray($field, CecDataTable::wrapNull($value),
      $onDuplicateUpdate);
  } // addBooleanFieldValuePair

  static private function _expandRemainingPairs($strBefore, $remainingPairsArray) {
    $headValue = array_shift($remainingPairsArray);
    if (is_null($headValue)) return($strBefore);
    if (!is_null($strBefore) && ($strBefore != "")) {
      $strBefore .= self::FIELD_VALUE_SEP;
    } // if
    if (!is_array($headValue)) {
      /* recursion */
      return(self::_expandRemainingPairs($strBefore.$headValue,
        $remainingPairsArray));
    } else {
      $returnArray = Array();
      foreach($headValue as $hv) {
        /* recursion */
        $returnArray[] = self::_expandRemainingPairs($strBefore.$hv,
          $remainingPairsArray);
      } // foreach
      return($returnArray);
    } // else
  } // _expandRemainingPairs

  private function generateValuesPhrase() {
    if ($this->selectKeywordSyntax) {
      $phrase = implode(',', array_values($this->valuePairArray));
      return(' SELECT '.$phrase);
    } // if
    $phrase = self::_expandRemainingPairs(null, $this->valuePairArray);
    $str = null;
    if (!is_array($phrase)) {
      $str .= '('.$phrase.')';
    } else {
      foreach($phrase as $ph) {
        if (!empty($str)) {
          /* new line is for debug output readability */
          $str .= self::FIELD_VALUE_SEP."\n";
        } // if
        $str .= '('.$ph.')';
      } // foreach
    } // else
    return(' VALUES '.$str);
  } // generateValuesPhrase

  public function toString($doIgnore=null) {
    if (count($this->valuePairArray)==0) {
      return(null);
    } // if
    $this->injectSubclassIntoStatement();
    $ignore = (is_null($doIgnore)?$this->ignore:$doIgnore);

    $escapedFields = '';
    foreach (array_keys($this->valuePairArray) as $k) {
        $escapedFields .= "`$k`,";
    } // foreach
    if ($escapedFields != '') {
        $escapedFields = substr($escapedFields, 0, -1);
    } // if
    $autoIncrementFieldName = $this->table->getAutoIncrementFieldName();

    $str = ' INSERT ';
    if ($ignore==1) {
      $str .= 'IGNORE ';
    } // if
    $str .= 'INTO '.$this->tableName.'('.$escapedFields.')'
      .$this->generateValuesPhrase();
    $duaCount = count($this->onDuplicateUpdateArray);
    if (($duaCount > 0) || ($this->setLastInsertId == 1)) {
      $str .= ' ON DUPLICATE KEY UPDATE ';
      if ($this->setLastInsertId == 1) {
         $str .= ' '.$autoIncrementFieldName.'=LAST_INSERT_ID('
           .$autoIncrementFieldName.')';
         if ($duaCount > 0) {
           $str .= self::FIELD_VALUE_SEP;
         } // if
      } // if
      $str .= implode(self::FIELD_VALUE_SEP, $this->onDuplicateUpdateArray);
    } // if
    $str .= ';';
    return($str);
  } // toString

  public function getOriginalValuePairArray() {
    return($this->originalValuePairArray);
  } // getOriginalValuePairArray

  public function getFieldValue($fieldName) {
    if (!isset($this->originalValuePairArray[$fieldName])) return(false);
    return($this->originalValuePairArray[$fieldName]);
  } // getFieldValue

} // CecSqlInsert
?>
