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
function crac_civicrm_alterAngular(&$angular)
{
//    $angular_modules = $angular->getModules();
//    if (isset($angular_modules['crmMosaico'])) {
//        CRM_Crac_Monitor::injectMosaicoMailMonitor();
//    }
}

/**
 * Don't log CRAC data
 */
function crac_civicrm_alterLogTables(&$logTableSpec)
{
    // disable logging for civicrm_resource_access
    if (isset($logTableSpec['civicrm_resource_access'])) {
        unset($logTableSpec['civicrm_resource_access']);
    }
}

/**
 * CRAC for everyone
 */
function crac_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions)
{
    $permissions['crac']['ping'] = ['access CiviCRM'];
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function crac_civicrm_config(&$config)
{
    _crac_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function crac_civicrm_install()
{
    _crac_civix_civicrm_install();
}

function crac_civicrm_pageRun($page): void {
    if (CRM_Crac_Monitor::mosaicoMonitorNeeded($page)){
        CRM_Crac_Monitor::injectMosaicoMailMonitor();
    }
}
/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function crac_civicrm_enable()
{
    _crac_civix_civicrm_enable();
}
