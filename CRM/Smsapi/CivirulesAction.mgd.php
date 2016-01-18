<?php

if (_smsapi_is_civirules_installed()) {
  return array (
    0 =>
      array (
        'name' => 'Civirules:Action.Smsapi',
        'entity' => 'CiviRuleAction',
        'params' =>
          array (
            'version' => 3,
            'name' => 'smsapi_send',
            'label' => 'Send SMS',
            'class_name' => 'CRM_Smsapi_CivirulesAction',
            'is_active' => 1
          ),
      ),
  );
} else {
  return array();
}
