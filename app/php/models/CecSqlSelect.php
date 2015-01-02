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
require_once($rDir.'cec/php/models/CecSqlStatement.php');

class CecSqlSelect extends CecSqlStatement {

  protected $tableAlias;
  protected $selectFieldArray;
  protected $whereClause;
  protected $orderBy;
  protected $limitCount;
  protected $distinctFlag;

  public function __construct($table, $tableAlias=null) {
    parent::__construct($table);
    $this->tableAlias = $tableAlias;
    $this->selectFieldArray = Array();
    $this->whereClause = null;
    $this->orderBy = null;
    $this->limitCount = null;
    $this->distinctFlag = FALSE;
  } // __construct

  protected function prefixTableAlias($field) {
    if (is_null($this->tableAlias)) return($field);
    return($this->tableAlias.'.'.$field);
  } // prefixTableAlias

  public function setDistinctFlag($distinctFlag) {
    $this->distinctFlag = $distinctFlag;
  } // setDistinctFlag

  public function addSelectField($field) {
    if (is_array($field)) {
      foreach($field as $f) {
        if (!is_null($f)) {
          $this->selectFieldArray[] = $this->prefixTableAlias($f);
        } // if
      } // foreach
    } else {
      $this->selectFieldArray[] = $this->prefixTableAlias($field);
    }
  } // addSelectField

  public function setSelectFieldArray($fieldArray) {
    $this->selectFieldArray = Array();
    if (is_null($fieldArray)) {
      return;
    } // if
    foreach($fieldArray as $field) {
      $this->selectFieldArray[] = $this->prefixTableAlias($field);
    } // foreach
  } // setSelectFieldArray

  public function setWhere($whereClause) {
    $this->whereClause = $whereClause;
  } // setWhere

  public function setOrderBy($orderBy) {
    $this->orderBy = self::flattenOrderBy($orderBy);
  } // setOrderBy

  public function setLimit($limit) {
    $this->limit = $limit;
  } // setLimit

  public function toString() {
    if (count($this->selectFieldArray)==0) {
      return(null);
    } // if

    $str = ' SELECT ';
    if ($this->distinctFlag) {
      $str .= 'DISTINCT ';
    } // if
    $str .= implode(',', $this->selectFieldArray)
      .' FROM '.$this->tableName;
    if (!is_null($this->tableAlias)) {
      $str .= ' AS '.$this->tableAlias;
    } // if

    $filter = $this->injectSubclassIntoFilter($this->whereClause);
    if (!is_null($filter)) {
      $str .= ' WHERE '.$filter;
    } // if

    if (!is_null($this->orderBy)) {
      $str .= ' ORDER BY '.$this->orderBy;
    } // if

    if (!is_null($this->limitCount)) {
      $str .= ' LIMIT '.$this->limitCount;
    } // if

    return($str);
  } // toString

} // CecSqlSelect
?>
