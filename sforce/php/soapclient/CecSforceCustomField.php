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
/* CecSforceCustomField.php */
require_once("sforce/soapclient/SforceMetaObject.php");

/**
 *  Supports Sforce CustomField
 */

class CecSforceCustomField extends SforceCustomField {

  const FIELD_TYPE_AUTO_NUMBER = 'AutoNumber';
  const FIELD_TYPE_LOOKUP = 'Lookup';
  const FIELD_TYPE_MASTER_DETAIL = 'MasterDetail';
  const FIELD_TYPE_CHECKBOX = 'Checkbox';
  const FIELD_TYPE_CURRENCY = 'Currency';
  const FIELD_TYPE_DATE = 'Date';
  const FIELD_TYPE_DATE_TIME = 'DateTime';
  const FIELD_TYPE_EMAIL = 'Email';
  const FIELD_TYPE_NUMBER = 'Number';
  const FIELD_TYPE_PERCENT = 'Percent';
  const FIELD_TYPE_PHONE = 'Phone';
  const FIELD_TYPE_PICKLIST = 'Picklist';
  const FIELD_TYPE_MULTISELECT_PICKLIST = 'MultiselectPicklist';
  const FIELD_TYPE_TEXT = 'Text';
  const FIELD_TYPE_TEXT_AREA = 'TextArea';
  const FIELD_TYPE_LONG_TEXT_AREA = 'LongTextArea';
  const FIELD_TYPE_URL = 'Url';
  const FIELD_TYPE_ENCRYPTED_TEXT = 'EncryptedText';
  const FIELD_TYPE_SUMMARY = 'Summary';
  const FIELD_TYPE_HIERARCHY = 'Hierarchy';
  const FIELD_TYPE_CUSTOM_DATA_TYPE = 'CustomDataType';


  const DEFAULT_SCALE = 0;
  const DEFAULT_PRECISION = 10;
  const MAX_PRECISION = 18;
  const MAX_STRING_LENGTH = 255;

  static public function mapMysqlToSforceDataType($custField, $dataType,
      $maxStringLength, $precision, $scale) {
    switch($dataType) {
    case 'bigint':
    case 'tinyint':
    case 'int':
      $custField->setType(CecSforceCustomField::FIELD_TYPE_NUMBER);
      if (is_null($precision)) {
        $custField->setPrecision(self::DEFAULT_PRECISION);
      } else if ($precision <= self::MAX_PRECISION) {
        $custField->setPrecision($precision);
      } else {
        $custField->setPrecision(self::MAX_PRECISION);
      } // else
      if (is_null($scale)) {
        $custField->setScale(self::DEFAULT_SCALE);
      } else {
        $custField->setScale($scale);
      } // else
      break;

    case 'varchar':
    case 'text':
      $custField->setType(CecSforceCustomField::FIELD_TYPE_TEXT);
      if (!is_null($maxStringLength)) {
        if ($maxStringLength <= self::MAX_STRING_LENGTH) {
          $custField->setLength($maxStringLength);
        } else {
          $custField->setLength(self::MAX_STRING_LENGTH);
        } // else
      } // if
      break;

    case 'date':
      $custField->setType(CecSforceCustomField::FIELD_TYPE_DATE);
      break;

    case 'datetime':
      $custField->setType(CecSforceCustomField::FIELD_TYPE_DATE_TIME);
      break;

    default:
      error_log('mapMysqlToSforceDataType: unsupported data type '.$dataType, 3);
      return(false);
    } // switch
    return(true);
  } // mapMysqlToSforceDataType

} // CecSforceCustomField
?>
