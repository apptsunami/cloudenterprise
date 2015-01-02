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
/* CecContext.php */
$rDir = '';
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/CecLocalization.php');
require_once($rDir.'cec/php/models/CecDataSource.php');
require_once($rDir.'cec/php/models/CecDbConnect.php');
require_once($rDir.'cec/php/models/CecSiteWrapper.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'php/ApplicationContext.php');

/**
 * It contains a runtime object used by Cloud Enterprise Computing Framework.
 * The application should not directly reference this class.
 */
class CecContext {

  /* variables */
  private $dbConnect;
  private $dataSource;
  private $userId;
  private $userTimeZone;
  private $propertyList;
  private $applicationContext;
  private $locale;

  /* methods */
  public function __construct($openDataSource=true,
      $debug=CecConfig::DEBUG_LEVEL,
      $databaseName=CecApplicationConfig::MY_DATABASE_NAME,
      $host=CecApplicationConfig::MY_DATABASE_PATH,
      $login=CecApplicationConfig::MY_DATABASE_USER,
      $password=CecApplicationConfig::MY_DATABASE_PASSWORD) {

    $this->dbConnect = new CecDbConnect($debug);
    $status = $this->dbConnect->connect($databaseName, $host, $login, $password);
    if ($openDataSource) {
      $dataSource = new CecDataSource();
      $this->dataSource = $dataSource;
      /* cache frequently needed data */
      $this->userId = $dataSource->getUserId();
      $this->userTimeZone = $dataSource->getUserTimeZone();
    } else {
      $this->dataSource = null;
      $this->userId = null;
      $this->userTimeZone = null;
    } // else
    $propertyList = Array();
    $this->applicationContext = new ApplicationContext($this);
    $loc = new CecLocalization();
    $this->locale = $loc->getCurrentLocale();
  } // __construct

  public function close() {
    $this->dbConnect->close();
  }

  /* getters */
  public function getApplicationContext() {
    return($this->applicationContext);
  } // getApplicationContext

  public function getDbConnect() {
    return($this->dbConnect);
  } // getDbConnect

  public function getDataSource() {
    return($this->dataSource);
  } // getDataSource

  public function getUserId() {
    return($this->userId);
  } // getUserId

  public function getTimeZone() {
    return($this->userTimeZone);
  } // getTimeZone

  public function getCurrentLocale() {
    return($this->locale);
  } // getLocale

  public function getCurrentUrl() {
    $ds = $this->getDataSource();
    $page = $ds->getCurrentPageName();
    $urlParam = new CecUrlParam();
    $urlParam->appendAllCurrentParameters(0, 1);
    $str = $ds->renderHrefUrl($page, $urlParam->toString());
    return($str);
  } // getCurrentUrl

  /* setters */
  public function setApplicationContext($ctx) {
    $this->applicationContext = $ctx;
  } // setApplicationContext

} // CecContext
?>
