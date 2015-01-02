<?php
/* views/CecFrameMultiCanvas.php */
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
$rDir = '';
require_once($rDir.'php/cec/CecApplicationConfig.php');
require_once($rDir.'cec/php/CecConfig.php');
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/CecGlobals.php');
require_once($rDir.'cec/php/CecLocalization.php');
require_once($rDir.'cec/php/helpers/CecHelperHtmlSite.php');
require_once($rDir.'cec/php/views/CecCanvasHelp.php');
require_once($rDir.'cec/php/views/CecUIObject.php');
require_once($rDir.'cec/php/views/CecFrameSingleCanvas.php');

class CecFrameMultiCanvas extends CecFrameSingleCanvas {

  const CLASS_EMPHASIZELINK = 'emphasizeLink';

  protected $currentCanvasTag;
  protected $canvasList;
  protected $canvasHelp;

  /* constructors */
  public function __construct() {
    parent::__construct();
    $this->canvasList = null;
    $loc = new CecLocalization();
    $this->currentCanvasTag = null;
  } // __construct

  /* methods */
  protected function getCanvasList() {
    /* should be defined in subclass */
    return(Array());
  } // getCanvasList

  public function getResponseDivId() {
    /* there is none at the frame level */
    return(null);
  } // getResponseDivId

  protected function analyzeCanvasTag() {
    $canvasTag = $this->getRequestParameter($this->canvasParamName);
    if (!is_null($canvasTag)) {
      $this->currentCanvasTag = $canvasTag;
    } else {
      $this->currentCanvasTag = $this->getDefaultCanvas();
    } // if
  } // analyzeCanvasTag

  private function renderIcon($icon, $title) {
      return($this->context->getDataSource()->renderImg($icon, $title));
  } // renderIcon

  protected function renderTopNavigationBar() {
    $ds = $this->getDataSource();
    $subCanvas = $this->getCurrentCanvas();
    $str = null;
    $canvasCount = 0;
    foreach($this->canvasList as $canvas) {
      $label = $canvas->getLabel();
      if (is_null($label) || ($label == '')) continue;
      $selected = ($canvas->getTag() == $this->currentCanvasTag);
      if (($this->tabStyle == self::TAB_STYLE_NATIVE) && ($canvasCount > 0)) {
        /* put a vertical bar to separate from the previous one */
        $str .= CecHelperHtmlSite::pipe();
      } // if
      $str .= $this->constructNavigationUrl($canvas, $selected);
      $canvasCount++;
    } // foreach

    return($str);

    switch($this->tabStyle) {
    case self::TAB_STYLE_NATIVE:
      return($ds->renderTabs($str));
    case self::TAB_STYLE_CLASSIC:
    default:
      return('<div class="dh_actions">'.$str.'</div>');
    } // switch

  } // renderTopNavigationBar

  protected function getCurrentCanvas() {
    if (is_null($this->canvasList)) return(null);
    if (!isset($this->currentCanvasTag)) {
      $this->analyzeCanvasTag();
    } // if
    $canvas = $this->lookUpCurrentCanvas($this->canvasList,
      $this->currentCanvasTag);
    $nextCanvasTag = $canvas->forwardToAnotherCanvas();
    while (!is_null($nextCanvasTag)) {
      $this->currentCanvasTag = $nextCanvasTag;
      $canvas = $this->lookUpCurrentCanvas($this->canvasList,
        $nextCanvasTag);
      $nextCanvasTag = $canvas->forwardToAnotherCanvas();
    } // while
    return($canvas);
  } // getCurrentCanvas

  protected function lookUpCurrentCanvas($canvasList, $canvasTag) {
    if ($this->currentCanvasTag == CecCanvasHelp::TAG) {
      if (!isset($this->helpCanvas)) {
        $this->helpCanvas = new CecCanvasHelp($this);
      } // if
      return($this->helpCanvas);
    } // if
    foreach($canvasList as $canvas) {
      if ($canvas->getTag() == $canvasTag) {
        $v = $canvas;
        if (!is_null($v)) {
          $v->setContext($this->context);
        } // if
        return($v);
      } // if
    } //foreach
    return(null);
  } // lookUpCurrentCanvas

  protected function getMustInstallCanvasTagList() {
    return(null);
  } // getMustInstallCanvasTagList

  protected function mustInstallBeforeUse($subCanvas) {
    /* can be defined in child class */
    /* no subcanvas */
    if (is_null($subCanvas)) return(FALSE);
    $tagList = $this->getMustInstallCanvasTagList();
    /* no must install canvas */
    if (is_null($tagList)) return(FALSE);
    if (array_search($subCanvas->getTag(), $tagList) === FALSE) {
      /* tag is not a must install canvas */
      return(FALSE);
    } // if
    return(TRUE);
  } // mustInstallBeforeUse

  private function renderCurrentCanvas() {
    $subCanvas = $this->getCurrentCanvas();
    $ds = $this->getDataSource();
    if ($ds->isAppAdded()==0) {
      if ($this->mustInstallBeforeUse($subCanvas)) {
        $msg1 = $this->lookUpLocale(CecLocalization::MSG_PLEASE_INSTALL_FIRST);
        $msg2 = $this->lookUpLocale(CecLocalization::MSG_YOU_MUST_INSTALL_FIRST);
        $buttonStr = $ds->renderHrefInString($ds->getAddApplicationURLBase(), null,
          ucwords($this->lookUpLocale(CecLocalization::MSG_INSTALL)),
          self::CLASS_EMPHASIZELINK, null, null);
        $str = CecHelperHtmlSite::messageInYellowishBox($msg1, $msg2.'<br>'.$buttonStr, '100%');
        return($str);
      } // if
    } // if

    if (!is_null($subCanvas)) {
      return($subCanvas->render());
    } else {
      return(null);
    } // if
  } // renderCurrentCanvas

  protected function renderMessageAboveCanvas() {
    /* to be defined in child class */
    return(null);
  } // renderMessageAboveCanvas

  public function renderCanvas() {
    $this->canvasList = $this->getCanvasList();
    $this->analyzeCanvasTag();
    $str = $this->renderTopDashBoard()
      .$this->renderMessageAboveCanvas()
      .$this->renderCurrentCanvas()
      .$this->renderFooter();
    return($str);
  } // renderCanvas

} // CecFrameMultiCanvas
?>
