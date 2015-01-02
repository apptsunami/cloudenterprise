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
/* views/CecCanvasMultiPanel.php */
$rDir = '';
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/views/CecUrlParam.php');
require_once($rDir.'cec/php/views/CecCanvas.php');

class CecCanvasMultiPanel extends CecCanvas {

  protected $panelList;
  protected $currentPanelTag;
  protected $currentPanel;

  public function __construct($parentUIObject) {
    parent::__construct($parentUIObject);
    $this->panelList = null;
    $this->currentPanelTag = null;
  } // __construct

  /* getters */
  /* to be defined in child */
  protected function getPanelList() {
    return(null);
  } // getPanelList

  /* to be defined in child */
  protected function getDefaultPanelTag() {
    return(null);
  } // getDefaultPanelTag

  /* methods */
  /* to be defined in child */
  protected function renderTop() {
    return(null);
  } // renderTop

  private function setUpCurrentPanelTag() {
    if (!is_null($this->panelList)) {
      $cmd2 = $this->getRequestParameter(CecConfig::PARAM_CMDLEVEL2);
      if (isset($cmd2)) {
        $this->currentPanelTag = $cmd2;
      } else {
        $this->currentPanelTag = $this->getDefaultPanelTag();
      } // if
    } else {
      $this->currentPanelTag = null;
    } // if
  } // setUpCurrentPanelTag

  private function constructPanelUrl($panel, $hrefClass) {
    $uParam = new CecUrlParam();
    $uParam->appendAllCurrentParameters(0, 0);
    $uParam->appendKeyValuePair(CecConfig::PARAM_CMDLEVEL2, $panel->getTag());

    $ds = $this->getDataSource();
    $icon = $ds->renderImg($panel->getIcon(), $panel->getLabel());
    $str = $ds->renderHrefInString($ds->getCurrentPageName(), $uParam->toString(),
       $icon.$panel->getLabel(), $hrefClass);
    return($str);
  } // constructPanelUrl

  private function wrapButtonList($list) {
    return('<div class="panelButton">'.$list.'</div>');
/*
    return('<div class="tabs clearfix"><center><div class="left_tabs">'
      .'<ul class="toggle_tabs clearfix" id="toggle_tabs_unused">'.$list
      .'</ul></div></center></div>');
*/
  } // wrapButtonList

  private function wrapButton($button, $panelCount, $index, $isSelected=false) {
    return('<span class="panelButton">'.$button.'</span>');

/*
    $str = '<li';
    if($index==0) {
      $str .= ' class="first"';
    } else if ($index == $panelCount-1) {
      $str .= ' class="last"';
    } // else
    $str .= '>'.$button.'</li>';
    return($str);
*/
  } // wrapButton

  protected function renderPanelBar() {
    $str = null;
    $panelCount = count($this->panelList);
    $index=0;
    foreach($this->panelList as $panel) {
      if ($panel->getTag()==$this->currentPanelTag) {
        $isSelected = true;
        $hrefClass = "selectedPanelButton";
        $this->currentPanel = $panel;
      } else {
        $isSelected = false;
        $hrefClass = "panelButton";
      } // if
      $button = $this->constructPanelUrl($panel, $hrefClass);
      $str .= $this->wrapButton($button, $panelCount, $index, $isSelected);
      $index++;
    } // foreach
    $str = $this->wrapButtonList($str);
    return($str);
  } // renderPanelBar

  public function render() {
    $this->panelList = $this->getPanelList();
    $this->setUpCurrentPanelTag();
    $str = $this->renderTop();
    $str .= $this->renderPanelBar();
    if (isset($this->currentPanel)) {
      $str .= $this->currentPanel->render();
    } // if
    return($str);
  } // render

} // CecCanvasMultiPanel
?>
