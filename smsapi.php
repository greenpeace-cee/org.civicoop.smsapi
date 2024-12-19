<?php

require_once 'smsapi.civix.php';
// phpcs:disable
use CRM_Smsapi_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function smsapi_civicrm_config(&$config) {
  _smsapi_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function smsapi_civicrm_install() {
  _smsapi_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function smsapi_civicrm_enable() {
  _smsapi_civix_civicrm_enable();
}

/**
 * Function for CiviRules, check if CiviRules is installed
 *
 * @return bool
 */
function _smsapi_is_civirules_installed() {
  $installed = FALSE;
  try {
    $extensions = civicrm_api3('Extension', 'get', array('options' => array('limit' => 0)));
    foreach ($extensions['values'] as $ext) {
      if ($ext['key'] == 'org.civicoop.civirules' && $ext['status'] == 'installed') {
        $installed = TRUE;
      }
    }
  }
  catch (Exception $e) {
    $installed = FALSE;
  }
  return $installed;
}
