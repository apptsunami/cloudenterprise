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
/* CecSforceTableProfile.php */
require_once('cec/php/models/CecSforceTable.php');

/**
 *  Supports Sforce table 'Profile'.
 */
class CecSforceTableProfile extends CecSforceTable {

  const TABLE_NAME = 'Profile';

  const FIELD_DESCRIPTION = 'Description';
  const FIELD_NAME = 'Name';
  const FIELD_PERMISSIONS_API_ENABLED = 'PermissionsApiEnabled';
  const FIELD_PERMISSIONS_AUTHOR_APEX = 'PermissionsAuthorApex';
  const FIELD_PERMISSIONS_BULK_API_HARD_DELETE = 'PermissionsBulkApiHardDelete';
  const FIELD_PERMISSIONS_CAN_USE_NEW_DASHBOARD_BUILDER = 'PermissionsCanUseNewDashboardBuilder';
  const FIELD_PERMISSIONS_CONVERT_LEADS = 'PermissionsConvertLeads';
  const FIELD_PERMISSIONS_CREATE_MULTIFORCE = 'PermissionsCreateMultiforce';
  const FIELD_PERMISSIONS_CUSTOM_SIDEBAR_ON_ALL_PAGES = 'PermissionsCustomSidebarOnAllPages';
  const FIELD_PERMISSIONS_CUSTOMIZE_APPLICATION = 'PermissionsCustomizeApplication';
  const FIELD_PERMISSIONS_EDIT_CASE_COMMENTS = 'PermissionsEditCaseComments';
  const FIELD_PERMISSIONS_EDIT_EVENT = 'PermissionsEditEvent';
  const FIELD_PERMISSIONS_EDIT_OPP_LINE_ITEM_UNIT_PRICE = 'PermissionsEditOppLineItemUnitPrice';
  const FIELD_PERMISSIONS_EDIT_PUBLIC_DOCUMENTS = 'PermissionsEditPublicDocuments';
  const FIELD_PERMISSIONS_EDIT_READONLY_FIELDS = 'PermissionsEditReadonlyFields';
  const FIELD_PERMISSIONS_EDIT_REPORTS = 'PermissionsEditReports';
  const FIELD_PERMISSIONS_EDIT_TASK = 'PermissionsEditTask';
  const FIELD_PERMISSIONS_ENABLE_NOTIFICATIONS = 'PermissionsEnableNotifications';
  const FIELD_PERMISSIONS_IMPORT_LEADS = 'PermissionsImportLeads';
  const FIELD_PERMISSIONS_INSTALL_MULTIFORCE = 'PermissionsInstallMultiforce';
  const FIELD_PERMISSIONS_MANAGE_ANALYTIC_SNAPSHOTS = 'PermissionsManageAnalyticSnapshots';
  const FIELD_PERMISSIONS_MANAGE_BUSINESS_HOUR_HOLIDAYS = 'PermissionsManageBusinessHourHolidays';
  const FIELD_PERMISSIONS_MANAGE_CALL_CENTERS = 'PermissionsManageCallCenters';
  const FIELD_PERMISSIONS_MANAGE_CASES = 'PermissionsManageCases';
  const FIELD_PERMISSIONS_MANAGE_CATEGORIES = 'PermissionsManageCategories';
  const FIELD_PERMISSIONS_MANAGE_CSS_USERS = 'PermissionsManageCssUsers';
  const FIELD_PERMISSIONS_MANAGE_CUSTOM_REPORT_TYPES = 'PermissionsManageCustomReportTypes';
  const FIELD_PERMISSIONS_MANAGE_DASHBOARDS = 'PermissionsManageDashboards';
  const FIELD_PERMISSIONS_MANAGE_DATA_CATEGORIES = 'PermissionsManageDataCategories';
  const FIELD_PERMISSIONS_MANAGE_DATA_INTEGRATIONS = 'PermissionsManageDataIntegrations';
  const FIELD_PERMISSIONS_MANAGE_DYNAMIC_DASHBOARDS = 'PermissionsManageDynamicDashboards';
  const FIELD_PERMISSIONS_MANAGE_EMAIL_CLIENT_CONFIG = 'PermissionsManageEmailClientConfig';
  const FIELD_PERMISSIONS_MANAGE_LEADS = 'PermissionsManageLeads';
  const FIELD_PERMISSIONS_MANAGE_MOBILE = 'PermissionsManageMobile';
  const FIELD_PERMISSIONS_MANAGE_REMOTE_ACCESS = 'PermissionsManageRemoteAccess';
  const FIELD_PERMISSIONS_MANAGE_SELF_SERVICE = 'PermissionsManageSelfService';
  const FIELD_PERMISSIONS_MANAGE_SOLUTIONS = 'PermissionsManageSolutions';
  const FIELD_PERMISSIONS_MANAGE_USERS = 'PermissionsManageUsers';
  const FIELD_PERMISSIONS_MODIFY_ALL_DATA = 'PermissionsModifyAllData';
  const FIELD_PERMISSIONS_NEW_REPORT_BUILDER = 'PermissionsNewReportBuilder';
  const FIELD_PERMISSIONS_PASSWORD_NEVER_EXPIRES = 'PermissionsPasswordNeverExpires';
  const FIELD_PERMISSIONS_PUBLISH_MULTIFORCE = 'PermissionsPublishMultiforce';
  const FIELD_PERMISSIONS_RUN_REPORTS = 'PermissionsRunReports';
  const FIELD_PERMISSIONS_SCHEDULE_REPORTS = 'PermissionsScheduleReports';
  const FIELD_PERMISSIONS_SEND_SIT_REQUESTS = 'PermissionsSendSitRequests';
  const FIELD_PERMISSIONS_SOLUTION_IMPORT = 'PermissionsSolutionImport';
  const FIELD_PERMISSIONS_TRANSFER_ANY_CASE = 'PermissionsTransferAnyCase';
  const FIELD_PERMISSIONS_TRANSFER_ANY_ENTITY = 'PermissionsTransferAnyEntity';
  const FIELD_PERMISSIONS_TRANSFER_ANY_LEAD = 'PermissionsTransferAnyLead';
  const FIELD_PERMISSIONS_USE_TEAM_REASSIGN_WIZARDS = 'PermissionsUseTeamReassignWizards';
  const FIELD_PERMISSIONS_VIEW_ALL_DATA = 'PermissionsViewAllData';
  const FIELD_PERMISSIONS_VIEW_DATA_CATEGORIES = 'PermissionsViewDataCategories';
  const FIELD_PERMISSIONS_VIEW_MY_TEAMS_DASHBOARDS = 'PermissionsViewMyTeamsDashboards';
  const FIELD_PERMISSIONS_VIEW_SETUP = 'PermissionsViewSetup';
  const FIELD_USER_LICENSE_ID = 'UserLicenseId';
  const FIELD_USER_TYPE = 'UserType';

  const STANDARD_PROFILE_SYSTEM_ADMINISTRATOR = 'System Administrator';
  const STANDARD_PROFILE_SOLUTION_MANAGER = 'Solution Manager';
  const STANDARD_PROFILE_READ_ONLY = 'Read Only';
  const STANDARD_PROFILE_MARKETING_USER = 'Marketing User';
  const STANDARD_PROFILE_CONTRACT_MANAGER = 'Contract Manager';
  const STANDARD_PROFILE_STANDARD_USER = 'Standard User';
  const STANDARD_PROFILE_STANDARD_PLATFORM_USER = 'Standard Platform User';
  const STANDARD_PROFILE_PARTNER_USER = 'Partner User';
  const STANDARD_PROFILE_CUSTOMER_PORTAL_MANAGER = 'Customer Portal Manager';
  const STANDARD_PROFILE_CHATTER_FREE_USER = 'Chatter Free User';
  const STANDARD_PROFILE_CHATTER_MODERATOR_USER = 'Chatter Moderator User';

  public function __construct($partnerClient, $tableProfile=self::TABLE_NAME,
      $fieldNameList=null) {
    if (is_null($fieldNameList)) {
      $fieldNameList = $this->getDefaultFieldNameList();
    } // if
    parent::__construct($partnerClient, $tableProfile, $fieldNameList);
  } // __construct

  public function getDefaultFieldNameList() {
    $fieldNameList = parent::getDefaultFieldNameList();
    $fieldNameList[] = self::FIELD_DESCRIPTION;
    $fieldNameList[] = self::FIELD_NAME;
    $fieldNameList[] = self::FIELD_PERMISSIONS_API_ENABLED;
    $fieldNameList[] = self::FIELD_PERMISSIONS_AUTHOR_APEX;
    $fieldNameList[] = self::FIELD_PERMISSIONS_BULK_API_HARD_DELETE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_CAN_USE_NEW_DASHBOARD_BUILDER;
    $fieldNameList[] = self::FIELD_PERMISSIONS_CONVERT_LEADS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_CREATE_MULTIFORCE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_CUSTOM_SIDEBAR_ON_ALL_PAGES;
    $fieldNameList[] = self::FIELD_PERMISSIONS_CUSTOMIZE_APPLICATION;
    $fieldNameList[] = self::FIELD_PERMISSIONS_EDIT_CASE_COMMENTS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_EDIT_EVENT;
    $fieldNameList[] = self::FIELD_PERMISSIONS_EDIT_OPP_LINE_ITEM_UNIT_PRICE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_EDIT_PUBLIC_DOCUMENTS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_EDIT_READONLY_FIELDS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_EDIT_REPORTS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_EDIT_TASK;
    $fieldNameList[] = self::FIELD_PERMISSIONS_ENABLE_NOTIFICATIONS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_IMPORT_LEADS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_INSTALL_MULTIFORCE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_ANALYTIC_SNAPSHOTS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_BUSINESS_HOUR_HOLIDAYS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_CALL_CENTERS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_CASES;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_CATEGORIES;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_CSS_USERS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_CUSTOM_REPORT_TYPES;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_DASHBOARDS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_DATA_CATEGORIES;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_DATA_INTEGRATIONS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_DYNAMIC_DASHBOARDS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_EMAIL_CLIENT_CONFIG;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_LEADS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_MOBILE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_REMOTE_ACCESS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_SELF_SERVICE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_SOLUTIONS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MANAGE_USERS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_MODIFY_ALL_DATA;
    $fieldNameList[] = self::FIELD_PERMISSIONS_NEW_REPORT_BUILDER;
    $fieldNameList[] = self::FIELD_PERMISSIONS_PASSWORD_NEVER_EXPIRES;
    $fieldNameList[] = self::FIELD_PERMISSIONS_PUBLISH_MULTIFORCE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_RUN_REPORTS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_SCHEDULE_REPORTS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_SEND_SIT_REQUESTS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_SOLUTION_IMPORT;
    $fieldNameList[] = self::FIELD_PERMISSIONS_TRANSFER_ANY_CASE;
    $fieldNameList[] = self::FIELD_PERMISSIONS_TRANSFER_ANY_ENTITY;
    $fieldNameList[] = self::FIELD_PERMISSIONS_TRANSFER_ANY_LEAD;
    $fieldNameList[] = self::FIELD_PERMISSIONS_USE_TEAM_REASSIGN_WIZARDS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_VIEW_ALL_DATA;
    $fieldNameList[] = self::FIELD_PERMISSIONS_VIEW_DATA_CATEGORIES;
    $fieldNameList[] = self::FIELD_PERMISSIONS_VIEW_MY_TEAMS_DASHBOARDS;
    $fieldNameList[] = self::FIELD_PERMISSIONS_VIEW_SETUP;
    $fieldNameList[] = self::FIELD_USER_LICENSE_ID;
    $fieldNameList[] = self::FIELD_USER_TYPE;
    return($fieldNameList);
  } // getDefaultFieldNameList

} // CecSforceTableProfile
?>
