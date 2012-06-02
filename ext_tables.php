<?php
if (!defined('TYPO3_MODE')) {
  die ('Access denied.');
}
$tempColumns = array (
  'tx_t3registration_privacy' => array (
    'exclude' => 1,
    'label' => 'LLL:EXT:t3registration/locallang_db.xml:fe_users.tx_t3registration_privacy',
    'config' => array (
      'type' => 'check',
    )
  ),
  'tx_t3registration_marketing_permission' => array (
    'exclude' => 1,
    'label' => 'LLL:EXT:t3registration/locallang_db.xml:fe_users.tx_t3registration_marketing_permission',
    'config' => array (
      'type' => 'check',
    )
  ),
  'user_auth_code' => array (
    'exclude' => 1,
    'label' => 'LLL:EXT:t3registration/locallang_db.xml:fe_users.userAuthCode',
    'config' => array (
      'type' => 'input',
      'maxchar' => 255
    )
  ),
  'admin_auth_code' => array (
    'exclude' => 1,
    'label' => 'LLL:EXT:t3registration/locallang_db.xml:fe_users.adminAuthCode',
    'config' => array (
      'type' => 'input',
      'maxchar' => 255
    )
  ),
  'admin_disable' => array (
    'exclude' => 1,
    'label' => 'LLL:EXT:t3registration/locallang_db.xml:fe_users.adminDisable',
    'config' => array (
      'type' => 'check',
    )
  )
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_t3registration_t3registration_privacy;;;;1-1-1, tx_t3registration_t3registration_marketing_permission,user_auth_code,admin_auth_code,admin_disable');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';


t3lib_extMgm::addPlugin(array(
  'LLL:EXT:t3registration/locallang_db.xml:tt_content.list_type_pi1',
  $_EXTKEY . '_pi1',
  t3lib_extMgm::extRelPath($_EXTKEY) . 't3registration.gif'
),'list_type');


include_once(t3lib_extMgm::extPath($_EXTKEY).'library/class.tx_t3registration_getFeUsersColumnNames.php');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';

    t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1','FILE:EXT:' . $_EXTKEY . '/flexform.xml');


if (TYPO3_MODE == 'BE') {
  $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_t3registration_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_t3registration_pi1_wizicon.php';
}

t3lib_extMgm::addPageTSConfig('
#disabled t3registration special fields from TCEFORMS visualization
TCEFORM.fe_users.admin_auth_code.disabled=1
TCEFORM.fe_users.admin_disable.disabled=1
TCEFORM.fe_users.user_auth_code.disabled=1
');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/t3registration_settings/', 't3registration settings');
?>