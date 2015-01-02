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
/* CecHelperImageInterlace.php */
$rDir = '';

class CecHelperImageInterlace {

  /* creates a JPG which is build up on the screen */
  const PROGRESSIVE = 0;
  /* fullscreen at low-res, then overlaying higher-res as downloaded */
  const INTERLACED = 1;

  public $interlace;

  public function __construct() {
    $this->interlace = self::INTERLACED;
  } // __construct

  private function interlace($image) {
    ImageInterlace($image, $this->interlace);
    return(null);
  } // interlace

  public function execute($image) {
    return($this->interlace($image));
  } // execute

} // CecHelperImageInterlace
?>
