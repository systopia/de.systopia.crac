/**------------------------------------------------------+
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
+-------------------------------------------------------*/

/**
 * the currently shown modal warning dialogue
 */
let mosaico_crac_dialogue = null;

/**
 * the currently shown modal warning dialogue
 */
let mosaico_ping_loop = null;

/**
 * execute a resource ping and show a warning if there is an issue
 *
 * remark: we have to keep the ping going even if the dialogue is up,
 *   since the ping also reports _us_ using the resource
 */
function crac_mosaico_ping() {
    // check if we're editing a mailing by investigating the URL
    let match = document.location.href.match(/civicrm\/a\/#\/mailing\/(?<mailing_id>[0-9]+)/);
    if (match) {
        // we're currently editing a mailing -> run a ping
        let crac_ping = {
            entity_id: match.groups.mailing_id,
            entity_table: 'civicrm_mailing',
            'interval': CRM.vars.CracMosaicoMonitor.interval
        };

        CRM.api3('Crac', 'ping', crac_ping)
            .done(function (result) {
                if (result.count > 0) {
                    // there is a resource conflict!
                    if (mosaico_crac_dialogue == null) {
                        // there is no dialogue yet => create one!
                        mosaico_crac_dialogue = CRM.confirm({
                            title: CRM.vars.CracMosaicoMonitor.dialogue_title,
                            resizable: false,
                            message: '<div class="crm-custom-image-popup">' + result.values.html_text + '</div>',
                            options: {
                                check: CRM.vars.CracMosaicoMonitor.dialogue_check,
                                abort: CRM.vars.CracMosaicoMonitor.dialogue_abort,
                                no: CRM.vars.CracMosaicoMonitor.dialogue_ignore},

                        }).on('crmConfirm:check', function() {
                            // user picked 'Check Again' => close dialogue and run ping
                            mosaico_crac_dialogue = null;
                            crac_mosaico_ping();

                        }).on('crmConfirm:abort', function() {
                            // user picked 'Abort' => navigate back
                            if (window.history.length > 2) {
                                window.history.back();
                            } else {
                                window.location.href = CRM.url('civicrm/dashboard');
                            }

                        }).on('crmConfirm:no', function() {
                            // user picked 'Ignore' => close dialogue and stop timer
                            mosaico_crac_dialogue = null;
                            clearInterval(mosaico_ping_loop);
                            CRM.alert(
                                CRM.vars.CracMosaicoMonitor.ignore_text,
                                CRM.vars.CracMosaicoMonitor.ignore_title,
                                "warn"
                                );
                        });
                    }
                }
            });
    }
}

cj(document).ready(function() {
    mosaico_ping_loop = setInterval(crac_mosaico_ping, CRM.vars.CracMosaicoMonitor.interval);
    crac_mosaico_ping();
});