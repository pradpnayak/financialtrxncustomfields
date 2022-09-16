<?php

require_once 'financialtrxncustomfields.civix.php';
// phpcs:disable
use CRM_Financialtrxncustomfields_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function financialtrxncustomfields_civicrm_config(&$config) {
  _financialtrxncustomfields_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function financialtrxncustomfields_civicrm_install() {
  _financialtrxncustomfields_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function financialtrxncustomfields_civicrm_postInstall() {
  _financialtrxncustomfields_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function financialtrxncustomfields_civicrm_uninstall() {
  _financialtrxncustomfields_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function financialtrxncustomfields_civicrm_enable() {
  _financialtrxncustomfields_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function financialtrxncustomfields_civicrm_disable() {
  _financialtrxncustomfields_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function financialtrxncustomfields_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _financialtrxncustomfields_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function financialtrxncustomfields_civicrm_entityTypes(&$entityTypes) {
  _financialtrxncustomfields_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_alterSettingsMetaData().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsMetaData
 */
function financialtrxncustomfields_civicrm_alterSettingsMetaData(&$settingsMetadata, $domainID, $profile) {
  $settingsMetadata['financialtrxncustomfields_delete_payment'] = [
    'group_name' => CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME,
    'group' => 'financialtrxncustomfields',
    'name' => 'financialtrxncustomfields_delete_payment',
    'type' => 'Boolean',
    'quick_form_type' => 'Element',
    'html_type' => 'radio',
    'default' => 0,
    'add' => '5.35',
    'title' => ts('Allow Delete Payments?'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
  ];
}

/**
 * Implements hook_civicrm_managed().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function financialtrxncustomfields_civicrm_managed(&$entities) {
  $entities[] = [
    'module' => 'financialtrxncustomfields',
    'name' => 'financialtrxncustomfields_cgeo',
    'update' => 'never',
    'entity' => 'OptionValue',
    'params' => [
      'label' => ts('Payments'),
      'name' => 'civicrm_financial_trxn',
      'value' => 'FinancialTrxn',
      'option_group_id' => 'cg_extend_objects',
      'is_active' => 1,
      'version' => 3,
      'options' => ['match' => ['option_group_id', 'name']],
    ],
  ];
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_buildForm
 */
function financialtrxncustomfields_civicrm_buildForm($formName, &$form) {
  if ('CRM_Financial_Form_PaymentEdit' == $formName) {
    CRM_FinancialTrxnCustomFields_Utils::$_fID = [
      $form->getVar('_id') => $form->getVar('_id'),
    ];

    $form->assign('customDataType', 'FinancialTrxn');
    $form->assign('entityID', $form->getVar('_id'));

    CRM_Core_Region::instance('payment-edit-block')->add([
      'template' => 'CRM/common/customDataBlock.tpl',
    ]);
  }

  if ('CRM_Admin_Form_Preferences_Contribute' == $formName) {
    $form->addYesNo('financialtrxncustomfields_delete_payment', ts('Allow Delete Payments?'));
    $form->assign('fields', $form->getVar('settingsMetadata'));
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postProcess
 */
function financialtrxncustomfields_civicrm_postProcess($formName, &$form) {
  if ('CRM_Financial_Form_PaymentEdit' == $formName) {
    CRM_FinancialTrxnCustomFields_Utils::processCustomFields($form->_submitValues);
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postProcess
 */
function financialtrxncustomfields_civicrm_postSave_civicrm_financial_trxn($dao) {
  CRM_FinancialTrxnCustomFields_Utils::$_fID[$dao->id] = $dao->id;
}

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 */
function financialtrxncustomfields_civicrm_preProcess($formName, &$form) {
  if ('CRM_Admin_Form_Preferences_Contribute' == $formName) {
    $vars = $form->getVar('_settings');
    $vars['financialtrxncustomfields_delete_payment'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
    $form->setVar('_settings', $vars);
    $form->setVar('settingsMetadata', '');
  }
}

/**
 * Implements hook_civicrm_permission().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_permission
 */
function financialtrxncustomfields_civicrm_permission(&$permissions) {
  $permissions['delete financial payments'] = [
    E::ts('Payments: Delete Payments'),
    E::ts('Warning: Give to trusted roles only; Permission to delete financial payments. '),
  ];
}
