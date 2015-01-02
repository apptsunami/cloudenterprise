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
/* CecControlUSStatesSelect.php */
$rDir = '';
require_once($rDir.'cec/php/controls/CecControlSelect.php');

class CecControlUSStatesSelect extends CecControlSelect {

    public function __construct($valueFieldName=null, $textFieldName=null,
        $controlAttributeArray=null) {
      $stateNameArray = Array(
        "" => "",
        "AL" => "Alabama",
        "AK" => "Alaska",
        "AB" => "Alberta",
        "AZ" => "Arizona",
        "AR" => "Arkansas",
        "BC" => "British Columbia",
        "CA" => "California",
        "CO" => "Colorado",
        "CT" => "Connecticut",
        "DE" => "Delaware",
        "DC" => "District of Columbia",
        "FL" => "Florida",
        "GA" => "Georgia",
        "HI" => "Hawaii",
        "ID" => "Idaho",
        "IL" => "Illinois",
        "IN" => "Indiana",
        "IA" => "Iowa",
        "KS" => "Kansas",
        "KY" => "Kentucky",
        "LA" => "Louisiana",
        "ME" => "Maine",
        "MB" => "Manitoba",
        "MD" => "Maryland",
        "MA" => "Massachusetts",
        "MI" => "Michigan",
        "MN" => "Minnesota",
        "MS" => "Mississippi",
        "MO" => "Missouri",
        "MT" => "Montana",
        "NE" => "Nebraska",
        "NV" => "Nevada",
        "NB" => "New Brunswick",
        "NF" => "Newfoundland",
        "NH" => "New Hampshire",
        "NJ" => "New Jersey",
        "NM" => "New Mexico",
        "NY" => "New York",
        "NC" => "North Carolina",
        "ND" => "North Dakota",
        "NS" => "Nova Scotia",
        "OH" => "Ohio",
        "OK" => "Oklahoma",
        "ON" => "Ontario",
        "OR" => "Oregon",
        "PA" => "Pennsylvania",
        "PE" => "Prince Edward Island",
        "PQ" => "Quebec",
        "RI" => "Rhode Island",
        "SK" => "Saskatchewan",
        "SC" => "South Carolina",
        "SD" => "South Dakota",
        "TN" => "Tennessee",
        "TX" => "Texas",
        "UT" => "Utah",
        "VT" => "Vermont",
        "VA" => "Virginia",
        "WA" => "Washington",
        "WV" => "West Virginia",
        "WI" => "Wisconsin",
        "WY" => "Wyoming",
      ); // Array
      parent::__construct($stateNameArray, $valueFieldName,
        $textFieldName, $controlAttributeArray);
    } // __construct

} // CecControlUSStatesSelect
?>
