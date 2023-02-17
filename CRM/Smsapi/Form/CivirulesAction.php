<?php

require_once 'CRM/Core/Form.php';

use CRM_Smsapi_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Smsapi_Form_CivirulesAction extends CRM_Core_Form {

  protected $ruleActionId = false;

  protected $ruleAction;

  protected $action;

  /**
   * Overridden parent method to do pre-form building processing
   *
   * @throws Exception when action or rule action not found
   * @access public
   */
  public function preProcess() {
    $this->ruleActionId = CRM_Utils_Request::retrieve('rule_action_id', 'Integer');

    $this->ruleAction = new CRM_Civirules_BAO_RuleAction();
    $this->action = new CRM_Civirules_BAO_Action();
    $this->ruleAction->id = $this->ruleActionId;
    if ($this->ruleAction->find(true)) {
      $this->action->id = $this->ruleAction->action_id;
      if (!$this->action->find(true)) {
        throw new Exception('CiviRules Could not find action with id '.$this->ruleAction->action_id);
      }
    } else {
      throw new Exception('CiviRules Could not find rule action with id '.$this->ruleActionId);
    }

    parent::preProcess();
  }

  /**
   * Method to get groups
   *
   * @return array
   * @access protected
   */
  protected function getMessageTemplates() {
    $messageTemplates = array();
    $query = 'SELECT id, msg_title FROM civicrm_msg_template WHERE is_active = %1 AND workflow_id IS NULL ORDER BY msg_title';
    $params = array(1 => array(1, 'Integer'));
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $messageTemplates[$dao->id] = $dao->msg_title;
    }
    $messageTemplates[0] = '- select -';
    asort($messageTemplates);
    return $messageTemplates;
  }

  /**
   * Method to get the SMS providers
   *
   * @return array $smsProviders
   * @access protected
   */
  protected function getSmsProviders() {
    $smsProviders = array();
    $query = 'SELECT id, title FROM civicrm_sms_provider WHERE is_active = %1';
    $params = array(1 => array(1, 'Integer'));
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $smsProviders[$dao->id] = $dao->title;
    }
    $smsProviders[0] = '- select -';
    asort($smsProviders);
    return $smsProviders;
  }

  function buildQuickForm() {

    $this->setFormTitle();

    $this->add('hidden', 'rule_action_id');
    $this->add('select', 'template_id', ts('Message template'), $this->getMessageTemplates(), true);
    $this->add('select', 'provider_id', ts('SMS Provider'), $this->getSmsProviders(), true);
    $this->addEntityRef('from_contact_id', ts('Message Sender'));
    $this->add('select', 'smarty', E::ts('Smarty'),
      [
        'use' => E::ts('Use Smarty'),
        'disable' => E::ts('Disable Smarty'),
        'settings' => E::ts('Use standard settings'),
      ]
    );
    $this->add('checkbox','alternative_receiver', ts('Send to alternative phone number'));
    $this->add('text', 'alternative_receiver_phone_number', ts('Send to'));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $data = array();
    $defaultValues = array();
    $defaultValues['rule_action_id'] = $this->ruleActionId;
    if (!empty($this->ruleAction->action_params)) {
      $data = unserialize($this->ruleAction->action_params);
    }
    if (!empty($data['provider_id'])) {
      $defaultValues['provider_id'] = $data['provider_id'];
    }
    if (!empty($data['template_id'])) {
      $defaultValues['template_id'] = $data['template_id'];
    }
    $defaultValues['from_contact_id'] = $data['from_contact_id'] ?? NULL;
    if (!empty($data['alternative_receiver_phone_number'])) {
      $defaultValues['alternative_receiver_phone_number'] = $data['alternative_receiver_phone_number'];
      $defaultValues['alternative_receiver'] = true;
    }
    if (!empty($data['smarty'])) {
      $defaultValues['smarty'] = $data['smarty'];
    } else {
      $defaultValues['smarty'] = 'settings';
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submitting
   *
   * @access public
   */
  public function postProcess() {
    $data['provider_id'] = $this->_submitValues['provider_id'];
    $data['template_id'] = $this->_submitValues['template_id'];
    $data['from_contact_id'] = $this->_submitValues['from_contact_id'] ?? CRM_Core_Session::getLoggedInContactID() ?? NULL;
    $data['alternative_receiver_phone_number'] = '';
    if (!empty($this->_submitValues['alternative_receiver_phone_number'])) {
      $data['alternative_receiver_phone_number'] = $this->_submitValues['alternative_receiver_phone_number'];
    }
    $data['smarty'] = $this->_submitValues['smarty'] ?? 'settings';

    $ruleAction = new CRM_Civirules_BAO_RuleAction();
    $ruleAction->id = $this->ruleActionId;
    $ruleAction->action_params = serialize($data);
    $ruleAction->save();

    $session = CRM_Core_Session::singleton();
    $session->setStatus('Action '.$this->action->label.' parameters updated to CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleAction->rule_id),
      'Action parameters updated', 'success');

    $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->ruleAction->rule_id, TRUE);
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Method to set the form title
   *
   * @access protected
   */
  protected function setFormTitle() {
    $title = 'CiviRules Edit Send SMS Action parameters';
    $this->assign('ruleActionHeader', 'Edit action '.$this->action->label.' of CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleAction->rule_id));
    CRM_Utils_System::setTitle($title);
  }
}
