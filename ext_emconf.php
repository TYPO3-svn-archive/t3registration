<?php

########################################################################
# Extension Manager/Repository config file for ext "t3registration".
#
# Auto generated 24-12-2011 12:24
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'T3Registration',
	'description' => 'T3Registration is a plugin to manage the user registration process, email confirmation, group association and so on...',
	'category' => 'plugin',
	'author' => 'Federico Bernardin',
	'author_email' => 'federico@bernardin.it',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 'uploads/pics',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'BFConsulting',
	'version' => '0.9.2',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '4.2.2-5.3.99',
			'typo3' => '4.5.2-4.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3jquery' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:43:{s:9:"ChangeLog";s:4:"8937";s:10:"README.txt";s:4:"98e1";s:22:"class.user_example.php";s:4:"efac";s:12:"ext_icon.gif";s:4:"3cf4";s:17:"ext_localconf.php";s:4:"e3f6";s:15:"ext_php_api.dat";s:4:"eacc";s:14:"ext_tables.php";s:4:"9426";s:14:"ext_tables.sql";s:4:"e96b";s:28:"ext_typoscript_constants.txt";s:4:"badd";s:24:"ext_typoscript_setup.txt";s:4:"8d74";s:12:"flexform.xml";s:4:"ce7c";s:13:"locallang.xml";s:4:"b675";s:16:"locallang_db.xml";s:4:"3082";s:18:"t3registration.gif";s:4:"bfbd";s:18:"t3registration.png";s:4:"a33f";s:14:"doc/manual.sxw";s:4:"616a";s:19:"doc/wizard_form.dat";s:4:"d8b2";s:20:"doc/wizard_form.html";s:4:"5a42";s:39:"hooks/class.tx_t3registration_hooks.php";s:4:"f5d1";s:47:"library/class.tx_t3registration_checkstatus.php";s:4:"85c8";s:57:"library/class.tx_t3registration_getFeUsersColumnNames.php";s:4:"2b45";s:14:"pi1/ce_wiz.png";s:4:"b1ee";s:35:"pi1/class.tx_t3registration_pi1.php";s:4:"b555";s:43:"pi1/class.tx_t3registration_pi1_wizicon.php";s:4:"290e";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"8ef9";s:18:"pi1/old_ce_wiz.gif";s:4:"02b6";s:26:"pi1/t3registration_wiz.gif";s:4:"aa92";s:26:"pi1/t3registration_wiz.png";s:4:"1e6a";s:17:"pi1/template.html";s:4:"f286";s:13:"res/error.png";s:4:"1c8f";s:21:"res/flashMessages.css";s:4:"0be5";s:19:"res/information.png";s:4:"6235";s:15:"res/message.css";s:4:"2368";s:14:"res/notice.png";s:4:"813d";s:10:"res/ok.png";s:4:"e36c";s:13:"res/trash.png";s:4:"b804";s:15:"res/warning.png";s:4:"dada";s:28:"res/javascript/initialize.js";s:4:"2020";s:30:"res/javascript/registration.js";s:4:"2e19";s:44:"static/t3registration_settings/constants.txt";s:4:"96ef";s:40:"static/t3registration_settings/setup.txt";s:4:"dfcb";s:44:"tests/tx_t3registration_general_testcase.php";s:4:"c800";}',
	'suggests' => array(
	),
);

?>