<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Federico Bernardin <federico@bernardin.it>
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
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extmgm::extPath('t3registration').'library/classes/class.tx_t3registration_formManagement.php');
require_once(t3lib_extmgm::extPath('t3registration').'library/classes/class.tx_t3registration_evaluate.php');


/**
 * Plugin 'T3 Registration' for the 't3registration' extension.
 *
 * @author	Federico Bernardin <federico@bernardin.it>
 * @package	TYPO3
 * @subpackage	tx_t3registration
 */
class tx_t3registration_pi1 extends tslib_pibase {
    public $prefixId      = 'tx_t3registration_pi1';		// Same as class name
    public $scriptRelPath = 'pi1/class.tx_t3registration_pi1.php';	// Path to this script relative to the extension dir.
    public $extKey        = 't3registration';	// The extension key.

    /**
     * template containing HTML code with subparts and marker
     * @var string
     */
    protected $templateFile = '';

    /**
     * The structure to extract data from flexform: sheet => list of fields
     * @var array
     */
    protected $fieldsFromPiFlexForm = array(
            'sDEF' => array('displayMode','pages','recursive'),
            's_template' => array('templateFile'),
            's_fields' => array('useEmailAsUsername'),
            's_configuration' => array('confirmationPage','groupAssociationMode','emailFormat','confirmationProcessMode','senderEmail','senderName','adminEmail','groupOnConfirmation','groupOnRegistration')
    );

    /**
     * The main method of the PlugIn
     *
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	The content that is displayed on the website
     */
    public function main($content, $conf) {
        $this->conf = $conf;
        $this->errors['username'] = array('Username is not unique','error number 2');
        $this->pi_setPiVarDefaults();
        $this->pi_initPIflexForm();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

        $this->adjustConfigurationArray();

        $this->templateFile = $this->cObj->fileResource($conf['templateFile']);
        $this->conf = $this->mergePiFlexFormValue();
        debug($this->conf,'piflexform');
        $this->createForm($this->templateFile);

        $content='
      <strong>This is a few paragraphs:</strong><br />
      <p>This is line 1</p>
      <p>This is line 2</p>

      <h3>This is a form:</h3>
      <form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'" method="POST">
        <input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars['input_field']).'">
        <input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL('submit_button_label')).'">
      </form>
      <br />
      <p>You can click here to '.$this->pi_linkToPage('get to this page again',$GLOBALS['TSFE']->id).'</p>
    ';

        return $this->pi_wrapInBaseClass($content);
    }

    /**
     * This function adjusts the conf array
     * @return void
     */
    protected function adjustConfigurationArray(){
        //arranges conf['fields'] to be compatible with one extract from piFlexForm
        if (strlen($this->conf['fields']) > 0 ){
            $tempFields = explode(',',$this->conf['fields']);
            $this->conf['fields'] = array();
            for ($countFields = 0; $countFields < count($tempFields); $countFields++){
                $this->conf['fields'][trim($tempFields[$countFields])] = trim($tempFields[$countFields]);
            }
        }
    }


    /**
     * This function merges data from conf with flexform (flexform overwrites conf)
     * @return array merged array
     */
    public function mergePiFlexFormValue(){
        //extract piflexform fields
        foreach($this->fieldsFromPiFlexForm as $sheet => $fieldsList){
            foreach($fieldsList as $field)
                $flexForm[$field] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],$field,$sheet);
        }
        //extracts fields from json piflexform FieldsManager
        $flexForm['fieldsConfiguration.'] = $this->extractPiFlexFormFields();
        //create an array with fields list with key and value equals to name of field
        foreach ($flexForm['fieldsConfiguration.'] as $field => $item){
            $fieldName = trim(substr($field,0,strlen($field)-1));
            $flexForm['fields'][$fieldName] = $fieldName;
        }
        //merge array
        $flexForm = t3lib_div::array_merge_recursive_overrule($this->conf,$flexForm);
        return $flexForm;
    }

    /**
     * Extracts data from fieldManager field of piFlexForm
     * fieldManager is a json object in this form: {"field1":["alfanum","date;valueofdate","saveinflex;nameoffield","maximum;numberofmaximum"],"field2":["required","int","decimal","minimum;numberofminimum"]}
     * @return array array of fields in this structure:
     * array('fieldsConfiguration' => array('field1' => array(array('alfanum' => 1),array('date' => 'valueofdate')), 'field2' => array(array('required' => 1),...)))
     */
    protected function extractPiFlexFormFields(){
        //extract value from piFlexForm
        $fieldsRawList = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'fieldsManager','s_fields');
        //decode from JSON into an associative array
        $fieldsList = json_decode($fieldsRawList,true);
        //builds the specified array
        foreach ($fieldsList as $field => $evalsList){
            foreach ($evalsList as $eval){
                //if contains a semicolon means it contains the value of eval field
                if(strpos($eval,';')){
                    $evalArray = explode(';',$eval);
                    $evalField[trim($evalArray[0])] = (isset($evalArray[1])) ? trim($evalArray[1]) : '';
                }
                else{
                    $evalField[trim($eval)] = 1;
                }
                $fields[trim($field) . '.'] = $evalField;
            }
        }
        return $fields;
    }

    protected function createForm($template){
        $formManagementClassName = t3lib_div::makeInstanceClassName('tx_t3registration_formManagement');
        $formManager = new $formManagementClassName($this->cObj,$this->conf,$template,$this);
        $formManager->getRegistrationForm();
    }


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/pi1/class.tx_t3registration_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/pi1/class.tx_t3registration_pi1.php']);
}

?>