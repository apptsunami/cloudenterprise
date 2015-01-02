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
$rDir = '';

class CecHelperVideo {

  const SINGLE_QUOTE = "'";
  const DOUBLE_QUOTE = '"';

  static public function embedFlowplayer3($playerUrl, $videoUrl, $playerId, $playerName,
      $width, $height) {
    $str = '<a id="'.$playerId
     // .'" href="'.$videoUrl
      .'" style="display:block;width:'.$width.';height:'.$height.'">'
      .'</a>'
      .'<script>'
      .'flowplayer("'.$playerId.'","'.$playerUrl.'");'
      .'</script>';
    return($str);
  } // embedFlowplayer

  static private function _serializeParamArray($fv, $quoteChar=self::DOUBLE_QUOTE, $quoteKeyNames=TRUE) {
    $str='{';
    $count = 0;
    foreach($fv as $key=>$value) {
      if ($count > 0) $str .= ',';
      if ($quoteKeyNames) {
        $keyName = $quoteChar.$key.$quoteChar;
      } else {
        $keyName = $key;
      } // else
      if (is_null($value)) {
        $str .= $keyName.':null';
      } else if ($value === FALSE) {
        $str .= $keyName.':false';
      } else if ($value === TRUE) {
        $str .= $keyName.':true';
      } else {
        $str .= $keyName.':'.$value;
      } // else
      $count++;
    } // foreach
    $str.='}';
    return($str);
  } // _serializeParamArray

  static private function _quoteStr($str, $quoteChar) {
    return($quoteChar.$str.$quoteChar);
  } // _quoteStr

  static private function _generatePlugins($quoteChar=self::DOUBLE_QUOTE, $quoteKeyNames=TRUE) {
    $quoteKeyNames=TRUE; // must be TRUE
    $fv = Array();

    $fv["url"] = self::_quoteStr('flowplayer.controls-3.0.1.swf',$quoteChar);

    // display properties ;
    $fv["bottom"] =0;
    $fv["height"] = 0; // default 24
    $fv["z-index"] =1;
    $fv["backgroundColor"] = self::_quoteStr('#2d3e46', $quoteChar);
    $fv["backgroundGradient"] = self::_quoteStr('low', $quoteChar);

    // controlbar specific configuration  ;
    $fv["fontColor"] = self::_quoteStr('#ffffff', $quoteChar);
    $fv["timeFontColor"] = self::_quoteStr('#333333', $quoteChar);
    $fv["autoHide"] = self::_quoteStr('never', $quoteChar);

    // which buttons are visible and which not ?
/*
    $fv["play"] =false;
    $fv["volume"] =false;
    $fv["mute"] =false;
    $fv["time"] =false;
    $fv["stop"] =false;
    $fv["playlist"] =false;
    $fv["fullscreen"] =false;
*/

    // scrubber is a well known nickname to the timeline/playhead combination 
    $fv["scrubber"] = false;
    // you can also use "all" flag to disable/enable all controls
    $fv["all"] =false;

    $fv1 = Array();
    $fv1["controls"] = self::_serializeParamArray($fv, $quoteChar, $quoteKeyNames);
    return(self::_serializeParamArray($fv1, $quoteChar, $quoteKeyNames));
  } // _generatePlugins

  static private function _generateNullControl($quoteChar=self::DOUBLE_QUOTE, $quoteKeyNames=TRUE) {
    $fv = Array();
    $fv["controls"] = null;
    return(self::_serializeParamArray($fv, $quoteChar, $quoteKeyNames));
  } // _generateNullControl

  static private function _generateConfigString($videoUrl, $playerId, $playerName,
      $width, $height, $autoBuffering=TRUE, $autoPlay=FALSE, $showControlBar=FALSE,
      $quoteChar=self::DOUBLE_QUOTE, $quoteKeyNames=TRUE) {

    $fv = Array();
    //$onMetaDataFunc = 'function(){this.stopBuffering();}';

    /* see http://flowplayer.org/v2/tutorials/skinning.html */
    /* minimalist skin */
/*
    $fv["showStopButton"] = false;
    $fv["showScrubber"] = false; 
    $fv["showVolumeSlider"] = false;
    $fv["showMuteVolumeButton"] = false; 
    $fv["showFullScreenButton"] = false; 
    $fv["showMenu"] = false;
    $fv["showPlayListButtons"] = true;
    $fv["controlsOverVideo"] = '"locked"';
    $fv["controlBarBackgroundColor"] = "-1";
    $fv["controlBarGloss"] = '"none"';
*/

    if (!is_null($playerId)) {
      $fv["playerId"] = $quoteChar.$playerId.$quoteChar;
    } // if
      $fv1 = Array();
      $fv["clip"] = self::_serializeParamArray($fv1, $quoteChar, $quoteKeyNames);

    if (!is_null($videoUrl)) {
      $fv2 = Array();
      $fv2['url'] = $quoteChar.$videoUrl.$quoteChar;
      $fv2['autoBuffering'] = $autoBuffering;
      $fv2['autoPlay'] = $autoPlay;
      $fv["playlist"] = '['.self::_serializeParamArray($fv2, $quoteChar, $quoteKeyNames).']';
    } // if
    $fv2 = Array();
    if (!$showControlBar) {
      $fv["plugins"] = self::_generateNullControl($quoteChar, $quoteKeyNames);
    } // if
    return(self::_serializeParamArray($fv, $quoteChar, $quoteKeyNames));
  } // _generateConfigString

  static private function _generateFlashParameters($playerUrl, $videoUrl, $playerId, $playerName,
      $width, $height, $includeConfig, $includeDetails=TRUE, 
      $autoBuffering=TRUE, $autoPlay=FALSE, $showControlBar=FALSE) {

    $str = null;
    if ($includeDetails) {
      $str .= ' bgcolor="#000000" '
        .' pluginspage="http://www.adobe.com/go/getflashplayer" '
        .' type="application/x-shockwave-flash" '
        .' quality="high" '
        .' allowscriptaccess="always" '
        .' allowfullscreen="true" '
        .' src="'.$playerUrl.'"';
    } // if
    if ($includeConfig) {
      $flashVars='config='.self::_generateConfigString($videoUrl, $playerId, $playerName,
        $width, $height, $autoBuffering, $autoPlay, $showControlBar, self::DOUBLE_QUOTE, TRUE);
      $str .= ' flashvars=\''.$flashVars.'\'';
    } // if
    if (!is_null($width)) {
      $str .= ' width="'.$width.'" ';
    } // if
    if (!is_null($height)) {
      $str .= ' height="'.$height.'" ';
    } // if
    if (!is_null($playerName)) {
      $str .= 'name="'.$playerName.'" ';
    } // if
    if (!is_null($playerId)) {
      $str .= 'id="'.$playerId.'" ';
    } // if
    return($str);
  } // _generateFlashParameters

  static private function _generateSwfParameters($playerUrl, $height, $width,
      $quoteChar=self::DOUBLE_QUOTE, $quoteKeyNames=TRUE) {
    $fv = Array();
    $fv["src"] = $quoteChar.$playerUrl.$quoteChar;
    if (!is_null($width)) {
      $fv["width"]=$width;
    } // if
    if (!is_null($height)) {
      $fv["height"]=$height;
    } // if
    return(self::_serializeParamArray($fv, $quoteChar, $quoteKeyNames));
  } // _generateSwfParameters

  static public function embedFlowplayer($playerUrl, $videoUrl, $playerId, $playerName,
      $width, $height, $autoBuffering=TRUE, $autoPlay=FALSE, $showControlBar=FALSE) {
    $configStr = self::_generateFlashParameters($playerUrl, $videoUrl, $playerId, $playerName,
      $width, $height, FALSE, FALSE, $autoBuffering, $autoPlay, $showControlBar);
    $flashVars=self::_generateConfigString($videoUrl, null, $playerName,
      $width, $height, $autoBuffering, $autoPlay, $showControlBar, self::DOUBLE_QUOTE, FALSE);
    $swfParameters = self::_generateSwfParameters($playerUrl, $height, $width,
      self::DOUBLE_QUOTE, false);
    $str = '<span '.$configStr.'></span>'
      .'<script>'
      .'flowplayer("'.$playerId.'",'.$swfParameters.','
        .$flashVars.');'
      .'</script>';
    return($str);
  } // embedFlowplayer

  static public function embedFlowplayerMinimal($playerUrl, $videoUrl, $playerId, $playerName,
      $width, $height, $autoBuffering=TRUE, $autoPlay=FALSE, $showControlBar=FALSE) {
    $configStr = self::_generateFlashParameters($playerUrl, $videoUrl, $playerId, $playerName,
      $width, $height, FALSE, FALSE, $autoBuffering, $autoPlay, $showControlBar);
    $str = '<span '.$configStr.'></span>'
      .'<script>'
      .'flowplayer("'.$playerId.'", "'.$playerUrl.'", "'.$videoUrl.'");'
      .'</script>';
    return($str);
  } // embedFlowplayerMinimal

} // CecHelperVideo
?>
