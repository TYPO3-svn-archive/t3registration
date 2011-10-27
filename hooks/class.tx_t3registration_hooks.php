<?php
class tx_t3registration_hooks{
    public function addPasswordMarker(&$params,&$pObj){
        if($pObj->conf['extra.']['passwordTwice']){
            if(!$params['preview']){
                $field = $pObj->getField('password');
                $field['name'] = $pObj->conf['extra.']['passwordTwiceField'];
                $field['label'] = ($pObj->conf['extra.']['passwordTwiceFieldLabel'])?$pObj->pi_getLL($pObj->conf['extra.']['passwordTwiceFieldLabel']):$field['label'];
                $params['contentArray']['###' . strtoupper($field['name']) . '_FIELD###'] = $pObj->getAndReplaceSubpart($field,$params['content']);
            }
            else{
                $field['name'] = $pObj->conf['extra.']['passwordTwiceField'];
                $params['hiddenArray'][strtoupper($field['name'])] = sprintf('<input type="hidden" name="%s" value="%s" />',$pObj->prefixId.'[' . $field['name'] . ']',$pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']]);
            }
        }
    }

    public function fillPasswordFieldForProfile(&$params,&$pObj){
        $pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']] = $params['user']['password'];
        return $pObj->piVars;
    }

    public function checkPasswordTwice($params,&$pObj){
        if(!isset($pObj->piVars['extra.']['passwordtwice']) || !$pObj->conf['extra.']['passwordTwice']){
            $pObj->errorArray['error'][$pObj->conf['extra.']['passwordTwiceField']] = true;
            return true;
        }
        else{
            if($pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']] === $params['value']){
                $pObj->errorArray['error'][$pObj->conf['extra.']['passwordTwiceField']] = true;
                return true;
            }
            else{
                $pObj->errorArray['error'][$pObj->conf['extra.']['passwordTwiceField']] = false;
                return false;
            }
        }
    }
}


?>