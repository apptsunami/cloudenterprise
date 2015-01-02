<?php
/* Copyright (C) 2008 App Tsunami, Inc. */
/* common/CecCoreRequest.php */
$rDir = '';

class CecCoreRequest {

  static public function issetRequestParameter($key) {
    if (isset($_GET[$key]) || isset($_POST[$key])) {
      return(TRUE);
    } else {
      return(FALSE);
    } // else
  } // issetRequestParameter

  static public function getRequestParameter($key, $checkNormalParams=TRUE,
      $checkAppParams=FALSE) {
    /* both $checkNormalParams and $checkAppParams are ignored */
    if (isset($_GET[$key])) {
      return($_GET[$key]);
    } else if (isset($_POST[$key])) {
      return($_POST[$key]);
    } else {
      return(null);
    } // else
  } // getRequestParameter

  static public function getRequestParameterArray($get=TRUE, $post=TRUE) {
    $pArray = array();
    if ($get) {
      $pArray = array_merge($pArray, $_GET);
    } // if
    if ($post) {
      $pArray = array_merge($pArray, $_POST);
    } // if
    return($pArray);
  } // getRequestParameterArray

  static public function getProfileUser() {
    /* only for Facebook */
    return(null);
  } // getProfileUser

} // CecCoreRequest
?>
