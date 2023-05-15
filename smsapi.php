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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function smsapi_civicrm_xmlMenu(&$files) {
  _smsapi_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function smsapi_civicrm_postInstall() {
  _smsapi_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function smsapi_civicrm_uninstall() {
  _smsapi_civix_civicrm_uninstall();
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
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function smsapi_civicrm_disable() {
  _smsapi_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function smsapi_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _smsapi_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function smsapi_civicrm_managed(&$entities) {
  _smsapi_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function smsapi_civicrm_caseTypes(&$caseTypes) {
  _smsapi_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function smsapi_civicrm_angularModules(&$angularModules) {
  _smsapi_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function smsapi_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _smsapi_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function smsapi_civicrm_entityTypes(&$entityTypes) {
  _smsapi_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function smsapi_civicrm_themes(&$themes) {
  _smsapi_civix_civicrm_themes($themes);
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
