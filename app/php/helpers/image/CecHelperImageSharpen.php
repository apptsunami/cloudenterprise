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
/* CecHelperImageSharpen.php */
$rDir = '';

class CecHelperImageSharpen {

  public $sharpenEffect;

  public function __construct() {
  /* The value of 0.75 is like 75% of sharpening effect
     Change if you need it to 0.01 to 1.00 or so
     Zero would be NO effect
     1.00 would be somewhat grainy */
    $this->sharpenEffect = 0.75;
  } // __construct

  private function sharpen($img) {
    $height = imagesy($img);
    $width = imagesx($img);
    $pix=array();

    //get all color values off the image
    for($hc=0; $hc<$height; ++$hc){
        for($wc=0; $wc<$width; ++$wc){
            $rgb = ImageColorAt($img, $wc, $hc);
            $pix[$hc][$wc][0]= $rgb >> 16;
            $pix[$hc][$wc][1]= $rgb >> 8 & 255;
            $pix[$hc][$wc][2]= $rgb & 255;
        } // for
    } // for

    //sharpen with upper and left pixels
    $height--; $width--;
    for($hc=1; $hc<$height; ++$hc){  
        $r5=$pix[$hc][0][0];
        $g5=$pix[$hc][0][1];
        $b5=$pix[$hc][0][2];  
        $hcc=$hc-1;
        for($wc=1; $wc<$width; ++$wc){
            $r=-($pix[$hcc][$wc][0]);
            $g=-($pix[$hcc][$wc][1]);
            $b=-($pix[$hcc][$wc][2]);  

            $r-=$r5+$r5; $g-=$g5+$g5; $b-=$b5+$b5;
  
            $r5=$pix[$hc][$wc][0];
            $g5=$pix[$hc][$wc][1];
            $b5=$pix[$hc][$wc][2];
  
            $r+=$r5*5; $g+=$g5*5; $b+=$b5*5;  

            $r*=.5; $g*=.5; $b*=.5;
  

            $r=(($r-$r5)*$this->sharpenEffect)+$r5;
            $g=(($g-$g5)*$this->sharpenEffect)+$g5;
            $b=(($b-$b5)*$this->sharpenEffect)+$b5;  

            if ($r<0) $r=0; elseif ($r>255) $r=255;
            if ($g<0) $g=0; elseif ($g>255) $g=255;
            if ($b<0) $b=0; elseif ($b>255) $b=255;
            imageSetPixel($img,$wc,$hc,($r << 16)|($g << 8)|$b);
        } // for
    } // for

    /* save pic */
    imageInterlace($img,1);
  } // sharpen

  public function execute($image) {
    return($this->sharpen($image));
  } // execute

} // CecHelperImageSharpen
?>
