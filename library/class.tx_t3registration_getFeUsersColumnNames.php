<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Federico Bernardin <federico@bernardin.it>
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


class tx_t3registration_getFeUsersColumnNames {

    /**
     * This function is used to get the "fe_users" field names into the flexform of the plugin.
     *
     * @param    array        $config: the fields selected
     * @return    array        $config
     */
    function getFeUsersColumnNames($config) {

        global $TCA;

        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3registration']);

        t3lib_div::loadTCA('fe_users');
        if ($extConf['disabledTranslation'] == 1) {
            foreach ($TCA['fe_users']['columns'] as $key => $item) {
                if (!t3lib_div::inList($extConf['disabledFieldsFromFlexform'],$key)) {
                    $config['items'][] = array($key, $key);
                }
            }
        } else {
            foreach ($TCA['fe_users']['columns'] as $key => $item) {
                if (!t3lib_div::inList($extConf['disabledFieldsFromFlexform'],$key)) {
                    $config['items'][] = array($GLOBALS['LANG']->sL($item['label'], true), $key);
                }
            }
        }


        return $config;
    }

}

?>