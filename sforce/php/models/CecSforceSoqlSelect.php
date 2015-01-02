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

class CecSforceSoqlSelect extends CecSqlStatement {

  protected $selectFieldArray;
  protected $whereClause;
  protected $orderBy;
  protected $limitCount;
  protected $distinctFlag;

  public function __construct($table) {
    parent::__construct($table);
    $this->selectFieldArray = Array();
    $this->whereClause = null;
    $this->orderBy = null;
    $this->limitCount = null;
    $this->distinctFlag = FALSE;
  } // __construct

  public function setDistinctFlag($distinctFlag) {
    $this->distinctFlag = $distinctFlag;
  } // setDistinctFlag

  public function addSelectField($field) {
    if (is_array($field)) {
      foreach($field as $f) {
        $this->selectFieldArray[] = $f;
      } // foreach
    } else {
      $this->selectFieldArray[] = $field;
    }
  } // addSelectField

  public function setSelectFieldArray($fieldArray) {
    $this->selectFieldArray = $fieldArray;
  } // setSelectFieldArray

  public function setWhere($whereClause) {
    $this->whereClause = $whereClause;
  } // setWhere

  public function setOrderBy($orderBy) {
    $this->orderBy = $orderBy;
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
    if (is_array($this->selectFieldArray)) {
      $str .= implode(',', $this->selectFieldArray);
    } else {
      $str .= $this->selectFieldArray;
    } // else

    $str .= ' FROM '.$this->tableName;

    $filter = $this->whereClause;
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

} // CecSforceSoqlSelect
?>
