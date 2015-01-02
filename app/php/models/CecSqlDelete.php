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

class CecSqlDelete extends CecSqlStatement {

  protected $where;

  public function __construct($table) {
    parent::__construct($table);
    $this->where = null;
  } // __construct

  public function setWhere($where) {
    if (!empty($where)) {
      $this->where = $this->injectSubclassIntoFilter($where);
    } // if
  } // setWhere

  public function setWhereObjidClause($objid) {
    $autoIncrementFieldName = $this->prefixTableAlias(
      $this->table->getAutoIncrementFieldName());
    if (is_array($objid)) {
      $this->where = $autoIncrementFieldName.' IN ('
        .implode(',',$objid).')';
    } else {
      $this->where = $autoIncrementFieldName.'='.$objid;
    } // else
  } // setWhereObjidClause

  public function toString($doIgnore=FALSE) {
    $str = ' DELETE ';
    if ($doIgnore) {
      $str .= 'IGNORE ';
    } // if
    $str .= ' FROM '.$this->tableName;
    $filter = $this->injectSubclassIntoFilter($this->where);
    if (!is_null($filter)) {
      $str .= ' WHERE '.$filter;
    } // if
    return($str.';');
  } // toString

} // CecSqlDelete
?>
