<?php
class user_example{
    public function check($params,&$pObj){
        $checked = ($pObj->piVars[$params['field']['name']])? 'checked':'';
        return sprintf('<input type="checkbox" name="%s" %s/>',$pObj->prefixId.'[' . $params['field']['name'] . ']',$checked);
    }
    public function ifcheck($params,&$pObj){
        if(isset($params['row'][$params['field']['name']]) && strlen($params['row'][$params['field']['name']])>0){
            return true;
        }
        else{
            return false;
        }
    }

    public function fetch($params,&$pObj){
        return '5';
    }
}
?>