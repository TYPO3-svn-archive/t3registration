<?php

########################################################################
# Extension Manager/Repository config file for ext "t3registration".
#
# Auto generated 10-08-2012 14:52
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
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 'uploads/pics',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'BFConsulting',
	'version' => '1.2.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '4.2.2-5.3.99',
			'typo3' => '4.5.0-4.7.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3jquery' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:48:{s:9:"ChangeLog";s:4:"a1bc";s:10:"README.txt";s:4:"98e1";s:21:"ext_conf_template.txt";s:4:"f6a1";s:12:"ext_icon.gif";s:4:"3cf4";s:17:"ext_localconf.php";s:4:"0c26";s:15:"ext_php_api.dat";s:4:"eacc";s:14:"ext_tables.php";s:4:"5cfa";s:14:"ext_tables.sql";s:4:"e96b";s:28:"ext_typoscript_constants.txt";s:4:"4828";s:24:"ext_typoscript_setup.txt";s:4:"b097";s:12:"flexform.xml";s:4:"a7ff";s:13:"locallang.xml";s:4:"dc39";s:16:"locallang_db.xml";s:4:"5265";s:18:"t3registration.gif";s:4:"bfbd";s:18:"t3registration.png";s:4:"a33f";s:8:"user.xml";s:4:"8093";s:14:"doc/manual.sxw";s:4:"b873";s:19:"doc/wizard_form.dat";s:4:"d8b2";s:20:"doc/wizard_form.html";s:4:"5a42";s:39:"hooks/class.tx_t3registration_hooks.php";s:4:"39ff";s:47:"library/class.tx_t3registration_checkstatus.php";s:4:"f9a4";s:57:"library/class.tx_t3registration_getFeUsersColumnNames.php";s:4:"1a9b";s:56:"library/class.tx_t3registration_tcaexternalfunctions.php";s:4:"a1c8";s:14:"pi1/ce_wiz.png";s:4:"b1ee";s:35:"pi1/class.tx_t3registration_pi1.php";s:4:"4831";s:43:"pi1/class.tx_t3registration_pi1_wizicon.php";s:4:"290e";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"26f1";s:18:"pi1/old_ce_wiz.gif";s:4:"02b6";s:26:"pi1/t3registration_wiz.gif";s:4:"aa92";s:26:"pi1/t3registration_wiz.png";s:4:"1e6a";s:17:"pi1/template.html";s:4:"5457";s:13:"res/error.png";s:4:"1c8f";s:21:"res/flashMessages.css";s:4:"0be5";s:19:"res/information.png";s:4:"6235";s:15:"res/message.css";s:4:"2368";s:14:"res/notice.png";s:4:"813d";s:10:"res/ok.png";s:4:"e36c";s:13:"res/trash.png";s:4:"b804";s:15:"res/warning.png";s:4:"dada";s:28:"res/javascript/initialize.js";s:4:"2020";s:30:"res/javascript/registration.js";s:4:"2e19";s:44:"static/t3registration_settings/constants.txt";s:4:"4462";s:40:"static/t3registration_settings/setup.txt";s:4:"8657";s:17:"tests/phpunit.xml";s:4:"176a";s:19:"tests/t3regTest.php";s:4:"2183";s:44:"tests/tx_t3registration_general_testcase.php";s:4:"c800";s:37:"tests/tx_t3registration_testsuite.php";s:4:"ad11";}',
	'suggests' => array(
	),
);

?>