<h3>{$ruleActionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block-sms-send">
  <div class="crm-section">
    <div class="label">{$form.provider_id.label}</div>
    <div class="content">{$form.provider_id.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.template_id.label}</div>
    <div class="content">{$form.template_id.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.alternative_receiver.label}</div>
    <div class="content">{$form.alternative_receiver.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section hiddenElement alternative_receiver_phone_number">
    <div class="label">{$form.alternative_receiver_phone_number.label}</div>
    <div class="content">{$form.alternative_receiver_phone_number.html}</div>
    <div class="clear"></div>
  </div>
</div>
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
  <script type="text/javascript">
    cj(function() {
      cj('#alternative_receiver').change(triggerAlternativeReceiverChange);

      triggerAlternativeReceiverChange();
    });

    function triggerAlternativeReceiverChange() {
      cj('.crm-section.alternative_receiver_phone_number').addClass('hiddenElement');
      var val = cj('#alternative_receiver').prop('checked');
      if (val) {
        cj('.crm-section.alternative_receiver_phone_number').removeClass('hiddenElement');
      }
    }
  </script>

{/literal}