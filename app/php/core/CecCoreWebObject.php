<?php
/* Copyright (C) 2008 App Tsunami, Inc. */
/* CecCoreWebObject.php */
$rDir = '';
require_once($rDir.'cec/php/core/CecCoreRequest.php');

class CecCoreWebObject {

  const PARAM_INVITED_UIDS = 'ids';

  protected function getRequestParameter($key) {
    return(CecCoreRequest::getRequestParameter($key));
  } // getRequestParameter

  protected function getInvitedUidList() {
    return($this->getRequestParameter(self::PARAM_INVITED_UIDS));
  } // getInvitedUidList

  protected function getEventClassFromParameter() {
    return($this->getRequestParameter(CecConfig::PARAM_EVENT_CLASS));
  } // getEventClassFromParameter

  protected function getActionClassFromParameter() {
    return($this->getRequestParameter(CecConfig::PARAM_ACTION_CLASS));
  } // getActionClassFromParameter

  protected function getRequestParameterArray($get=TRUE, $post=TRUE) {
    return(CecCoreRequest::getRequestParameterArray($get, $post));
  } // getRequestParameterArray

  protected function getRequestParametersByPrefix($prefix, $get=TRUE,
      $post=TRUE) {
    $prefixLen = strlen($prefix);
    $paramArray = $this->getRequestParameterArray($get, $post);
    $valueArray = array();
    foreach($paramArray as $key => $value) {
      if (substr($key, 0, $prefixLen) == $prefix) {
        $valueArray[] = $value;
      } // if
    } // foreach
    return($valueArray);
  } // getRequestParametersByPrefix

} // CecCoreWebObject
?>
