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
/* CecLocalization.php */
$rDir = '';

/**
 * It contains localized strings used by Cloud Enterprise Computing Framework.
 * The application should not directly reference this class.
 */
class CecLocalization {

  const MSG_HELP = '1';
  const MSG_INSTALL = '2';
  const MSG_JOIN_APP = '3';
  const MSG_PLEASE_WAIT = 4;
  const MSG_YOU_MUST_INSTALL_FIRST = 5;
  const MSG_PLEASE_INSTALL_FIRST = 6;
  const MSG_POWERED_BY_TFW  = 7;

  protected $us_en;

  public function __construct() {
    $this->us_en = Array(
      self::MSG_HELP => 'help',
      self::MSG_INSTALL => 'install',
      self::MSG_JOIN_APP => 'Join %s.',
      self::MSG_PLEASE_WAIT => 'Please wait...',
      self::MSG_YOU_MUST_INSTALL_FIRST => 'You must install this application first.',
      self::MSG_PLEASE_INSTALL_FIRST => 'Please install this application.',
      self::MSG_POWERED_BY_TFW => '<hr>Powered by the Cloud Enterprise Computing Framework</a>',
    );
  } // __construct

  public function getCurrentLocale() {
    return($this->us_en);
  } // getCurrentLocale

} // CecLocalization
?>
