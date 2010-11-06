<?php
class tx_t3registration_piFlexForm_fieldsManagement{
    public function getFields($PA, $fObj) {
        //TODO: make dynamic changeable the path to js
        $formField = '<script type="text/javascript" src="/typo3conf/ext/t3registration/library/javascript/piFlexFormFieldsManager.js" ></script>';
        $formField .= '<div id="fieldsManagerPlaceHolder"></div><input value="pippo" type="hidden" name="'. $PA['fieldsManager'] . '" id="fieldsManagerHidden" />';
        return $formField;
    }
}

?>