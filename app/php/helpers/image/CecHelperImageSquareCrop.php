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
/* CecHelperImageSquareCrop.php */
$rDir = '';
require_once($rDir.'cec/php/CecDebugUtil.php');

class CecHelperImageSquareCrop {

  public function __construct() {
  } // __construct

  private function squareCrop($image) {

    $originalW = imagesx($image);
    $originalH = imagesy($image);

    if ($originalW == $originalH) {
        /* do nothing if the image is already square */
        return($image);
    } // if

    /* create a new square image and copy from the center of the original */
    if ($originalW < $originalH) {
        $newWidth = $originalW;
        $newHeight = $originalW;
        $newX = 0;
        $newY = ($originalH - $originalW) / 2;
    } else {
        $newWidth = $originalH;
        $newHeight = $originalH;
        $newX = ($originalW - $originalH) / 2;
        $newY = 0;
    } // else

    $newImage = imageCreateTrueColor($newWidth, $newHeight);
    $status = imagecopy($newImage, $image, 0, 0, $newX, $newY, $newWidth, $newHeight);

    if ($status === FALSE) {
      return(null);
    } // if
    return($newImage);
  } // squareCrop

  public function execute($image) {
    return($this->squareCrop($image));
  } // execute

} // CecHelperImageSquareCrop
?>
