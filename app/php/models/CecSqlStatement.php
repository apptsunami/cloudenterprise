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
class CecSqlStatement {

  protected $table;
  protected $tableName;

  public function __construct($table) {
    $this->table = $table;
    $this->tableName = $table->getTableName();
  } // __construct

  static function isNullDate($dateValue) {
    if (is_null($dateValue)) return(TRUE);
    if ($dateValue == '0000-00-00') return(TRUE);
    return(FALSE);
  } // isNullDate

  protected function injectSubclassIntoFilter($filter) {
    return($this->table->injectSubclassIntoFilter($filter));
  } // injectSubclassIntoFilter

  protected function injectSubclassIntoStatement() {
    $this->table->injectSubclassIntoStatement($this);
  } // injectSubclassIntoStatement

  protected function prefixTableAlias($field) {
    return($field);
  } // prefixTableAlias

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

  static protected function flattenOrderBy($orderBy) {
    if (is_array($orderBy)) {
      return(implode(',', $orderBy));
    } else {
      return($orderBy);
    } // else
  } // flattenOrderBy

} // CecSqlStatement
?>
