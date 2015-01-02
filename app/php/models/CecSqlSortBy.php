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

class CecSqlSortBy {

  const ASC = 'ASC';
  const DESC = 'DESC';

  protected $sortFieldArray;

  public function __construct() {
    parent::__construct();
    $this->sortFieldArray = Array();
  } // __construct

  /* order is either self::ASC or self::DESC */
  public function addSortField($fieldName, $order) {
    $this->sortFieldArray[] = CecDataTable::escape($fieldName).' '.$order;
  } // addSortField

  public function toString() {
    if (count($this->sortFieldArray)==0) {
      return(null);
    } // if
    return(implode(',', $this->sortFieldArray));
  } // toString

} // CecSqlSortBy
?>
