<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010
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

define('PATH_t3registration',t3lib_extMgm::extPath('t3registration'));

//require_once(PATH_t3registration . 'configuration/languageFields')

/**
 *
 *
 * @package
 * @subpackage
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_t3registration_eid extends tslib_pibase {
    public function main($operation){
        $this->pi_loadLL();
        switch ($operation){
            case 'language':
                $language = (t3lib_div::_GP('language')) ? t3lib_div::_GP('language') : 'default';
                $languageLabelArray = t3lib_div::readLLfile(PATH_t3registration . 'locallang_js.xml','it','utf-8');
                tslib_eidtools::initLanguage($language);
                $GLOBALS['LANG']->includeLLFile(PATH_t3registration . 'locallang_js.xml');
                /*foreach($languageLabelArray[$language] as $key => $item){

                }*/
                header('Content-type: text/javascript');
                echo 'var translateObject = ' . json_encode($languageLabelArray[$language]) . ';';

                //echo $GLOBALS['LANG']->getLL('msgbox.addPanel.title');
                break;
        }
    }
}

$eidClass = t3lib_div::makeInstance('tx_t3registration_eid');
$eidClass->main(t3lib_div::_GP('operation'));