/**------------------------------------------------------+
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
+-------------------------------------------------------*/


/**
 * function to return an object with entity_id a entity_table properties defining the resource spec
 */
function get_crac_resource() {
    /* this will be replaced by JS code for the function */
    RESOURCE_OBJECT
}

/**
 * execute a resource ping and show a warning if there is an issue
 */
var alert_dialogue = null;
function crac_ping() {
    let resource = get_crac_resource();
    CRM.api3('Crac', 'ping', resource)
        .done(function(result) {
            if (result.count > 0) {
                if (alert_dialogue) {
                    // how to update?
                    console.log("how to update?");
                } else {
                    alert_dialogue = CRM.alert(result.values.html_text, "Concurrent Resource Use!", 'warm', {expires: false});
                }
            } else {
                if (alert_dialogue) {
                    alert_dialogue.alert('close');
                    alert_dialogue = null;
                }
            }
        });
}

cj(document).ready(function() {
    let resource = get_crac_resource();
    let ping_loop = setInterval(crac_ping, resource.interval);
    crac_ping();
});