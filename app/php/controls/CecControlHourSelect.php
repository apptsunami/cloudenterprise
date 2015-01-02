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
/* CecControlHourSelect.php */
$rDir = '';
require_once('cec/php/controls/CecControlSelect.php');

class CecControlHourSelect extends CecControlSelect {

    const NOTATION_MILITARY = 1;
    const NOTATION_AM_PM = 2;

    protected $displayNotation;

    public function __construct($controlAttributeArray=null,
        $displayNotation=self::NOTATION_MILITARY) {
      parent::__construct(null, null, null, $controlAttributeArray);
      $this->displayNotation = $displayNotation;
    } // __construct

    protected function renderChoiceList($selectedValue) {
      $str = null;
      $hour=0;
      while ($hour < 24) {
        $str .= '<OPTION value="'.$hour.'"';
        if (!is_null($selectedValue)) {
          if ($hour == $selectedValue) {
            $str .= ' selected ';
          } // if
        } // if
        $str .= '>';
        switch($this->displayNotation) {
          case self::NOTATION_MILITARY:
            $str .= sprintf('%02d', $hour);
          break;
          case self::NOTATION_AM_PM:
            if ($hour == 0) {
              $str .= '12 Midnight';
            } else if ($hour <12) {
              $str .= ($hour).' AM';
            } else if ($hour == 12){
              $str .= '12 Noon';
            } else {
              $str .= ($hour-12).' PM';
            }  // else
          break;
          default:
            $str .= $hour;
        } // switch

        $str .= '</option>';
        $hour++;
      } // while

      return($str);
    } // renderChoiceList

} // CecControlHourSelect
?>
