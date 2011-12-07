<?php
class tx_t3registration_hooks {
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

    public function fillPasswordFieldForProfile(&$params, &$pObj) {
        $pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']] = $params['user']['password'];
        return $pObj->piVars;
    }

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

    public function addHiddenForParams(&$params, $pObj) {
        //Enable function
        if ($pObj->conf['extra.']['saveParamsFromUrl'] && $GLOBALS['TSFE']->loginUser == 0) {
            if (isset($pObj->piVars['paramsFromUrl'])) {
                $params['hiddenArray']['paramsFromUrl'] = '<input type="hidden" name="tx_t3registration_pi1[paramsFromUrl]" value="' . $pObj->piVars['paramsFromUrl'] . '" />';
            }
            else {
                $paramsWhitelist = (isset($pObj->conf['extra.']['saveParamsFromUrl.']['list']) || isset($pObj->conf['extra.']['saveParamsFromUrl.']['list.'])) ? $pObj->cObj->stdWrap($pObj->conf['extra.']['saveParamsFromUrl.']['list'], $pObj->conf['extra.']['saveParamsFromUrl.']['list.']) : '';
                $paramsList = explode('&', t3lib_div::getIndpEnv('QUERY_STRING'));
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
                    debug(t3lib_div::locationHeaderUrl($pObj->pi_getPageLink($redirectId, '', $urlParameters)));
                }
            }
        }
    }
}


?>