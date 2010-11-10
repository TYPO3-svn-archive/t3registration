<?php
class tx_t3registration_piFlexForm_groupAssociationMode{
    public function listMode($PA, $fObj) {
        global $LANG;
        //using compressor to reduce size of js files
        $compressor = t3lib_div::makeInstance('t3lib_compressor');
        //main js file
        $T3RegistrationGroupAssociationMode = $compressor->compressJsFile('../typo3conf/ext/t3registration/library/javascript/T3RegistrationGroupAssociationMode.js');
        //create code for script
        $formField = '<script type="text/javascript" src="' . $T3RegistrationGroupAssociationMode . '" ></script>';
        //add tab place holder and hidden field
        $formField .= '<div id="T3RegistrationGroupAssociationModePlaceHolder"></div><input value=\'' .   $PA['itemFormElValue'] . '\' type="hidden" name="'. $PA['itemFormElName'] . '" id="T3RegistrationGroupAssociationModeHidden" />';
        return $formField;
    }
}

?>