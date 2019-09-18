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

use CRM_Crac_ExtensionUtil as E;

/**
 * Tools to inject JS monitors into civicrm pages
 */
class CRM_Crac_Monitor {

  /**
   * Add a simple JS monitor for the given resource
   *
   * @param $entity_id      integer resource ID
   * @param $entity_table   string  resource type
   * @param $interval       integer ping interval in milliseconds
   */
  public static function injectSimpleMonitor($entity_id, $entity_table, $interval = 5000) {
    self::injectMonitor("return {entity_id:{$entity_id}, entity_table:'{$entity_table}', interval: {$interval}};");
  }

  /**
   * Add a simple JS monitor for the given resource
   *
   * @param $resource_snippet string JS code the generates a structure
   */
  public static function injectMonitor($resource_snippet) {
    $monitor_code = file_get_contents(E::path('js/simple_monitor.js'));
    $monitor_code = preg_replace('/RESOURCE_OBJECT/', $resource_snippet, $monitor_code);
    Civi::resources()->addScript($monitor_code);
  }

  public static function buildFormHook($formName, $form) {
    if ($formName == 'CRM_Contribute_Form_Contribution') {
      // todo: configurable
      self::injectSimpleMonitor($form->_id, 'civicrm_contribution');
    }
  }
}
