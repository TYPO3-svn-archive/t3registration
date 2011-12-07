<?php

########################################################################
# Extension Manager/Repository config file for ext "t3registration".
#
# Auto generated 17-06-2011 12:19
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
  'version' => '0.9.0',
  'constraints' => array(
    'depends' => array(
            'cms' => '',
            'php' => '4.2.2-5.3.99',
            'typo3' => '4.5.2-4.6.99',
    ),
    'conflicts' => array(
    ),
    'suggests' => array(
        't3jquery' => ''
    ),
  ),
  '_md5_values_when_last_written' => 'a:23:{s:9:"ChangeLog";s:4:"8937";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"5ee3";s:14:"ext_tables.php";s:4:"f859";s:14:"ext_tables.sql";s:4:"92a5";s:13:"locallang.xml";s:4:"b675";s:16:"locallang_db.xml";s:4:"d466";s:19:"doc/wizard_form.dat";s:4:"d8b2";s:20:"doc/wizard_form.html";s:4:"5a42";s:13:"mod1/conf.php";s:4:"f88b";s:14:"mod1/index.php";s:4:"1d67";s:18:"mod1/locallang.xml";s:4:"a139";s:22:"mod1/locallang_mod.xml";s:4:"82a0";s:22:"mod1/mod_template.html";s:4:"7c59";s:19:"mod1/moduleicon.gif";s:4:"8074";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:35:"pi1/class.tx_t3registration_pi1.php";s:4:"2b81";s:43:"pi1/class.tx_t3registration_pi1_wizicon.php";s:4:"7eb2";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"9ab1";s:44:"static/t3registration_settings/constants.txt";s:4:"96ef";s:40:"static/t3registration_settings/setup.txt";s:4:"f531";}',
);

?>