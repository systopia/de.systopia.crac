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
   * Inject Mosaico Mail monitor
   */
  public static function injectMosaicoMailMonitor() {
    // make sure this is only executed once
    static $injection_tried = false;
    if ($injection_tried) {
      return;
    } else {
      $injection_tried = true;
    }

    $interval = (int) Civi::settings()->get('crac_mosaico_mailing_monitor');
    if ($interval) { // interval 0 means 'disabled'
      // inject resources
      $ingore_option = (boolean) Civi::settings()->get('crac_ignore_option');
      CRM_Core_Resources::singleton()->addVars('CracMosaicoMonitor', [
          'interval'        => $interval,
          'dialogue_title'  => E::ts("Concurrent Edit Detected!"),
          'dialogue_check'  => E::ts("Check Again"),
          'dialogue_abort'  => E::ts("Go Back"),
          'dialogue_ignore' => $ingore_option ? E::ts("Ignore") : '',
          'ignore_title'    => E::ts("Concurrent Editing"),
          'ignore_text'     => E::ts("This Mailing is currently edited multiple times. If multiple people will save their changes, some of it <strong>will be lost</strong>. Hope you know what you're doing."),
      ]);
      CRM_Core_Resources::singleton()->addScriptFile('de.systopia.crac', 'js/mosaico_monitor.js');
    }
  }

  /**
   * Add a simple JS monitor for the given resource
   *
   * @param $resource_snippet string JS code the generates a structure
   *
   * @deprecated
   */
  public static function injectMonitor($resource_snippet) {
    $monitor_code = file_get_contents(E::path('js/simple_monitor.js'));
    $monitor_code = preg_replace('/RESOURCE_OBJECT/', $resource_snippet, $monitor_code);
    Civi::resources()->addScript($monitor_code);
  }

  /**
   * Triggered by buildForm hook, injects configured monitors
   *
   * @param $formName string name of the form
   * @param $form     CRM_Core_Form form object
   */
  public static function injectMonitors($formName, $form) {
    // TODO: inject monitors?
  }
}
