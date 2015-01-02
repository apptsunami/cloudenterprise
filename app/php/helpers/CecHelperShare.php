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
require_once($rDir.'cec/php/views/CecUrlParam.php');

class CecHelperShare {

  static public function shareUrlFacebook($url, $title) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('u', $url);
    $uParam->appendKeyValuePair('t', $title);
    return("http://www.facebook.com/sharer.php?".$uParam->toString());
  } // shareUrlFacebook

  static public function shareUrlMyspace($url, $title, $c, $l) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('u', $url);
    $uParam->appendKeyValuePair('t', $title);
    $uParam->appendKeyValuePair('c', $c);
    $uParam->appendKeyValuePair('l', $l);
    return("http://www.myspace.com/Modules/PostTo/Pages/?".$uParam->toString());
  } // shareUrlMyspace

  static public function shareUrlDelicious($url, $title, $partner) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('v', 4);
    $uParam->appendKeyValuePair('jump', 'close');
    $uParam->appendKeyValuePair('url', $url);
    $uParam->appendKeyValuePair('title', $title);
    $uParam->appendKeyValuePair('partner', $partner);
    return("http://del.icio.us/post?noui&".$uParam->toString());
  } // shareUrlDelicious

  static public function shareUrlOrkut($url, $title) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('u', $url);
    return("http://www.orkut.com/FavoriteVideos.aspx?".$uParam->toString());
  } // shareUrlOrkut

  static public function shareUrlStumbleupon($url, $title) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('u', $url);
    return("http://www.orkut.com/FavoriteVideos.aspx?".$uParam->toString());
  } // shareUrlStumbleupon

  static public function shareUrlDigg($url, $title, $bodyText, $topic) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('phase', 2);
    $uParam->appendKeyValuePair('url', $url);
    $uParam->appendKeyValuePair('title', $title);
    $uParam->appendKeyValuePair('bodyText', $bodyText);
    $uParam->appendKeyValuePair('topic', $topic);
    return("http://digg.com/submit?".$uParam->toString());
  } // shareUrlDigg

  static public function shareUrlLiveSpaces($url, $title, $description) {
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('Title', $title);
    $uParam->appendKeyValuePair('SourceURL', $url);
    $uParam->appendKeyValuePair('description', $description);
    return("http://spaces.live.com/BlogIt.aspx?".$uParam->toString());
  } // shareUrlLiveSpaces

  static public function shareUrlMixx($format, $url, $partner) {
    /* $format is 'video' */
    $uParam = new CecUrlParam();
    $uParam->appendKeyValuePair('partner', $partner);
    $uParam->appendKeyValuePair('page_url', $url);
    return("http://www.mixx.com/submit/".$format."?".$uParam->toString());
  } // shareUrlMixx

} // CecHelperShare
?>
