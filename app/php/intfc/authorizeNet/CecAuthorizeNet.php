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
/* CecAuthorizeNet.php */
$rDir = '';
require_once('anet_php_sdk/AuthorizeNet.php');
require_once($rDir.'cec/php/utils/CecUtil.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');

class CecAuthorizeNet {

  const API_VERSION = '3.1';

  protected $loginId;
  protected $transactionKey;
  protected $md5Setting;
  protected $productionMode;

  public function __construct($loginId, $transactionKey, $md5Setting,
      $productionMode) {
    $this->loginId = $loginId;
    $this->transactionKey = $transactionKey;
    $this->md5Setting = $md5Setting;
    $this->productionMode = $productionMode;
  } // __construct

  protected static function formatPrice($price) {
    if (is_null($price)) return null;
    /* do not use thousand separator */
    $price = CecUtil::strtodouble($price);
    return @number_format($price, 2, '.', '');
  } // formatPrice

} // CecAuthorizeNet
