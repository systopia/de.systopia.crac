<?php
/*-------------------------------------------------------+
| Concurrent Resource Access Control (CRAC)              |
| Copyright (C) 2020 SYSTOPIA                            |
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
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Crac_Form_Settings extends CRM_Core_Form
{
    public function buildQuickForm()
    {
        $ping_rates = [
            '0'     => E::ts("disabled"),
            '1000'  => E::ts("every second"),
            '2000'  => E::ts("every %1 seconds", [1 => 2]),
            '5000'  => E::ts("every %1 seconds", [1 => 5]),
            '10000' => E::ts("every %1 seconds", [1 => 10]),
        ];

        $ttl_options = [
            '1'  => E::ts("1 second"),
            '2'  => E::ts("%1 seconds", [1 => 2]),
            '5'  => E::ts("%1 seconds", [1 => 5]),
            '10' => E::ts("%1 seconds", [1 => 10]),
        ];


        $this->add(
            'select',
            'crac_ttl',
            E::ts('Tolerance (TTL)'),
            $ttl_options,
            true
        );

        $this->add(
            'checkbox',
            'crac_ignore_option',
            E::ts('Allow "ignore" option')
        );

        $this->add(
            'select',
            'crac_mosaico_mailing_monitor',
            E::ts('Mailing Editor (Mosaico)'),
            $ping_rates,
            true
        );

        $this->addButtons(
            [
                [
                    'type'      => 'submit',
                    'name'      => E::ts('Save'),
                    'isDefault' => true,
                ]
            ]
        );

        // set defaults
        $this->setDefaults(
            [
                'crac_ignore_option'           => (int)Civi::settings()->get('crac_ignore_option'),
                'crac_ttl'                     => (int)Civi::settings()->get('crac_ttl'),
                'crac_mosaico_mailing_monitor' => (int)Civi::settings()->get('crac_mosaico_mailing_monitor'),
            ]
        );

        parent::buildQuickForm();
    }

    public function postProcess()
    {
        $values = $this->exportValues();

        // set values
        Civi::settings()->set('crac_ignore_option', CRM_Utils_Array::value('crac_ignore_option', $values, 0));
        Civi::settings()->set('crac_ttl', CRM_Utils_Array::value('crac_ttl', $values, 0));
        Civi::settings()->set(
            'crac_mosaico_mailing_monitor',
            CRM_Utils_Array::value('crac_mosaico_mailing_monitor', $values, 0)
        );

        parent::postProcess();
    }
}