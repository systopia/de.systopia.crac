{*-------------------------------------------------------+
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
+-------------------------------------------------------*}

{crmScope extensionKey='de.systopia.crac'}
  <div class="crm-block crm-form-block crm-dbmonitor-form-block">
    <br/>
    <div class="crm-section">
      <div class="label">{$form.crac_ttl.label}</div>
      <div class="content">{$form.crac_ttl.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.crac_ignore_option.label}</div>
      <div class="content">{$form.crac_ignore_option.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.crac_mosaico_mailing_monitor.label}</div>
      <div class="content">{$form.crac_mosaico_mailing_monitor.html}</div>
      <div class="clear"></div>
    </div>
    <br/>
  </div>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>

{/crmScope}