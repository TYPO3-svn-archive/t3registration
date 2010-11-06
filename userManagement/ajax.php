<?php
/***************************************************************
*  Copyright notice
*
*  (c) 20102010
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 *
 *
 * @package
 * @subpackage
 *
 */


/**
 * Module 'T3 Registration' for the 't3registration' extension.
 *
 * @author  Federico Bernardin <federico@bernardin.it>
 * @package TYPO3
 * @subpackage  tx_t3registration
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class  tx_t3registration_ajax {

    public function main($params, &$ajaxObject){
        $test= array(
            array('uid' => 1, 'username' => 'federico', 'password' => 'test'),
            array('uid' => 2, 'username' => 'federico1', 'password' => 'test1'),
            array('uid' => 3, 'username' => 'federico2', 'password' => 'test2'),
        );
        //debug($_GET);
        $test = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,username,password','fe_users','pid=' . t3lib_div::_GP('folder') . ' AND disable = 0 AND deleted = 0');
        //debug($GLOBALS['TYPO3_DB']->SELECTquery('uid,username,password','fe_users','pid=' . t3lib_div::_GP('id')));
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($test)){
            $list[] = $row;
        }
        //debug($test);
        $ajaxObject->addContent('data',$list);
        $ajaxObject->setContentFormat('json');
    }


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/userManagement/ajax.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/userManagement/ajax.php']);
}

?>
