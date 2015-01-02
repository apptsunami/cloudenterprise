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
/* CecControlMinuteSelect.php */
$rDir = '';
require_once('cec/php/controls/CecControlSelect.php');

class CecControlMinuteSelect extends CecControlSelect {

    public $stepSize;

    public function __construct($controlAttributeArray=null) {
      parent::__construct(null, null, null, $controlAttributeArray);
      $this->stepSize = 1;
    } // __construct

    public function setStepSize($stepSize) {
      $this->stepSize = $stepSize;
    } // setStepSize

    protected function renderChoiceList($selectedValue) {
      $str = null;
      $minute=0;
      while ($minute < 60) {
        $str .= '<OPTION value="'.$minute.'"';
        if (!is_null($selectedValue)) {
          if ($minute == $selectedValue) {
            $str .= ' selected ';
          } // if
        } // if
        $str .= '>'.$minute.'</option>';
        $minute += $this->stepSize;
      } // while
      return($str);
    } // renderChoiceList

} // CecControlMinuteSelect
?>
