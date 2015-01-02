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
/* CecHtmlHelper.php*/
$rDir = '';
require_once($rDir.'cec/php/CecSystemProfile.php');
require_once($rDir.'php/cec/CecApplicationConfig.php');

class  CecHtmlHelper {
  const CSS_FILENAME = 'css/application.css';
  const IMAGES_FB_DIR = 'cec/images.fb';
  const TFW_SCRIPT_DIR = 'cec/js';
  const TFW_CSS_DIR = 'cec/css';
  const APP_SCRIPT_DIR = 'js';

  private $emulatorMode;
  private $applicationTitle;
  private $profile;

  public function __construct($emulatorMode, $applicationTitle) {
    $this->emulatorMode = $emulatorMode;
    $this->applicationTitle = $applicationTitle;
    $this->profile = CecSystemProfile::getHtmlProfile();
  } // __construct

  private function getFullPath($path) {
    $rDir = '';
    return($rDir.$path);
  } // getFullPath

  private function getFilesInDir($dirName) {
    $results = array();
    $dir = opendir($dirName);
    while (($file = readdir($dir)) !== false) {
      if ($file != '.' && $file != '..') {
            $results[] = $file;
      } // if
    } // while
    closedir($dir);
    return($results);
  } // getFilesInDir

  public function renderHeader() {
    $str = null;
    if ($this->profile['showHtmlTag']) {
      $str .= '<HTML><HEAD><TITLE>'.$this->applicationTitle.'</TITLE></HEAD>';
      $str .= '<body class="fbframe"> <div id="book"> <div class="canvas_rel_positioning">';
    } else if ($this->profile['showDivTag']) {
      $str .= '<div class="fbframe">';
    } // if
    $str .= $this->renderStyle(FALSE);
    $str .= $this->renderScriptSrc();
    return($str);
  } // renderHeader

  public function renderFooter() {
    $str = null;
    if ($this->profile['showHtmlTag']) {
      $str .= '</div></div></BODY></HTML>';
    } else if ($this->profile['showDivTag']) {
      $str .= '</div>';
    } // if
    return($str);
  } // renderFooter

  private function renderStyle($embed) {
    $str = null;
    /* openface styles */
    $cssFileNameList = $this->getFilesInDir($this->getFullPath(self::TFW_CSS_DIR));
    foreach($cssFileNameList as $css) {
      $cssPath = $this->getFullPath(self::TFW_CSS_DIR).'/'.$css;
      $str .= $this->renderOneStyleFile($cssPath, $embed);
    } // foreach

    /* fb styles: used for debugging */
    if ($this->profile['renderFbStyles']) {
      /* include the Facebook css */
      $cssList = array( "actionspro.css", "canvas.css", "common.css", "confirmation.css", "pages.css");
      foreach ($cssList as $css) {
        $cssPath = $this->getFullPath(self::IMAGES_FB_DIR).'/'.$css;
        $str .= $this->renderOneStyleFile($cssPath, $embed);
      } // foreach
    } // if
    $str .= $this->renderOneStyleFile($this->getFullPath(self::CSS_FILENAME), $embed);
    return($str);
  } // renderStyle

  private function renderOneStyleFile($filePath, $embed) {
    if ($embed) {
?>
<style type="text/css"> <?php require($filePath); ?> </style>
<?php
      return(null);
    } else {
      return('<style type="text/css">'.file_get_contents($filePath).'</style>');
    } // else
  } // renderOneStyleFile

  public function renderStyleLinks() {
    return($this->renderStyle(FALSE));
  } // renderStyleLinks

  private function expandJsFileName($dirName, $f) {
      return(CecApplicationConfig::APP_CALLBACK_URL.'/'.$dirName.'/'.$f);
  } // expandJsFileName

  private function renderScriptSrcDir($dirName, $embed) {
    $str = null;
    $fileNameList = $this->getFilesInDir($this->getFullPath($dirName));
    foreach($fileNameList as $f) {
      if ($embed) {
        $scriptPath = $this->getFullPath($dirName).'/'.$f;
        $str .= '<script type="text/javascript">'
          .file_get_contents($scriptPath).'</script>';
      } else {
        $str .= '<script type="text/javascript" src="'.$this->expandJsFileName($dirName,$f).'"></script>';
      } // else
    } // foreach
    return($str);
  } // renderScriptSrcDir

  public function renderScriptSrc($embed=FALSE) {
    if ($this->profile['renderScriptSrc']==0) return;

    $str = null;
    /* order is important */
    $jsList = Array (
      'openfaceUtil.js',
      'openfaceDebug.js',
      'openfaceConfig.js',
      'openfaceLocalization.js',
      'openfaceView.js',
      'openfaceMultiCanvasView.js',
      'openfaceInvite.js',
      'openfaceMvc.js'
    );
    $jsFileNameList = $this->getFilesInDir($this->getFullPath(self::TFW_SCRIPT_DIR));
    foreach($jsList as $f) {
      if (array_search($f, $jsFileNameList) !== FALSE) {
        $str .= '<script src="'.$this->expandJsFileName(self::TFW_SCRIPT_DIR,$f).'"></script>';
      } // if
    } // foreach
    $str .= $this->renderScriptSrcDir(self::APP_SCRIPT_DIR, $embed);
    return($str);
  } // renderScriptSrc

} //  CecHtmlHelper
?>
