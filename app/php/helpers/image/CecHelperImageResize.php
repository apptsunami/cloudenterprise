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
/* CecHelperImageResize.php */
$rDir = '';
require_once($rDir.'cec/php/CecDebugUtil.php');

class CecHelperImageResize {

  public $newWidth;
  public $newHeight;
  public $preserveAspectRatio;
  public $resampleFlag;

  public function __construct() {
    $this->preserveAspectRatio = TRUE;
    $this->resampleFlag = TRUE;
  } // __construct

  private function resize($image) {
    if (is_null($image)) return;
    if (is_null($this->newWidth) && is_null($this->newHeight)) return;

    /* newWidth and newHeight are temporary for this run */
    $newWidth = $this->newWidth;
    $newHeight = $this->newHeight;

    $originalW = imagesx($image);
    $originalH = imagesy($image);

    if (!is_null($newWidth)) {
      if (is_null($newHeight)) {
        $newHeight = $originalH * ($newWidth / $originalW);
      } else {
        /* both non-null */
        if ($this->preserveAspectRatio) {
          /* do not exceed either newWidth or newHeight */
          $wRatio = $newWidth / $originalW;
          $hRatio = $newHeight / $originalH;
          /* use the smaller of the two ratio */
          if ($wRatio < $hRatio) {
            $newHeight = $originalH * $wRatio;
          } else {
            $newWidth = $originalW * $hRatio;
          } // else
        } // if
      } // if
    } else {
      if (is_null($newWidth)) {
        $newWidth = $originalW * ($newHeight / $originalH);
      } // if
    } // else

    /* create a new true color image */
    $newImage = imageCreateTrueColor($newWidth, $newHeight);
    /* resize and resample color */
    if ($this->resampleFlag) {
      $status = @imageCopyResampled($newImage, $image, 
        0, 0, 0, 0, $newWidth, $newHeight, $originalW, $originalH);
    } else {
      $status = @imageCopyResized($newImage, $image, 
        0, 0, 0, 0, $newWidth, $newHeight, $originalW, $originalH);
    } // else
    if ($status === FALSE) {
      return(null);
    } // if
    return($newImage);
  } // resize

  public function execute($image) {
    if (is_null($image)) return;
    return($this->resize($image));
  } // execute

} // CecHelperImageResize
?>
