<?php
if (!defined ('TYPO3_MODE')) {
  die ('Access denied.');
}

/*############Setting new columns###########*/
t3lib_div::loadTCA('tt_content');


/*############Update TCE FORMS############*/
//Exclude fields
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';

//Include Fields
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
//Add FlexForm
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:t3registration/res/pi_flexform.xml');


/*###########Adding the Fronteend Plugin##############*/
t3lib_extMgm::addPlugin(array(
  'LLL:EXT:t3registration/locallang_db.xml:tt_content.list_type_pi1',
  $_EXTKEY . '_pi1',
  t3lib_extMgm::extRelPath($_EXTKEY) . 't3registration.png'
),'list_type');



if (TYPO3_MODE == 'BE') {
  t3lib_extMgm::addModulePath('web_txt3registrationM1', t3lib_extMgm::extPath($_EXTKEY) . 'userManagement/');

  t3lib_extMgm::addModule('web', 'txt3registrationM1', 'before:info', t3lib_extMgm::extPath($_EXTKEY) . 'userManagement/');
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/t3_registration_configuration/', 'T3 Registration Configuration');
?>