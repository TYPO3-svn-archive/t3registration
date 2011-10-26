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
            $params['hiddenArray'][strtoupper($field['name'])] = sprintf('<input type="hidden" name="%s" value="%s" />',$pObj->prefixId.'[' . $field['name'] . ']',$pObj->piVars[$pObj->conf['passwordTwiceField']]);
        }
        }
    }

    public function fillPasswordFieldForProfile(&$params,&$pObj){
        $pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']] = $params['user']['password'];
    }

    public function checkPasswordTwice($params,&$pObj){
        if(!$pObj->conf['extra.']['passwordTwice']){
            return true;
        }
        if($pObj->conf['extra.']['passwordTwice'] && isset($pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']])){
            if($pObj->piVars[$pObj->conf['extra.']['passwordTwiceField']] === $params['value']){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            if($pObj->conf['extra.']['passwordTwice'] && isset($pObj->piVars['extra.']['passwordtwice']) && $pObj->piVars['extra.']['passwordtwice'] === $params['value']){
                return true;
            }
            else{
                return false;
            }
        }
        break;
    }
}


?>