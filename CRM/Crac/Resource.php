<?php
use CRM_Crac_ExtensionUtil as E;

/**
 * Resource Access Control
 */
class CRM_Crac_Resource {

  /** @var integer resource ID */
  protected $entity_id;

  /** @var string resource table or type */
  protected $entity_table;

  /** @var int time to live in seconds */
  protected $ttl;


  public function __construct($entity_table, $entity_id, $ttl = 10) {
    $this->entity_id = $entity_id;
    $this->entity_table = $entity_table;
    $this->ttl = $ttl;
  }

  /**
   * Announce yourself as (still) using the resource
   *
   * @param $contact_id integer|null contact ID. Defaults to current user
   */
  public function ping($contact_id = NULL) {
    if (!$contact_id) {
      $contact_id = CRM_Core_Session::getLoggedInContactID();
    }

    // find timestamp
    $status = CRM_Core_DAO::singleValueQuery("SELECT IF(timeout >= NOW(), 'ALIVE', 'TIMED_OUT') FROM civicrm_resource_access WHERE entity_id = %1 AND entity_table = %2 AND contact_id = %3", [
        1 => [$this->entity_id,    'Integer'],
        2 => [$this->entity_table, 'String'],
        3 => [$contact_id,         'Integer'],
    ]);

    if ($status == 'ALIVE') {
      // just update TIMEOUT
      CRM_Core_DAO::executeQuery("UPDATE civicrm_resource_access SET timestamp = (NOW() + INTERVAL %4 SECOND) WHERE entity_id = %1 AND entity_table = %2 AND contact_id = %3", [
          1 => [$this->entity_id,    'Integer'],
          2 => [$this->entity_table, 'String'],
          3 => [$contact_id,         'Integer'],
          4 => [$this->ttl,          'Integer'],
      ]);

    } elseif ($status == 'TIMED_OUT') {
      // start a new session (reset using_since)
      CRM_Core_DAO::executeQuery("UPDATE civicrm_resource_access SET using_since = NOW(), timestamp = (NOW() + INTERVAL %4 SECOND) WHERE entity_id = %1 AND entity_table = %2 AND contact_id = %3", [
          1 => [$this->entity_id,    'Integer'],
          2 => [$this->entity_table, 'String'],
          3 => [$contact_id,         'Integer'],
          4 => [$this->ttl,          'Integer'],
      ]);

    } else {
      // no entry: create a new one
      CRM_Core_DAO::executeQuery("INSERT INTO civicrm_resource_access(entity_id,entity_table,contact_id,using_since,timeout) VALUES (%1, %2, %3, NOW(), (NOW() + INTERVAL %4 SECOND))", [
          1 => [$this->entity_id,    'Integer'],
          2 => [$this->entity_table, 'String'],
          3 => [$contact_id,         'Integer'],
          4 => [$this->ttl,          'Integer'],
      ]);
    }
  }

  /**
   *
   * @param bool $exclude_contact_id
   */
  public function getUsers($exclude_contact_id = FALSE) {
    // no entry: create a new one
    CRM_Core_DAO::executeQuery("SELECT contact_id, using_since, contact.display_name 
                                      FROM civicrm_resource_access
                                      LEFT JOIN civicrm_contact contact.id = contact_id 
                                      WHERE entity_id = %1 AND entity_table = %2 AND timeout >= NOW();", [
        1 => [$this->entity_id,    'Integer'],
        2 => [$this->entity_table, 'String'],
    ]);

    // TODO:
  }
}
