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
 * Resource Access Control
 */
class CRM_Crac_Resource
{

    /** @var integer resource ID */
    protected $entity_id;

    /** @var string resource table or type */
    protected $entity_table;

    /** @var int time to live in seconds */
    protected $ttl;


    public function __construct($entity_table, $entity_id, $ttl = 10)
    {
        $this->entity_id    = $entity_id;
        $this->entity_table = $entity_table;
        $this->ttl          = $ttl;
    }

    /**
     * Announce yourself as (still) using the resource
     *
     * @param $contact_id integer|null contact ID. Defaults to current user
     */
    public function ping($contact_id = null)
    {
        if (!$contact_id) {
            $contact_id = CRM_Core_Session::getLoggedInContactID();
        }

        // find timestamp
        $status = CRM_Core_DAO::singleValueQuery(
            "SELECT IF(timeout >= NOW(), 'ALIVE', 'TIMED_OUT') FROM civicrm_resource_access WHERE entity_id = %1 AND entity_table = %2 AND contact_id = %3",
            [
                1 => [$this->entity_id, 'Integer'],
                2 => [$this->entity_table, 'String'],
                3 => [$contact_id, 'Integer'],
            ]
        );

        if ($status == 'ALIVE') {
            // just update TIMEOUT
            CRM_Core_DAO::executeQuery(
                "UPDATE civicrm_resource_access SET timeout = (NOW() + INTERVAL %4 SECOND) WHERE entity_id = %1 AND entity_table = %2 AND contact_id = %3",
                [
                    1 => [$this->entity_id, 'Integer'],
                    2 => [$this->entity_table, 'String'],
                    3 => [$contact_id, 'Integer'],
                    4 => [$this->ttl, 'Integer'],
                ]
            );
        } elseif ($status == 'TIMED_OUT') {
            // start a new session (reset using_since)
            CRM_Core_DAO::executeQuery(
                "UPDATE civicrm_resource_access SET using_since = NOW(), timeout = (NOW() + INTERVAL %4 SECOND) WHERE entity_id = %1 AND entity_table = %2 AND contact_id = %3",
                [
                    1 => [$this->entity_id, 'Integer'],
                    2 => [$this->entity_table, 'String'],
                    3 => [$contact_id, 'Integer'],
                    4 => [$this->ttl, 'Integer'],
                ]
            );
        } else {
            // no entry: create a new one
            CRM_Core_DAO::executeQuery(
                "INSERT INTO civicrm_resource_access(entity_id,entity_table,contact_id,using_since,timeout) VALUES (%1, %2, %3, NOW(), (NOW() + INTERVAL %4 SECOND))",
                [
                    1 => [$this->entity_id, 'Integer'],
                    2 => [$this->entity_table, 'String'],
                    3 => [$contact_id, 'Integer'],
                    4 => [$this->ttl, 'Integer'],
                ]
            );
        }
    }

    /**
     * Get a structured list of users on this resource
     *
     * @param integer $exclude_contact_id
     *    contact ID of users to exclude from the list, most likely the current user
     *
     * @param string $access_type
     *    type of access, one of 'access', 'edit', 'use'
     *    will only be used for the generated text
     *
     * @return array
     *    data structure containing the usage data / warning
     */
    public function getUsers($exclude_contact_id = 0, $access_type = 'access')
    {
        // no entry: create a new one
        $query = CRM_Core_DAO::executeQuery(
            "SELECT r.contact_id         AS contact_id, 
                                                      r.using_since        AS using_since, 
                                                      contact.display_name AS display_name 
                                      FROM civicrm_resource_access r
                                      LEFT JOIN civicrm_contact contact ON contact.id = r.contact_id 
                                      WHERE entity_id = %1 AND entity_table = %2 AND timeout >= NOW();",
            [
                1 => [$this->entity_id, 'Integer'],
                2 => [$this->entity_table, 'String'],
            ]
        );

        $data = [];
        while ($query->fetch()) {
            $contact_id = (int)$query->contact_id;
            if ($contact_id == $exclude_contact_id) {
                continue;
            }
            $data[$contact_id] = [
                'contact_id'   => $query->contact_id,
                'using_since'  => $query->using_since,
                'display_name' => $query->display_name,
                'text'         => E::ts(
                    "'%1' <a href=\"%2\" target=\"_blank\">[%3]</a> since %4",
                    [
                        1 => $query->display_name,
                        2 => CRM_Utils_System::url("civicrm/contact/view", "reset=1&cid={$query->contact_id}"),
                        3 => $contact_id,
                        4 => CRM_Utils_Date::customFormat($query->using_since)
                    ]
                )
            ];
        }

        // calculate the string
        if (!empty($data)) {
            if (count($data) == 1) {
                switch ($access_type) {
                    case 'edit':
                        $data['html_text'] = E::ts(
                            "This resource is concurrently edited by contact %1",
                            [1 => reset($data)['text']]
                        );
                        break;

                    case 'use':
                        $data['html_text'] = E::ts(
                            "This resource is concurrently used by contact %1",
                            [1 => reset($data)['text']]
                        );
                        break;

                    default:
                    case 'access':
                        $data['html_text'] = E::ts(
                            "This resource is concurrently accessed by contact %1",
                            [1 => reset($data)['text']]
                        );
                        break;
                }
            } else {
                switch ($access_type) {
                    case 'edit':
                        $text = E::ts(
                            "This resource is concurrently edited by %1 other contacts:",
                            [1 => count($data)]
                        );
                        break;

                    case 'use':
                        $text = E::ts("This resource is concurrently used by %1 other contacts:", [1 => count($data)]);
                        break;

                    default:
                    case 'access':
                        $text = E::ts("This resource is concurrently accessed %1 other contacts:", [1 => count($data)]);
                        break;
                }
                $text .= "<ul>";
                foreach ($data as $contact_data) {
                    $text .= "<li>{$contact_data['text']}</li>";
                }
                $text              .= "</ul>";
                $data['html_text'] = $text;
            }

            return $data;
        }
    }
}
