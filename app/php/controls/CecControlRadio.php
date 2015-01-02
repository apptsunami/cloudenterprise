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
/* CecControlRadio.php */
$rDir = '';

class CecControlRadio {

  protected $choiceList;
  protected $valueFieldName;
  protected $textFieldName;

  public function __construct($choiceList, $valueFieldName=null, $textFieldName=null) {
    $this->choiceList = $choiceList;
    $this->valueFieldName = $valueFieldName;
    $this->textFieldName = $textFieldName;
  } // __construct

  public function render($controlName, $selectedValue=null, $separator='<br/>') {
    $str = null;
    if (is_null($this->valueFieldName)) {
      foreach($this->choiceList as $value=>$txt) {
        $str .= $this->renderOneEntry($controlName, $value, $txt, $selectedValue, $separator);
      } // foreach
    } else {
      foreach($this->choiceList as $choice) {
        $str .= $this->renderOneEntry($controlName, $choice[$this->valueFieldName],
          $choice[$this->textFieldName], $selectedValue, $separator);
      } // foreach
    } // else
    return($str);
  } // render

  private function renderOneEntry($controlName, $value, $txt, $selectedValue, $separator) {
      $str = '<input type="radio" value="'.$value.'" name="'.$controlName.'" ';
      if (!is_null($selectedValue)) {
        if ($selectedValue == $value) {
          $str .= ' checked ';
        } // if
      } // if
      $str .= '>'.htmlentities($txt).$separator;
      return($str);
  } // renderOneEntry

} // CecControlRadio
?>
