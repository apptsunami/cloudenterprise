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
/* Cecmodels/CecDataSource.php */
$rDir = '';
require_once($rDir.'cec/php/models/CecSiteWrapper.php');
require_once($rDir.'php/cec/CecApplicationConfig.php');

class CecDataSource {

  protected $socialNetworkWrapper; // object
  protected $userId; // int
  protected $addUrl; // String
  protected $isAppAdded; // boolean
  protected $friendList; // object array
  protected $friendIsAppAddedList; // object array

  public function __construct() {
    /* no-op */
  } // __construct

  /* methods */
  private function getClient() {
    if (!isset($this->socialNetworkWrapper)) {
      $this->socialNetworkWrapper = new CecSiteWrapper();
    } // if
    return($this->socialNetworkWrapper);
  } // getClient

  public function startDisplay() {
    return($this->getClient()->startDisplay());
  } // startDisplay

  public function isSocialNetworkParameter($key) {
    return($this->getClient()->isSocialNetworkParameter($key));
  } // isSocialNetworkParameter

  public function getUserId() {
    return($this->getClient()->getUserId());
  } // getUserId

  public function getUserTimeZone() {
    return($this->getClient()->getUserTimeZone());
  } // getUserTimeZone

  public function getUserFirstLastName($uid=null) {
    if (is_null($uid)) {
      return($this->getClient()->getUserFirstLastName($this->getUserId()));
    } else {
      return($this->getClient()->getUserFirstLastName($uid));
    }
  } // getUserFirstLastName

  public function getFriendList() {
    return($this->getClient()->getFriendList());
  } // getFriendList

  public function getFriendWithApp() {
    return($this->getClient()->getFriendWithApp());
  } // getFriendWithApp

  public  function getFriendIsAppAddedList() {
    return($this->getClient()->getFriendIsAppAddedList());
  } // getFriendIsAppAddedList

  public  function getFriendIsAppAdded($uid) {
    return($this->getClient()->getFriendIsAppAdded($uid));
  } // getFriendIsAppAdded

  public function isAppAdded() {
    return($this->getClient()->isAppAdded());
  } // isAppAdded

  public function getAddApplicationURLBase() {
    return($this->getClient()->getAddApplicationURLBase());
  } // getAddApplicationURLBase

  public function getUserAffiliationList($uid=null) {
    return($this->getClient()->getUserAffiliationList($uid));
  } // getUserAffiliationList

  public function getUserPrimaryAffiliation($uid=null) {
    return($this->getClient()->getUserPrimaryAffiliation($uid));
  } // getUserPrimaryAffiliation

  public function profile_getFBML($uid=null) {
    return($this->getClient()->profile_getFBML($uid));
  } // profile_getFBML

  public function generateProfileLink($uid=null) {
    return($this->getClient()->generateProfileLink($uid));
  } // generateProfileLink

  public function beginBatch() {
    $this->getClient()->beginBatch();
  } // beginBatch

  public function endBatch() {
    $this->getClient()->endBatch();
  } // endBatch

  public function feedPublishStoryToUser($feedTitle, $feedBody=null,
      $image_1=null, $image_1_link=null, $image_2=null, $image_2_link=null,
      $image_3=null, $image_3_link=null, $image_4=null, $image_4_link=null) {
    return($this->getClient()->feedPublishStoryToUser($feedTitle, $feedBody,
      $image_1, $image_1_link, $image_2, $image_2_link,
      $image_3, $image_3_link, $image_4, $image_4_link));
  } // feedPublishStoryToUser

  public function feedPublishActionOfUser($feedTitle, $feedBody=null,
      $image_1=null, $image_1_link=null, $image_2=null, $image_2_link=null,
      $image_3=null, $image_3_link=null, $image_4=null, $image_4_link=null) {
    return($this->getClient()->feedPublishActionOfUser($feedTitle, $feedBody,
      $image_1, $image_1_link, $image_2, $image_2_link,
      $image_3, $image_3_link, $image_4, $image_4_link));
  } // feedPublishActionOfUser

  public function feedPublishTemplatizedAction($title_template, $title_data,
      $body_template, $body_data, $body_general, $image_1=null, $image_1_link=null,
      $image_2=null, $image_2_link=null, $image_3=null, $image_3_link=null,
      $image_4=null, $image_4_link=null, $target_ids='', $page_actor_id=null) {
    return($this->getClient()->feed_publishTemplatizedAction($title_template, $title_data,
      $body_template, $body_data, $body_general, $image_1, $image_1_link,
      $image_2, $image_2_link, $image_3, $image_3_link,
      $image_4, $image_4_link, $target_ids, $page_actor_id));
  } // feedPublishTemplatizedAction

  public function  feedRegisterTemplateBundle($oneLineStoryTemplates,
      $shortStoryTemplates, $fullStoryTemplates, $actionLinks=null) {
    return($this->getClient()->
        feedRegisterTemplateBundle($oneLineStoryTemplates,
      $shortStoryTemplates, $fullStoryTemplates, $actionLinks));
  } // feedRegisterTemplateBundle

  public function feedGetRegisteredTemplateBundles() {
    return($this->getClient()->feedGetRegisteredTemplateBundles());
  } // feedGetRegisteredTemplateBundles

  public function feedDeactivateAllCurrentTemplates() {
    return($this->getClient()->feedDeactivateAllCurrentTemplates());
  } // feedDeactivateAllCurrentTemplates

  public function feedPublishUserAction($templateBundleId, $templateData,
      $targetUserIdArray, $bodyGeneral) {
    return($this->getClient()->feedPublishUserAction(
      $templateBundleId, $templateData, $targetUserIdArray, $bodyGeneral));
  } // feedGetRegisteredTemplateBundles

  public function notificationsSend($uid, $message) {
    $this->getClient()->notificationsSend($uid, $message);
  } // notificationsSend

  public function getRequestIdList() {
    return($this->getClient()->getRequestIdList());
  } // getRequestIdList

  public function getMyPageName() {
    return($this->getClient()->getMyPageName());
  } // getMyPageName

  public function renderDialogInString($urlAfterInvite, 
      $installButtonLabel, $formAction, $messageToFriend, $inviteModeStr,
      $hiddenParameters, $explanationToUser, $dialogName, $dialogTitle,
      $formName, $allFriends=FALSE) {

    return($this->getClient()->renderDialogInString($urlAfterInvite,
      $installButtonLabel, $formAction, $messageToFriend, $inviteModeStr,
      $hiddenParameters, $explanationToUser, $dialogName, $dialogTitle,
      $formName, $allFriends));
  } // renderDialogInString

  public function renderMockAjaxButtonInString($form, $url, $divId, 
      $titleText, $showDiv=null, $showDialog=null) {
    return($this->getClient()->renderMockAjaxButtonInString($form, 
      $url, $divId, $titleText, $showDiv, $showDialog));
  } // renderMockAjaxButtonInString

  public function renderMockAjaxHrefInString($formId, $url, $divId, 
      $label, $htmlClass, $showDiv=null, $showDialog=null, $onClick=null,
      $id=null) {
    return($this->getClient()->renderMockAjaxHrefInString($formId,
      $url, $divId, $label, $htmlClass, $showDiv, $showDialog, $onClick, $id));
  } // renderMockAjaxHrefInString

  public function getRootPhp() {
    return($this->getClient()->getRootPhp());
  } // renderHrefUrl

  public function renderHrefUrl($page, $urlParameters) {
    return($this->getClient()->renderHrefUrl($page, $urlParameters));
  } // renderHrefUrl

  public function renderOnClickButtonInString($onClickStr, 
      $titleText) {
    return($this->getClient()->renderOnClickButtonInString($onClickStr,
      $titleText));
  } // renderOnClickButtonInString

  public function renderMockAjaxFormStartInString($dialogName, $dialogSubmitUrl) {
    return($this->getClient()->renderMockAjaxFormStartInString($dialogName, 
      $dialogSubmitUrl));
  } // renderMockAjaxFormStartInString

  public function renderHrefInString($page, $urlParameters, $label, 
      $htmlClass=null, $target=null, $title=null) {
    return($this->getClient()->renderHrefInString($page, 
      $urlParameters, $label, $htmlClass, $target, $title));
  } // renderHrefInString

  public function renderTabItemInString($page, $urlParameters, $label, $icon,
      $selected=FALSE, $align="left") {
    return($this->getClient()->renderTabItemInString($page,
      $urlParameters, $label, $icon, $selected, $align));
  } // renderTabItemInString

  public function renderTabs($tabs) {
    return($this->getClient()->renderTabs($tabs));
  } // renderTabs

  public function renderImg($icon , $title=null, $htmlClass=null, $onclick=null) {
    if (is_null($icon)) return(null);
    if ($icon == '') return(null);
    return($this->getClient()->renderImg($icon, $title, $htmlClass, $onclick));
  } // renderImg

  public function renderApplicationTitle() {
    return($this->getClient()->renderApplicationTitle());
  } // renderImg

  public function renderCallBackUrl($subUrl=null) {
    return($this->getClient()->renderCallBackUrl($subUrl));
  } // renderCallBackUrl

  public function renderInviteForm($urlAfterInvite, 
      $installButtonLabel, $formAction, $messageToFriend, $inviteModeStr,
      $hiddenParameters, $explanationToUser, $onlyFriendsWithoutApp=FALSE,
      $excludeIdList=null) {
    return($this->getClient()->renderInviteForm(
        $urlAfterInvite, $installButtonLabel, $formAction, $messageToFriend, 
        $inviteModeStr, $hiddenParameters, $explanationToUser,
        $onlyFriendsWithoutApp, $excludeIdList));
  } // renderInviteForm

  public function renderConfirmButton($urlAfterInvite, $buttonLabel,
      $onlyIfNotAppUser) {
    return($this->getClient()->renderConfirmButton($urlAfterInvite,
      $buttonLabel, $onlyIfNotAppUser));
  } // renderConfirmButton

  public function getCurrentPageName() {
    return($this->getClient()->getCurrentPageName());
  } // getCurrentPageName

  /* fbml */
  public function fbName($uid=null, $firstNameOnly="true", $useYou="false",
      $linked="true") {
    return($this->getClient()->fbName($uid, $firstNameOnly, $useYou,
      $linked));
  } // fbName

  public function fbProfilePic($uid, $picSize=CecSiteWrapper::DEFAULT_PIC_SIZE) {
    return($this->getClient()->fbProfilePic($uid, $picSize));
  } // fbProfilePic

  public function fbPronoun($uid, $possessive="true") {
    return($this->getClient()->fbPronoun($uid, $possessive));
  } // fbPronoun

  public function fbUserLink($uid, $showNetwork="false") {
    return($this->getClient()->fbUserLink($uid, $showNetwork));
  } // fbuserLink

  public function fbGoogleAnalytics($acctId) {
    return($this->getClient()->fbGoogleAnalytics($acctId));
  } // fbGoogleAnalytics

  public function fbShareUrl($url) {
    return($this->getClient()->fbShareUrl($url));
  } // fbShareUrl

  public function fbShareMeta($metaArray, $linkArray) {
    return($this->getClient()->fbShareMeta($metaArray, $linkArray));
  } // fbShareMeta

  public function fbShareMetaDetails($detailArray) {
    return($this->getClient()->fbShareMetaDetails($detailArray));
  } // fbShareMetaDetails

  public function fbSetInnerHTMLFunction() {
    return($this->getClient()->fbSetInnerHTMLFunction());
  } // fbSetInnerHTMLFunction

  public function wrapLinkAsShare($linkHtml, $inLine=FALSE) {
    return($this->getClient()->wrapLinkAsShare($linkHtml, $inLine));
  } // wrapLinkAsShare

  public function renderShareStyleButton($label, $href, $title=null, 
      $onClick=null) {
    return($this->getClient()->renderShareStyleButton($label, 
      $href, $title, $onClick));
  } // renderShareStyleButton

  public function renderHrefStyleButton($label, $href='#', $htmlClass=null, 
      $title=null, $onClick=null, $id=null, $style=null) {
    return($this->getClient()->renderHrefStyleButton($label, $href, 
      $htmlClass, $title, $onClick, $id, $style));
  } // renderHrefStyleButton

  public function renderHrefStyleSubmit($label, $formId, $htmlClass=null,
       $title=null) {
    return($this->getClient()->renderHrefStyleSubmit($label, $formId, 
      $htmlClass, $title));
  } // renderHrefStyleSubmit

  public function renderShareStyleSubmit($label, $formId, $title=null) {
    return($this->getClient()->renderShareStyleSubmit($label, $formId, $title));
  } // renderHrefStyleSubmit

  public function renderMockAjaxButtonInShareStyle($formId, $url, $divId, 
      $label, $showDiv=null, $showDialog=null) {
    return($this->getClient()->renderMockAjaxButtonInShareStyle($formId, 
      $url, $divId, $label, $showDiv, $showDialog));
  } // renderMockAjaxButtonInShareStyle

  public function getAppInvocationUrl() {
    return(CecApplicationConfig::APP_CALLBACK_URL.'/cec/php/index.php');
  } // getAppInvocationUrl

  public function registerWindowOnLoad($str) {
    return($this->getClient()->registerWindowOnLoad($str));
  } // registerWindowOnLoad

  static function renderDialogResponse($responseTitle, $responseText) {
    return(CecSiteWrapper::renderDialogResponse($responseTitle, 
      $responseText));
  } // renderDialogResponse

  public function renderFriendSelector($uid, $formElementName, $formElementId,
      $includeMe=FALSE, $excludeIdListStr=null, $includeLists=FALSE) {
    return($this->getClient()->renderFriendSelector($uid, 
      $formElementName, $formElementId, $includeMe, $excludeIdListStr, 
      $includeLists));
  } // renderFriendSelector

  public function setProfileContent($fbmlProfile, $fbmlMobile, $fbmlMain,
      $uid=null) {
    return($this->getClient()->setProfileContent($fbmlProfile,
      $fbmlMobile, $fbmlMain, $uid));
  } // setProfileContent

  public function newInfoItem($label, $image, $description, $link) {
    return($this->getClient()->newInfoItem($label, $image, $description, $link));
  } // newInfoItem

  public function setInfoSection($fieldName, $infoItemArrayName, $infoItemArray, 
      $infoSectionType=self::INFO_SECTION_TEXT, $uid=null) {
    return($this->getClient()->setInfoSection($fieldName, $infoItemArrayName,
       $infoItemArray, $infoSectionType, $uid));
  } // setInfoSection

  public function renderAddProfileBoxButton() {
    return($this->getClient()->renderAddProfileBoxButton());
  } // renderAddProfileBoxButton

  public function renderAddInfoTabButton() {
    return($this->getClient()->renderAddInfoTabButton());
  } // renderAddInfoTabButton

  public function renderConfirmationBox($title, $description, $yesLabel,
      $noLabel, $confirmUrl) {
    return($this->getClient()->renderConfirmationBox($title,
      $description, $yesLabel, $noLabel, $confirmUrl));
  } // renderConfirmationBox

  public function renderAlertBox($title, $message) {
    return($this->getClient()->renderAlertBox($title, $message));
  } // renderAlertBox

} // CecDataSource
?>
