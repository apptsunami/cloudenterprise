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
/* CecGoogleMap.php */
$rDir = '';
require_once($rDir.'cec/php/views/CecUrlParam.php');

class CecGoogleMap {
  const PARAM_F = 'f';
  const PARAM_HUMAN_LANGUAGE = 'hl';
  const PARAM_STARTING_ADDRESS = 'saddr';
  const PARAM_DESTINATION_ADDRESS = 'daddr';
  const PARAM_ENCODING = 'ie';
  const PARAM_OM = 'om';
  const GOOGLE_MAP_ROOT_URL = 'http://maps.google.com/maps';

  const LANG_ENGLISH = 'en';

  static function renderGoogleDrivingDirectionUrl($startAddr, $endAddr, 
      $lang=self::LANG_ENGLISH) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair(self::PARAM_F,'d');
    $uParam->appendKeyValuePair(self::PARAM_HUMAN_LANGUAGE, $lang);
    $uParam->appendKeyValuePair(self::PARAM_STARTING_ADDRESS, $startAddr);
    $uParam->appendKeyValuePair(self::PARAM_DESTINATION_ADDRESS, $endAddr);
    $uParam->appendKeyValuePair(self::PARAM_ENCODING, 'UTF8');
    $uParam->appendKeyValuePair(self::PARAM_OM, '1');
    $str = self::GOOGLE_MAP_ROOT_URL.'?'.$uParam->toString();
    return($str);
  } // renderGoogleDrivingDirectionUrl

} // CecGoogleMap
?>
