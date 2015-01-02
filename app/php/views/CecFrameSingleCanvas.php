<?php
/* views/CecFrameSingleCanvas.php */
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
require_once($rDir.'cec/php/CecLocalization.php');
require_once($rDir.'cec/php/helpers/CecHelperHtml.php');
require_once($rDir.'cec/php/views/CecExecuteAction.php');
require_once($rDir.'cec/php/views/CecUIObject.php');

class CecFrameSingleCanvas extends CecUIObject {

  const HELP_PAGE = 'help.php';

  /* TAB_STYLE_CLASSIC is being phased out */
  const TAB_STYLE_CLASSIC = 1;
  const TAB_STYLE_NATIVE = 2;

  protected $mainCanvas;
  protected $canvasParamName;
  protected $executeAction;
  protected $actionCompletionMessage;
  protected $callbackMode;
  protected $tabStyle;

  /* constructors */
  public function __construct() {
    parent::__construct();
    $this->callbackMode = FALSE;
    $this->canvasParamName = CecConfig::PARAM_CMDLEVEL1;
    //$loc = new CecLocalization();
    $this->mainCanvas = null;
    $this->executeAction = new CecExecuteAction($this);
    $this->actionCompletionMessage = null;
    $this->tabStyle = self::TAB_STYLE_NATIVE;
  } // __construct

  /* methods */
  public function getActionCompletionMessage() {
    return($this->actionCompletionMessage);
  } // getActionCompletionMessage

  protected function getDefaultCanvas() {
    /* should be defined in subclass */
    return('');
  } // getDefaultCanvas

  public function getCallbackMode() {
    return($this->callbackMode);
  } // getCallbackMode

  public function setCallbackMode($callbackMode) {
    $this->callbackMode = $callbackMode;
  } // setCallbackMode

  public function setExecuteAction($executeAction) {
    $this->executeAction = $executeAction;
  } // setExecuteAction

  private function renderIcon($icon, $title) {
      return($this->context->getDataSource()->renderImg($icon, $title));
  } // renderIcon

  protected function renderInstallLink() {
/* This function is obsolete
    $ds = $this->context->getDataSource();
    if ($ds->isAppAdded()==0) {
      return('<a href="'.$ds->getAddApplicationURLBase().'">'
        .$this->lookUpLocale(CecLocalization::MSG_INSTALL).'</a>');
    } // if
*/
    return(null);
  } // renderInstallLink

  protected function constructNavigationUrl($canvas, $selected, $align="left") {
    $ds = $this->getDataSource();
    $uParam = new CecUrlParam();
    $uParam->appendAllCurrentParameters(0, 0);
    $uParam->removeKey(CecConfig::PARAM_CMDLEVEL4);
    $uParam->removeKey(CecConfig::PARAM_CMDLEVEL3);
    $uParam->removeKey(CecConfig::PARAM_CMDLEVEL2);
    $uParam->appendKeyValuePair($this->canvasParamName, $canvas->getTag());

    $page = $ds->getMyPageName(); 
    $urlParameters = $uParam->toString();
    $iconTitle = $canvas->getLabel();
    $icon = $this->renderIcon($canvas->getIcon(), $iconTitle);
    $label = ucwords($iconTitle);

    switch($this->tabStyle) {
    case self::TAB_STYLE_NATIVE:
      return($ds->renderTabItemInString($page, $urlParameters, $label, $icon,
        $selected, $align));
    case self::TAB_STYLE_CLASSIC:
    default:
      return($this->renderClassicTabItem($page, $urlParameters, $label, $icon,
        $selected));
    } // switch
  } // constructNavigationUrl

  protected function renderClassicTabItem($page, $urlParameters, $label, $icon,
      $selected) {
    $canvasLink = $icon;
    /* bold means selected */
    if ($selected) {
      $canvasLink .= '<b>';
    } // if
    $canvasLink .= $label;
    if ($selected) {
      $canvasLink .= '</b>';
    } // if
    $ds = $this->getDataSource();
    return($ds->renderHrefInString($page, $urlParameters, $canvasLink));
  } // renderClassicTabItem

  protected function renderHelpLink() {
    $this->helpCanvas = new CecCanvasHelp($this);
    $str = $this->constructNavigationUrl($this->helpCanvas, FALSE, 'right');
    return($str);
/*
    return('<a href="'.self::HELP_PAGE.'">'
      .$this->lookUpLocale(CecLocalization::MSG_HELP).'</a>');
*/
  } // renderHelpLink

  protected function renderRightHandSide() {
    $str = $this->renderInstallLink();
    if (!is_null($str)) {
      $str .= CecHelperHtml::pipe();
    } // if
    $str .= $this->renderHelpLink();
    return($str);
  } // renderRightHandSide

  protected function renderTopNavigationBar() {
    return(null);
  } // renderTopNavigationBar

  protected function renderTopRow() {
    $navBar = $this->renderTopNavigationBar();
    $rightHandSide = null;

    switch($this->tabStyle) {
    case self::TAB_STYLE_NATIVE:
      $ds = $this->context->getDataSource();
      $str = $ds->renderTabs($navBar.$rightHandSide);
      return('<div class="clearfix">'.$str.'</div>');
    case self::TAB_STYLE_CLASSIC:
    default:
      $str = '<div class="dh_actions">'.$navBar.'</div>'
        .'<div class="dh_help">'.$rightHandSide.'</div>';
      return('<div class="dh_links clearfix">'.$str.'</div>');
    } // switch
  } // renderTopRow

  protected function renderApplicationTitle() {
    return($this->context->getDataSource()->renderApplicationTitle());
  } // renderApplicationTitle

  protected function renderButtonsOnTop() {
    /* child class can define this */
    return(null);
  } // renderButtonsOnTop

  protected function renderMessageNextToTitle() {
    /* to be defined in child class */
    return(null);
  } // renderMessageNextToTitle

  protected function renderTopDashBoard() {
    $str = '<div class="dashboard_header">'
      .'<div class="dh_titlebar clearfix">'
      .$this->renderApplicationTitle()
      .$this->renderMessageNextToTitle()
      .'<div class="topRightLinks">'
      .$this->renderButtonsOnTop()
      .'</div>'
      .'</div>'
      .$this->renderTopRow()
      .'</div>';
    return($str);
  } // renderTopDashBoard

  private function renderCurrentCanvas() {
    $subCanvas = $this->mainCanvas;
    if (!is_null($subCanvas)) {
      return($subCanvas->render());
    } else {
      return(null);
    } // if
  } // renderCurrentCanvas

  protected function renderFooter() {
    return($this->lookUpLocale(CecLocalization::MSG_POWERED_BY_TFW));
  } // renderFooter

  public function doAction() {
    /* execute actions first */
    if (!is_null($this->executeAction)) {
      $this->actionCompletionMessage = $this->executeAction->execute();
    } // if
  } // doAction

  public function render() {
    $this->doAction();
    /* then draw */
    return($this->renderCanvas());
  } // render

  protected function renderCanvas() {
    $this->mainCanvas = $this->getDefaultCanvas();
    $str = $this->renderTopDashBoard()
      .$this->renderCurrentCanvas()
      .$this->renderFooter();
    /* draw to screen */
    return($str);
  } // render

} // CecFrameSingleCanvas
?>
