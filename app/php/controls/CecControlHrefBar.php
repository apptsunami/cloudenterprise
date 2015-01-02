<?PHP
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
/* CecControlHrefBar.php */
$rDir = '';
require_once($rDir.'cec/php/views/CecUIObject.php');

class CecControlHrefBar extends CecUIObject {

  const CLASS_CATEGORY_TABLE = 'hrefBar';
  const CLASS_CATEGORY = 'nonSelectedHrefBar';
  const CLASS_CATEGORY_SELECTED = 'selectedHrefBar';

  protected $hrefList;
  protected $selectedIndex;
  public $tableHtmlClass;
  public $selectedHtmlClass;
  public $nonSelectedHtmlClass;

  public function __construct($parentUIObject) {
    parent::__construct($parentUIObject);
    $this->hrefList = Array();
    $this->selectedIndex = -1;
    $this->tableHtmlClass = self::CLASS_CATEGORY_TABLE;
    $this->selectedHtmlClass = self::CLASS_CATEGORY_SELECTED;
    $this->nonSelectedHtmlClass = self::CLASS_CATEGORY;
  } // __construct

  public function appendHref($href, $selected) {
    $this->hrefList[] = $href;
    if ($selected) {
      $this->selectedIndex = count($this->hrefList) -1;
    } // if
  } // appendHref

  public function render() {
    $tdWidth = 100/count($this->hrefList);
    $index = 0;
    $str = StyleHelper::tableHeader('100%', $this->tableHtmlClass).'<tr>';
    foreach($this->hrefList as $href) {
      $tdClass = ($index==$this->selectedIndex?$this->selectedHtmlClass:$this->nonSelectedHtmlClass);
      $str .= '<td vAlign="top" align="center" width="'.$tdWidth.'%" class="'
        .$tdClass.'">'.$href.'</td>';
      $index++;
    } // foreach
    $str .= '</tr></table>';
    return($str);
  } // render

} // CecControlHrefBar
?>
