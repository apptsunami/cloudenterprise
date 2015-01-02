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
/* CecFrameMultiCanvas2.php */
$rDir = '';
require_once($rDir.'cec/php/views/CecFrameMultiCanvas.php');
require_once($rDir.'cec/php/CecDebugUtil.php');

class CecFrameMultiCanvas2 extends CecFrameMultiCanvas {

  protected $canvasList2;

  /* methods */
  protected function getCanvasList2() {
    /* should be defined in subclass */
    return(null);
  } // getCanvasList2

  private function constructPanelUrl($canvas, $hrefClass) {
    $uParam = new CecUrlParam();
    $uParam->appendAllCurrentParameters(0, 0);
    $uParam->removeKey(CecConfig::PARAM_CMDLEVEL4);
    $uParam->removeKey(CecConfig::PARAM_CMDLEVEL3);
    $uParam->removeKey(CecConfig::PARAM_CMDLEVEL2);
    $uParam->appendKeyValuePair($this->canvasParamName, $canvas->getTag());

    $ds = $this->getDataSource();
    $icon = $ds->renderImg($canvas->getIcon(), $canvas->getLabel());
    $label =  $icon.$canvas->getLabel();
    $str = $ds->renderHrefInString($ds->getCurrentPageName(), $uParam->toString(),
       $label, $hrefClass);
    return($str);
  } // constructPanelUrl

  protected function renderPanelBar() {
    $str = '<div class="tabs clearfix"><center><div class="left_tabs">'
      .'<ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
    $canvasCount = count($this->canvasList2);
    $index=0;
    foreach($this->canvasList2 as $canvas) {
      $label = $canvas->getLabel();
      if (is_null($label) || ($label == '')) continue;
      $str .= '<li';
      /* no class name for entries in the middle */
      if($index==0) {
        $str .= ' class="first"';
      } else if ($index == $canvasCount-1) {
        $str .= ' class="last"';
      } // else
      $str .= '>';

      $hrefClass = null;
      if ($canvas->getTag()==$this->currentCanvasTag) {
        $hrefClass = 'selected';
      } // if
      $str .= $this->constructPanelUrl($canvas, $hrefClass);
      $str .= '</li>';
    } // foreach
    $str .= '</ul></div></center></div>';
    $index++;
    return($str);
  } // renderPanelBar

  protected function renderTopDashBoard() {
    $str = parent::renderTopDashBoard();
    $this->canvasList2 = $this->getCanvasList2();
    if (!is_null($this->canvasList2)) {
      $str .= $this->renderPanelBar();
    } // if
    return($str);
  } // renderTopDashBoard

  protected function getCurrentCanvas() {
    $c = parent::getCurrentCanvas();
    if (!is_null($c)) return($c);
    if (!isset($this->canvasList2)) {
      $this->canvasList2 = $this->getCanvasList2();
    } // if
    return($this->lookUpCurrentCanvas($this->canvasList2));
  } // getCurrentCanvas

} // CecFrameMultiCanvas2
?>
