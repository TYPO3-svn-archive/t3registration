<?php
if (!defined('TYPO3_MODE')) {
  die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_t3registration_pi1.php', '_pi1', 'list_type', 0);
$TYPO3_CONF_VARS['EXTCONF']['t3registration']['extraMarkersRegistration'][] = 'EXT:t3registration/hooks/class.tx_t3registration_hooks.php:tx_t3registration_hooks->addPasswordMarker';
$TYPO3_CONF_VARS['EXTCONF']['t3registration']['profileFetchData'][] = 'EXT:t3registration/hooks/class.tx_t3registration_hooks.php:tx_t3registration_hooks->fillPasswordFieldForProfile';
$TYPO3_CONF_VARS['EXTCONF']['t3registration']['extraMarkersRegistration'][] = 'EXT:t3registration/hooks/class.tx_t3registration_hooks.php:tx_t3registration_hooks->addHiddenForParams';
$TYPO3_CONF_VARS['EXTCONF']['t3registration']['confirmedProcessComplete'][] = 'EXT:t3registration/hooks/class.tx_t3registration_hooks.php:tx_t3registration_hooks->redirectWithParams';
$TYPO3_CONF_VARS['EXTCONF']['t3registration']['afterInsertUser'][] = 'EXT:t3registration/hooks/class.tx_t3registration_hooks.php:tx_t3registration_hooks->saveParams';
$TYPO3_CONF_VARS['EXTCONF']['t3registration']['beforeInsertUser'][] = 'EXT:t3registration/hooks/class.tx_t3registration_hooks.php:tx_t3registration_hooks->saltedPassword';
$TYPO3_CONF_VARS['EXTCONF']['t3registration']['beforeUpdateUser'][] = 'EXT:t3registration/hooks/class.tx_t3registration_hooks.php:tx_t3registration_hooks->saltedPassword';
?>