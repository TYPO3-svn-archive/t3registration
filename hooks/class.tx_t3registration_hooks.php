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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class tx_t3registration_hooks
 *   55:     public function addPasswordMarker(&$params, &$pObj)
 *   77:     public function fillPasswordFieldForProfile(&$params, &$pObj)
 *   89:     public function checkPasswordTwice($params, &$pObj)
 *  114:     public function addHiddenForParams(&$params, $pObj)
 *  147:     public function saveParams(&$params, $pObj)
 *  167:     public function redirectWithParams(&$params, $pObj)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * This class manages hooks
 *
 */
class tx_t3registration_hooks {

    /**
 * This function manages marker for double password
 *
 * @param	$params		array parameters
 * @param	$pObj		object remote caller class
 * @return	[type]		...
 */
    public function addPasswordMarker(&$params, &$pObj) {
        if ($pObj->conf['extra.']['passwordTwice']) {
            if (!$params['preview']) {
                $field = $pObj->getField('password');
                $field['name'] = $pObj->conf['extra.']['passwordTwiceField'];
                $field['label'] = ($pObj->conf['extra.']['passwordTwiceFieldLabel']) ? $pObj->pi_getLL($pObj->conf['extra.']['passwordTwiceFieldLabel']) : $field['label'];
                $params['contentArray']['###' . strtoupper($field['name']) . '_FIELD###'] = $pObj->getAndReplaceSubpart($field, $params['content']);
            }
            else {
                $field['name'] = $pObj->conf['extra.']['passwordTwiceField'];
                $params['hiddenArray'][strtoupper($field['name'])] = sprintf('<input type="hidden" name="%s" value="%s" />', $pObj->prefixId . '[' . $field['name'] . ']', $pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']]);
            }
        }
    }

    /**
 * This function manages marker for double password in update profile prefills field
 *
 * @param	$params		array parameters
 * @param	$pObj		object remote caller class
 * @return	[type]		...
 */
    public function fillPasswordFieldForProfile(&$params, &$pObj) {
        $pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']] = $params['user']['password'];
        return $pObj->piVars;
    }

    /**
 * This function evaluates double password
 *
 * @param	$params		array parameters
 * @param	$pObj		object remote caller class
 * @return	boolean		true if no errors found, otherwise return false
 */
    public function checkPasswordTwice($params, &$pObj) {
        if (!isset($pObj->conf['extra.']['passwordTwice']) || !$pObj->conf['extra.']['passwordTwice']) {
            $pObj->errorArray['error'][$pObj->conf['extra.']['passwordTwiceField']] = true;
            return true;
        }
        else {
            if ($pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']] === $params['value']) {
                $pObj->errorArray['error'][$pObj->conf['extra.']['passwordTwiceField']] = true;
                return true;
            }
            else {
                $pObj->errorArray['error'][$pObj->conf['extra.']['passwordTwiceField']] = false;
                return false;
            }
        }
    }

    /**
 * This function manages the redirect parameters passed from url use extra.saveParamsFromUrl=1 to enabled features
 * use extra.saveParamsFromUrl.list to define the list of parameters to allowed to be saved (stdWrap)
 *
 * @param	$params		array parameters
 * @param	$pObj		object remote caller class
 * @return	[type]		...
 */
    public function addHiddenForParams(&$params, $pObj) {
        //Enable function
        if ($pObj->conf['extra.']['saveParamsFromUrl'] && $GLOBALS['TSFE']->loginUser == 0) {
            if (isset($pObj->piVars['paramsFromUrl'])) {
                $params['hiddenArray']['paramsFromUrl'] = '<input type="hidden" name="tx_t3registration_pi1[paramsFromUrl]" value="' . $pObj->piVars['paramsFromUrl'] . '" />';
            }
            else {
                $paramsWhitelist = (isset($pObj->conf['extra.']['saveParamsFromUrl.']['list']) || isset($pObj->conf['extra.']['saveParamsFromUrl.']['list.'])) ? $pObj->cObj->stdWrap($pObj->conf['extra.']['saveParamsFromUrl.']['list'], $pObj->conf['extra.']['saveParamsFromUrl.']['list.']) : '';
                $paramsList = explode('&', urldecode(t3lib_div::getIndpEnv('QUERY_STRING')));
                $paramToSave = array();
                if (is_array($paramsList) && count($paramsList)) {
                    foreach ($paramsList as $item) {
                        $tempSingleParam = explode('=', $item);
                        if (t3lib_div::inList($paramsWhitelist, $tempSingleParam[0])) {
                            $paramToSave[] = htmlentities(strip_tags($item));
                        }
                    }
                }
                if (count($paramToSave) > 0) {
                    $params['hiddenArray']['paramsFromUrl'] = '<input type="hidden" name="tx_t3registration_pi1[paramsFromUrl]" value="' . implode(',', $paramToSave) . '" />';
                }
            }
        }
    }

    /**
 * This function manages the redirect parameters passed from url use extra.saveParamsFromUrl=1 to enabled features
 * this part of hook save params in cache_md5params before saving user
 *
 * @param	$params		array parameters
 * @param	$pObj		object remote caller class
 * @return	[type]		...
 */
    public function saveParams(&$params, $pObj) {
        if ($pObj->conf['extra.']['saveParamsFromUrl'] && $GLOBALS['TSFE']->loginUser == 0) {
            $values = array(
                'md5hash' => substr(md5($params['user']['uid'] . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']), 0, 20),
                'tstamp' => time(),
                'type' => 'fe',
                'params' => serialize($params['piVars']['paramsFromUrl'])
            );
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('cache_md5params', $values);
        }
    }

    /**
 * This function manages the redirect parameters passed from url use extra.saveParamsFromUrl=1 to enabled features
 * this part of hook fetch data from cache to redirect user
 *
 * @param	$params		array parameters
 * @param	$pObj		object remote caller class
 * @return	[type]		...
 */
    public function redirectWithParams(&$params, $pObj) {
        if ($pObj->conf['extra.']['saveParamsFromUrl'] && $GLOBALS['TSFE']->loginUser == 0) {
            if ($params['lastEvent'] == 'userAuth') {
                $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'cache_md5params', 'md5hash=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(substr(md5($params['user']['uid'] . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']), 0, 20), 'cache_md5params'));
                if ($GLOBALS['TYPO3_DB']->sql_num_rows($resource) == 1) {
                    $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                    $paramsWhitelist = (isset($pObj->conf['extra.']['saveParamsFromUrl.']['list']) || isset($pObj->conf['extra.']['saveParamsFromUrl.']['list.'])) ? $pObj->cObj->stdWrap($pObj->conf['extra.']['saveParamsFromUrl.']['list'], $pObj->conf['extra.']['saveParamsFromUrl.']['list.']) : '';
                    $paramsList = explode(',', unserialize($row['params']));
                    foreach ($paramsList as $item) {
                        $tempSingleParam = explode('=', $item);
                        if (t3lib_div::inList($paramsWhitelist, $tempSingleParam[0])) {
                            $urlParameters[$tempSingleParam[0]] = $tempSingleParam[1];
                        }
                    }
                    if (isset($pObj->conf['extra.']['saveParamsFromUrl.']['pageParameter'])) {
                        $redirectId = $urlParameters[$pObj->conf['extra.']['saveParamsFromUrl.']['pageParameter']];
                        unset($urlParameters[$pObj->conf['extra.']['saveParamsFromUrl.']['pageParameter']]);
                    }
                    else {
                        $redirectId = (isset($pObj->conf['extra.']['saveParamsFromUrl.']['redirectPage'])) ? $pObj->conf['extra.']['saveParamsFromUrl.']['redirectPage'] : $GLOBALS['TSFE']->id;
                    }
                    $GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_md5params', 'md5hash=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(substr(md5($params['user']['uid'] . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']), 0, 20), 'cache_md5params'));
                    header('Location: ' . t3lib_div::locationHeaderUrl($pObj->pi_getPageLink($redirectId, '', $urlParameters)));
                    exit();
                }
            }
        }
    }
}


?>