<?php
use CRM_Crac_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Crac_Upgrader extends CRM_Crac_Upgrader_Base {

  /**
   * Create data structure
   */
  public function install() {
    $this->executeSqlFile('sql/install.sql');
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled.
   */
  public function uninstall() {
   $this->executeSqlFile('sql/uninstall.sql');
  }

}
