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
require_once($rDir.'cec/php/helpers/CecHelperHtml.php');

/**
 *  Support the generation of various subscription <a> tags.
 */
class CecHelperSubscribeFeed {

  const AMP = '&amp;';

  static private function _generateTag($fullUrl, $label, $attrArray) {
    return('<a href="'.$fullUrl.'" '
      .CecHelperHtml::injectHtmlTagAttrArray($attrArray)
      .'>'.htmlEntities($label).'</a>');
  } // _generateTag

  static private function _generateRss2Fragment($url) {
    return($url.'?feed=rss2');
  } // _generateRss2Fragment

  /**
   *  Returns an HTML <a> tag for RSS Feed subscription.
   *  @param string url Full url like 'http://www.mycompany.com' but without parameters
   *  @param string label User-readable lable of the <a> tag
   *  @param array attrArray Array of <a> tag attributes
   *  @return string HTML <a> tag
   */
  static public function subscribeFeedRSS($url, $label='RSS Feed', $attrArray=null) {
    $fullUrl = self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedRSS

  /**
   *  Returns an HTML <a> tag for Bloglines subscription.
   *  @param string url Full url like 'http://www.mycompany.com' but without parameters
   *  @param string label User-readable lable of the <a> tag
   *  @param array attrArray Array of <a> tag attributes
   *  @return string HTML <a> tag
   */
  static public function subscribeFeedBloglines($url, $label='Bloglines', $attrArray=null) {
    $fullUrl = 'http://www.bloglines.com/sub/'.self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedBloglines

  static public function subscribeFeedGoogleReader($url, $label='Google Reader', $attrArray=null) {
    $fullUrl = 'http://fusion.google.com/add?feedurl='.self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedGoogleReader

  static public function subscribeFeedMyAol($url, $label='My AOL', $attrArray=null) {
    $fullUrl = 'http://feeds.my.aol.com/add.jsp?url='.self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedMyAol

  static public function subscribeFeedMyMsn($url, $label='My MSN', $attrArray=null) {
    $fullUrl = 'http://my.msn.com/addtomymsn.armx?id=rss'
      .self::AMP.'ut='.self::_generateRss2Fragment($url)
      .self::AMP.'ru='.$url;
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedMyMsn

  static public function subscribeFeedMyYahoo($url, $label='My Yahoo!', $attrArray=null) {
    $fullUrl = 'http://add.my.yahoo.com/rss?url='.self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedMyYahoo

  static public function subscribeFeedNewsgator($url, $label='NewsGator', $attrArray=null) {
    $fullUrl = 'http://www.newsgator.com/ngs/subscriber/subext.aspx?url='
      .self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedNewsgator

  static public function subscribeFeedPageflakes($url, $label='Pageflakes', $attrArray=null) {
    $fullUrl = 'http://www.pageflakes.com/subscribe.aspx?url='
      .self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedPageflakes

  static public function subscribeFeedTechnorati($url, $label='Technorati', $attrArray=null) {
    $fullUrl = 'http://technorati.com/faves?add='.$url;
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedTechnorati

  static public function subscribeFeedWindowsLive($url, $label='Windows Live', $attrArray=null) {
    $fullUrl = 'http://www.live.com/?add='.self::_generateRss2Fragment($url);
    return(self::_generateTag($fullUrl, $label, $attrArray));
  } // subscribeFeedWindowsLive

} // CecHelperSubscribeFeed
?>
