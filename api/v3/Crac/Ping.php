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

/**
 * Ping CRAC that you're using the given resource
 * @see https://projekte.systopia.de/redmine/issues/7245
 */
function civicrm_api3_crac_ping($params) {
  $resource = new CRM_Crac_Resource($params['entity_table'], $params['entity_id'], $params['ttl']);
  $resource->ping(CRM_Utils_Array::value('contact_id', $params, NULL));
  $uses = $resource->getUsers(CRM_Utils_Array::value('contact_id', $params, CRM_Core_Session::getLoggedInContactID()));
  return civicrm_api3_create_success($uses);
}

/**
 * Adjust Metadata for Crac.ping
 */
function _civicrm_api3_crac_ping_spec(&$params) {
  $params['entity_table'] = array(
      'name'         => 'entity_table',
      'api.required' => 1,
      'title'        => 'Resource type',
      'description'  => 'entity table or any other string identifying the type',
  );
  $params['entity_id'] = array(
      'name'         => 'entity_id',
      'api.required' => 1,
      'title'        => 'Resource ID',
      'description'  => 'ID of the resource being used',
  );
  $params['ttl'] = array(
      'name'         => 'ttl',
      'api.default'  => 10,
      'title'        => 'TTL (in seconds)',
      'description'  => 'ID of the resource being used',
  );
  $params['contact_id'] = array(
      'name'         => 'contact_id',
      'api.required' => 0,
      'title'        => 'User Contact ID',
      'description'  => 'Will default to the logged in contact_id',
  );
}

