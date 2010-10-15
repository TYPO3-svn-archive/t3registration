<?php
if (!defined ('TYPO3_MODE')) {
   die ('Access denied.');
}


t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_t3registration_pi1.php', '_pi1', 'list_type', 0);

$TYPO3_CONF_VARS['BE']['AJAX']['tx_t3registration::getuser'] = 'EXT:t3registration/userManagement/ajax.php:tx_t3registration_ajax->main';
?>