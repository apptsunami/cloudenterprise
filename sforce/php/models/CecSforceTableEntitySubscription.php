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
/* CecSforceTableEntitySubscription.php */
require_once('cec/php/models/CecSforceTable.php');
require_once('cec/php/models/CecSforceTableName.php');
require_once('cec/php/models/CecSforceTableUser.php');

/**
 *  Supports Sforce table 'EntitySubscription'.
 */
class CecSforceTableEntitySubscription extends CecSforceTable {

  const TABLE_NAME = 'EntitySubscription';

  const FIELD_PARENT_ID = "ParentId";
  const FIELD_SUBSCRIBER_ID = "SubscriberId";

  const RELATION_SUBSCRIBER = 'subscriber';

  const NON_ADMIN_USER_SELECT_LIMIT = 500;

  public function __construct($partnerClient, $tableName=self::TABLE_NAME,
      $fieldNameList=null) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    parent::__construct($partnerClient, $tableName, $fieldNameList);
  } // __construct

  public function getDefaultFieldNameList() {
    $fieldNameList = Array();
    $fieldNameList[] = self::FIELD_PARENT_ID;
    $fieldNameList[] = self::FIELD_SUBSCRIBER_ID;
    return($fieldNameList);
  } // getDefaultFieldNameList

  public function selectFollowUserList($parentId, $fieldNameList=null) {
    if (empty($parentId)) return(null);
    if (is_array($parentId)) {
      $filter = self::generateInClause(self::FIELD_PARENT_ID, $parentId, true);
    } else {
      $filter = self::FIELD_PARENT_ID.'='.self::quoteSoqlString($parentId);
    } // else
    if (is_null($fieldNameList)) {
      $tableUser = new CecSforceTableUser($this->partnerClient);
      $fieldNameList = $tableUser->getDefaultFieldNameList();
    } // if
    /* returns parentID plus user fields */
    $userFieldNameList = Array();
    $userFieldNameList[] = self::FIELD_PARENT_ID;
    foreach($fieldNameList as $fieldName) {
      $userFieldNameList[] = self::RELATION_SUBSCRIBER.'.'.$fieldName;
    } // foreach
    $dataRowArray = $this->selectSomeAsArray($filter, $userFieldNameList);
    $flatDataRowArray = Array();
    foreach($dataRowArray as $dataRow) {
      if (isset($dataRow[0])) {
        $subObject = $this->convertStdObjectToArray($dataRow[0]);
        unset($dataRow[0]);
        $flatDataRowArray[] = array_merge($dataRow, $subObject);
      } else {
        $flatDataRowArray[] = $dataRow;
      } // else
    } // foreach
    return($flatDataRowArray);
  } // selectFollowUserList

  public function selectParentIdByType($objectType, $limit=self::NON_ADMIN_USER_SELECT_LIMIT) {
    $oldLimitCount = $this->limitCount;
    $this->limitCount = $limit;
    /* parent is a Name object */
    $filter = self::parentFieldName(CecSforceTableName::FIELD_TYPE).'='
      .self::quoteSoqlString($objectType);
    $fieldNameList = Array(self::FIELD_PARENT_ID);
    $dataArray = $this->selectSomeAsArray($filter, $fieldNameList, self::FIELD_PARENT_ID);
    $this->limitCount = $oldLimitCount;
    return($dataArray);
  } // selectParentIdByType

} // CecSforceTableEntitySubscription
?>
