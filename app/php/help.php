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
/* help.php */
/**
 * It calls <tt>php/cec/CecHelp.php<tt> to launch the help command which is located on the 
 * upper right hand corner of the application.
 */
$rDir = '';
require_once($rDir.'php/cec/CecApplicationConfig.php');
?>
<h1>
<?PHP echo(CecApplicationConfig::APPLICATION_TITLE); ?>
</h1>
<?PHP require($rDir.'php/cec/CecHelp.php'); ?>
