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
/* cecAjax.js */

var cec = cec || {};

cec.ajax = {};

cec.ajax.collectFormInput = function(formId, formDataArray) {
  var form = document.getElementById(formId);
  if (form == null) {
    return;
  } // if
  var objArray = cec.ajax.findChildInputElement(form);
  for (var i=0; i<objArray.length; i++) {
    var inputObj = objArray[i];
    if (cec.ajax.notSavableInput(inputObj)) continue;
    if (inputObj.name == "") continue;
    formDataArray[inputObj.name] = inputObj.value;
  } // for
}; // collectFormInput

cec.ajax.findChildInputElement = function(node) {
var paramArray = new Array();
var paramCount = 0;
  if (node == null) {
    return(paramArray);
  } // if
  var childList = node.childNodes;
  if (childList == null) {
    return(paramArray);
  } // if
  for (var i=0; i<childList.length; i++) {
    var child = childList[i];
    if ((child.tagName == "INPUT") || (child.tagName == "TEXTAREA") || 
        (child.tagName == "SELECT")){
      if (cec.ajax.notSavableInput(child)) continue;
      paramArray[paramCount] = child;
      paramCount++;
    } else {
      var childParamArray = cec.ajax.findChildInputElement(child);
      for (var j=0; j<childParamArray.length; j++) {
        paramArray[paramCount] = childParamArray[j];
        paramCount++;
      } // for
    } // else
  } // for
  return(paramArray);
}; // findChildInputElement

cec.ajax.notSavableInput = function(obj) {
  var objType = obj.type.toUpperCase();
  if ((objType=="RADIO") || (objType=="CHECKBOX")) {
    if (!obj.checked) return(true);
  } // if
  return(false);
}; // notSavableInput

cec.ajax.formInputToUrl = function(formDataArray) {
  if (formDataArray == null) return(null);
  var str = "";
  for(field in formDataArray) {
    if (str != "") {
       str += "&";
    } // if
    str += field +"="+escape(formDataArray[field]);
  } // for
  return(str);
} // formInputToUrl


cec.ajax.checkFormRequiredFields = function(formDataArray,
    requiredFieldString) {
  if (formDataArray == null) return(null);
  if (requiredFieldString == null) return(null);
  var requiredFieldArray = requiredFieldString.split(",");
  var missingFieldArray = new Array();
  var missingFieldCount = 0;
  for(field in requiredFieldArray) {
    if (typeof(requiredFieldArray[field]) == 'function') {
      continue;
    } // if
    var fieldName = requiredFieldArray[field];
    if (typeof(formDataArray[fieldName]) == 'undefined') {
      missingFieldArray[missingFieldCount] = fieldName;
      missingFieldCount++;
    } else if ((formDataArray[fieldName] == "") ||
      (formDataArray[fieldName] == null)) {
      missingFieldArray[missingFieldCount] = fieldName;
      missingFieldCount++;
    } // if
  } // for
  return(missingFieldArray);
} // checkFormRequiredFields
