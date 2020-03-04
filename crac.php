<?php
/*-------------------------------------------------------+
| Concurrent Resource Access Control (CRAC)              |
| Copyright (C) 2019 SYSTOPIA                            |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/


require_once 'crac.civix.php';
use CRM_Crac_ExtensionUtil as E;



/**
 * Inject angular based monitors
 *
 * @param $angular \Civi\Angular\Manager
 */
function crac_civicrm_alterAngular(&$angular) {
  $angular_modules = $angular->getModules();
  if (isset($angular_modules['crmMosaico'])) {
    CRM_Crac_Monitor::injectMosaicoMailMonitor();
  }
}

/**
 * Don't log CRAC data
 */
function crac_civicrm_alterLogTables(&$logTableSpec) {
  // disable logging for civicrm_resource_access
  if (isset($logTableSpec['civicrm_resource_access'])) {
    unset($logTableSpec['civicrm_resource_access']);
  }
}

/**
 * CRAC for everyone
 */
function crac_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['crac']['ping'] = ['access CiviCRM'];
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function crac_civicrm_config(&$config) {
  _crac_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function crac_civicrm_xmlMenu(&$files) {
  _crac_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function crac_civicrm_install() {
  _crac_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function crac_civicrm_postInstall() {
  _crac_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function crac_civicrm_uninstall() {
  _crac_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function crac_civicrm_enable() {
  _crac_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function crac_civicrm_disable() {
  _crac_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function crac_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _crac_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function crac_civicrm_managed(&$entities) {
  _crac_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function crac_civicrm_caseTypes(&$caseTypes) {
  _crac_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function crac_civicrm_angularModules(&$angularModules) {
  _crac_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function crac_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _crac_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function crac_civicrm_entityTypes(&$entityTypes) {
  _crac_civix_civicrm_entityTypes($entityTypes);
}

