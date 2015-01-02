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
require_once($rDir.'cec/php/models/CecSqlInsert.php');

class CecSqlUpdate extends CecSqlInsert {

  protected $whereClause;
  protected $orderBy;
  protected $limitCount;
  protected $tableAlias;
  protected $additionalTableArray;

  public function __construct($table, $ignore=1, $tableAlias=null,
      $additionalTableArray=null) {
    parent::__construct($table, $ignore);
    $this->tableAlias = $tableAlias;
    $this->additionalTableArray = $additionalTableArray;
  } // __construct

  protected function prefixTableAlias($field) {
    if (is_null($this->tableAlias)) return($field);
    return($this->tableAlias.'.'.$field);
  } // prefixTableAlias

  protected function formatOnDuplicate($field, $value) {
    return($field.'='.$this->formatBoolean($value));
  } // formatOnDuplicate

  public function addRawFieldValuePair($field, $value, $ignore=null) {
    parent::addRawFieldValuePair($this->prefixTableAlias($field), $value, 1);
  } // addRawFieldValuePair

  public function addStringFieldValuePair($field, $value, $ignore=null) {
    parent::addStringFieldValuePair($this->prefixTableAlias($field), $value, 1);
  } // addStringFieldValuePair

  public function addEnumFieldValuePair($field, $value, $ignore=null) {
    parent::addStringFieldValuePair($this->prefixTableAlias($field), $value, 1);
  } // addEnumFieldValuePair

  public function addNonStringFieldValuePair($field, $value, $ignore=null) {
    parent::addNonStringFieldValuePair($this->prefixTableAlias($field), $value, 1);
  } // addNonStringFieldValuePair

  public function addFloatFieldValuePair($field, $value, $decimals, $ignore=null) {
    parent::addFloatFieldValuePair($this->prefixTableAlias($field), $value,
      $decimals, 1);
  } // addFloatFieldValuePair

  public function addIncrementFieldValuePair($field, $increment=1, $ignore=null) {
    if (!is_numeric($increment)) return;
    $value = floatval($increment);
    if ($value >= 0) {
      $value = strval($value);
      if ($value[0] != '+') {
        $value = '+'.$value;
      } // if
    } else {
      $value = strval($value);
      if ($value[0] != '-') {
        $value = '-'.$value;
      } // if
    } // else
    parent::addNonStringFieldValuePair($this->prefixTableAlias($field),
      $this->prefixTableAlias($field).$value, 1);
  } // addIncrementFieldValuePair

  public function addNowFieldValuePair($field, $ignore=null) {
    parent::addNowFieldValuePair($this->prefixTableAlias($field), 1);
  } // addNowFieldValuePair

  public function addMD5FieldValuePair($field, $value, $ignore=null) {
    parent::addMD5FieldValuePair($this->prefixTableAlias($field), $value, 1);
  } // addMD5FieldValuePair

  public function addUnixNowFieldValuePair($field, $ignore=null) {
    parent::addUnixNowFieldValuePair($this->prefixTableAlias($field), 1);
  } // addUnixNowFieldValuePair

  public function addBooleanFieldValuePair($field, $value, $ignore=null) {
    parent::addBooleanFieldValuePair($this->prefixTableAlias($field), $value, 1);
  } // addBooleanFieldValuePair

  public function setWhereClause($whereClause) {
    $this->setWhere($whereClause);
  } // setWhereClause

  public function setWhere($whereClause) {
    $this->whereClause = $whereClause;
  } // setWhere

  public function setOrderBy($orderBy) {
    $this->orderBy = self::flattenOrderBy($orderBy);
  } // setOrderBy

  public function setLimitCount($limitCount) {
    $this->limitCount = $limitCount;
  } // setLimitCount

  public function setWhereObjidClause($objid) {
    $autoIncrementFieldName = $this->prefixTableAlias(
      $this->table->getAutoIncrementFieldName());
    if (is_array($objid)) {
      $this->whereClause = $autoIncrementFieldName.' IN ('
        .implode(',',$objid).')';
    } else {
      $this->whereClause = $autoIncrementFieldName.'='.$objid;
    } // else
  } // setWhereObjidClause

  public function toString($ignore=null) {
    if (is_null($ignore)) {
      $ignore = $this->ignore;
    } // if
    if (count($this->onDuplicateUpdateArray)==0) {
      return(null);
    } // if

    $str = ' UPDATE ';
    if ($ignore) {
      $str .= 'IGNORE ';
    } // if
    $str .= $this->tableName;
    if (!is_null($this->tableAlias)) {
      $str .= ' AS '.$this->tableAlias;
    } // if
    if (!is_null($this->additionalTableArray)) {
      foreach($this->additionalTableArray as $tableName => $alias) {
        $str .= ', '.$tableName.' AS '.$alias;
      } // foreach
    } // if
    $str .= ' SET '
      .implode(',', $this->onDuplicateUpdateArray);
    if (!empty($this->whereClause)) {
      $str .= ' WHERE '.$this->injectSubclassIntoFilter($this->whereClause);
    } // if
    if (!empty($this->orderBy)) {
      $str .= ' ORDER BY '.trim($this->orderBy);
    } // if
    if (!empty($this->limitCount)) {
      $str .= ' LIMIT '.trim($this->limitCount);
    } // if
    return($str.';');
  } // toString

} // CecSqlUpdate
?>
