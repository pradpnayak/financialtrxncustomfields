<?php

class CRM_FinancialTrxnCustomFields_Utils {

  public static $_fID = NULL;

  public static function processCustomFields(array $submittedValues) {
    if (empty(CRM_FinancialTrxnCustomFields_Utils::$_fID)) {
      return;
    }
    $customParams = self::getOnlyCustomParams($submittedValues);
    if (!empty($customParams)) {
      $fIds = CRM_FinancialTrxnCustomFields_Utils::$_fID;
      $customParams['id'] = array_pop($fIds);
      civicrm_api3('FinancialTrxn', 'create', $customParams);
      if (count($fIds) == 2) {
        self::copyNegateCustomData($fIds);
      }
    }
  }

  private function copyNegateCustomData($fIds) {
    $negateFtId = array_pop($fIds);
    $oldFtId = array_pop($fIds);
    try {
      $customParams = civicrm_api3('FinancialTrxn', 'getsingle', [
        'id' => $oldFtId,
      ]);
      $customParams = self::getOnlyCustomParams($customParams);
      $customParams['id'] = $negateFtId;
      civicrm_api3('FinancialTrxn', 'create', $customParams);
    }
    catch (Exception $e) {
    }
  }

  private static function getOnlyCustomParams(array $params): array {
    $customParams = [];

    foreach ($params as $k => $v) {
      if (strpos($k, 'custom_') !== 0) {
        continue;
      }
      $customFieldId = CRM_Core_BAO_CustomField::getKeyID($k);
      if (is_array($v)) {
        $v = array_filter($v);
      }
      $customParams['custom_' . $customFieldId] = $v;

    }
    return $customParams;
  }

}
