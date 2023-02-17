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
  if (!CRM_Utils_Type::validate($params['contact_id'], 'CommaSeparatedIntegers')) {
    throw new API_Exception('Parameter contact_id must be a unique id or a list of ids separated by comma');
  }
  $contactIds = explode(",", $params['contact_id']);
  $alternativePhoneNumber = !empty($params['alternative_receiver_phone_number']) ? $params['alternative_receiver_phone_number'] : false;

  $messageTemplates = new CRM_Core_DAO_MessageTemplate();
  $messageTemplates->id = $params['template_id'];

  if (!$messageTemplates->find(TRUE)) {
    throw new API_Exception('Could not find template with ID: '.$params['template_id']);
  }

  foreach($contactIds as $contactId) {
    $contactDetails[$contactId]['contact_id'] = $contactId;

    //to check if the phone type is "Mobile"
    $phoneTypes = CRM_Core_OptionGroup::values('phone_type', TRUE, FALSE, FALSE, NULL, 'name');
    if ($alternativePhoneNumber) {
      $contactDetails[$contactId]['phone_id'] = 0;
      $contactDetails[$contactId]['phone'] = $alternativePhoneNumber;
    } else {
      $phone = CRM_Core_DAO::executeQuery('SELECT * FROM civicrm_phone WHERE phone_type_id = %1 AND contact_id = %2', array(
        1 => array(CRM_Utils_Array::value('Mobile', $phoneTypes), 'Integer'),
        2 => array($contactId, 'Integer'),
      ));
      if (!$phone->fetch()) {
        throw new API_Exception('Suppressed sending sms to: ' . $contactDetails[$contactId]['display_name']);
      }
      $contactDetails[$contactId]['phone_id'] = $phone->id;
      $contactDetails[$contactId]['phone'] = $phone->phone;
    }
    $contactDetails[$contactId]['phone_type_id'] = CRM_Utils_Array::value('Mobile', $phoneTypes);
    $message['messageSubject'] = (empty($params['subject']) ? $messageTemplates->msg_subject : $params['subject']);
    $message['text'] = $messageTemplates->msg_text ?? CRM_Utils_String::htmlToText($messageTemplates->msg_html);
    $message['html'] = $messageTemplates->msg_html;
    $message_params = $params;
    $message_params['contact_id'] = $contactId;
    ['messageSubject' => $messageSubject, 'html' => $html, 'text' => $text] = CRM_Smsapi_Utils_Tokens::replaceTokens($contactId, $message, $message_params);

    $activityParams['html_message'] = $html;
    $activityParams['text_message'] = $text;
    $activityParams['sms_text_message'] = $text;
    $activityParams['activity_subject'] = $messageSubject;
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
