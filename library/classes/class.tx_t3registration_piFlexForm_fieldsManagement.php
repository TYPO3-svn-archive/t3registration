<?php
class tx_t3registration_piFlexForm_fieldsManagement{
    public function getFields($PA, $fObj) {
        //using compressor to reduce size of js files
        $compressor = t3lib_div::makeInstance('t3lib_compressor');
        //js file for checkbox with field
        $T3RegistrationCheckboxWithField = $compressor->compressJsFile('../typo3conf/ext/t3registration/library/javascript/T3RegistrationCheckboxWithField.js');
        //js file for tab object
        $T3RegistrationTab = $compressor->compressJsFile('../typo3conf/ext/t3registration/library/javascript/T3RegistrationTab.js');
        //main js file
        $T3RegistrationFieldsManager = $compressor->compressJsFile('../typo3conf/ext/t3registration/library/javascript/T3RegistrationFieldsManager.js');
        //create code for script
        $formField = '<script type="text/javascript" src="' . $T3RegistrationCheckboxWithField . '" ></script>
                      <script type="text/javascript" src="' . $T3RegistrationTab . '" ></script>
                      <script type="text/javascript" src="' . $T3RegistrationFieldsManager . '" ></script>';
        //add tab place holder and hidden field
        $formField .= '<div id="T3RegistrationFieldsManagerPlaceHolder"></div><input value=\'' .   $PA['itemFormElValue'] . '\' type="hidden" name="'. $PA['itemFormElName'] . '" id="T3RegistrationFieldsManagerHidden" />';
        return $formField;
    }
}

?>