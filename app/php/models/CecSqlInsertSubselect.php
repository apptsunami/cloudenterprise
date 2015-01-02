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

class CecSqlInsertSubselect extends CecSqlStatement {

  protected $selectFieldArray;
  protected $subSelect;
  public $ignore;

  public function __construct($table, $ignore=1) {
    parent::__construct($table);
    $this->ignore = $ignore;
    $this->selectFieldArray = Array();
    $this->subSelect = null;
  } // __construct

  public function enableIgnore() {
    $this->ignore = 1;
  } // enableIgnore

  public function disableIgnore() {
    $this->ignore = 0;
  } // disableIgnore

  public function addSelectField($fields) {
    if (is_array($fields)) {
      $this->selectFieldArray = array_merge($this->selectFieldArray, $fields);
    } else {
      $this->selectFieldArray[] = $fields;
    } // else
  } // addSelectField

  public function setSubselect($subSelect) {
    $this->subSelect = $subSelect;
  } // setSubselect

  public function toString($doIgnore=null) {
    if (count($this->selectFieldArray)==0) {
      return(null);
    } // if
    if (is_null($this->subSelect)) {
      return(null);
    } // if

    $this->injectSubclassIntoStatement();
    $ignore = (is_null($doIgnore)?$this->ignore:$doIgnore);

    $escapedFields = '';
    foreach ($this->selectFieldArray as $k) {
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
    $str .= 'INTO '.$this->tableName
      .'('.$escapedFields.')'
      .$this->subSelect;
    $str .= ';';
    return($str);
  } // toString

  public function getOriginalValuePairArray() {
    return($this->originalValuePairArray);
  } // getOriginalValuePairArray

} // CecSqlInsertSubselect
?>
