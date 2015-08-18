<?php

/**
 * Sms.Send API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_sms_send_spec(&$spec) {
  $spec['contact_id']['api.required'] = 1;
  $spec['template_id']['api.required'] = 1;
  $spec['provider_id']['api.required'] = 1;
}

/**
 * Sms.Send API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sms_send($params) {
  $version = CRM_Core_BAO_Domain::version();
  if (!preg_match('/[0-9]+(,[0-9]+)*/i', $params['contact_id'])) {
    throw new API_Exception('Parameter contact_id must be a unique id or a list of ids separated by comma');
  }
  $contactIds = explode(",", $params['contact_id']);

  // Compatibility with CiviCRM > 4.3
  if($version >= 4.4) {
    $messageTemplates = new CRM_Core_DAO_MessageTemplate();
  } else {
    $messageTemplates = new CRM_Core_DAO_MessageTemplates();
  }
  $messageTemplates->id = $params['template_id'];
  
  if (!$messageTemplates->find(TRUE)) {
    throw new API_Exception('Could not find template with ID: '.$params['template_id']);
  }
  
  foreach($contactIds as $contactId) {
    $returnProperties = array(
        'sort_name' => 1,
        'phone' => 1,
        'do_not_sms' => 1,
        'is_deceased' => 1,
        'display_name' => 1,
    );
    list($contactDetails) = CRM_Utils_Token::getTokenDetails(array($contactId), $returnProperties, FALSE, FALSE);

    //to check if the phone type is "Mobile"
    $phoneTypes = CRM_Core_OptionGroup::values('phone_type', TRUE, FALSE, FALSE, NULL, 'name');
    $phone = CRM_Core_DAO::executeQuery('SELECT * FROM civicrm_phone WHERE phone_type_id = %1 AND contact_id = %2', array(
        1 => array(CRM_Utils_Array::value('Mobile', $phoneTypes), 'Integer'),
        2 => array($contactId, 'Integer'),
    ));
    if (!$phone->fetch()) {
      throw new API_Exception('Suppressed sending sms to: ' . $contactDetails[$contactId]['display_name']);
    }
    $contactDetails[$contactId]['phone_id'] = $phone->id;
    $contactDetails[$contactId]['phone'] = $phone->phone;
    $contactDetails[$contactId]['phone_type_id'] = CRM_Utils_Array::value('Mobile', $phoneTypes);

    $activityParams['html_message'] = $messageTemplates->msg_html;
    $activityParams['text_message'] = $messageTemplates->msg_text;
    $activityParams['sms_text_message'] = $messageTemplates->msg_text;
    $activityParams['activity_subject'] = $messageTemplates->msg_subject;
    $smsParams['provider_id'] = $params['provider_id'];

    $from_contact_id = null;
    if (isset($params['from_contact_id'])) {
      $from_contact_id = $params['from_contact_id'];
    }

    $contactIds = array($contactId);
    $return = CRM_Activity_BAO_Activity::sendSMS($contactDetails, $activityParams, $smsParams, $contactIds, $from_contact_id);
  }
  $returnValues = array();
  return civicrm_api3_create_success($returnValues, $params, 'Sms', 'Send');
}
