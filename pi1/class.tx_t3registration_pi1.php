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
 * Hint: use extdeveval to insert/update function index above.
 */

/**
 * Constant for default path of upload folder if it's not setted
 * @var string
 */
define('UPLOAD_FOLDER', 'uploads/pics');

/**
 * Constant for HTML value
 * @var int
 */
define('HTML', 1);
/**
 * Constant for TEXT value
 * @var int
 */
define('TEXT', 2);

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('t3registration') . 'library/class.tx_t3registration_checkstatus.php');




/**
 * Plugin 'Registration' for the 't3registration' extension.
 *
 * @author	Federico Bernardin <federico@bernardin.it>
 * @package	TYPO3
 * @subpackage	tx_t3registration
 */
class tx_t3registration_pi1 extends tslib_pibase {
    var $prefixId      = 'tx_t3registration_pi1';		// Same as class name
    var $scriptRelPath = 'pi1/class.tx_t3registration_pi1.php';	// Path to this script relative to the extension dir.
    var $extKey        = 't3registration';	// The extension key.
    var $pi_checkCHash = true;

    /**
     * contains fields will be override from ts
     * @var array
     */
    private $flexformOverrideTs = array('contactEmailMode','approvalProcess','userFolder', 'templateFile');

    /**
     * Contains fields with its configuration to rendering form fields
     * @var array
     */
    private $fieldsData = array();

    /**
     * If true double-optin is enabled
     * @var boolean
     */
    protected $userAuth = false;

    /**
     * If true admin authorization is enabled
     * @var boolean
     */
    protected $adminAuth = false;

    /**
     * language class object
     * @var object
     */
    protected $languageObj;

    /**
     * Column from fe_users TCA
     * @var array
     */
    protected $TCAField;

    protected $changeProfilePath = false;

    private $fileFields = array();

    private $emailFormat = 0;


    /**
     * The main method of the PlugIn
     *
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	string that is displayed on the website
     */
    public function main($content, $conf) {
        $GLOBALS['TSFE']->additionalHeaderData['t3registrationJQuery'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('t3registration'). 'res/javascript/initialize.js"></script>';
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        //initialize language object used in label translation from TCA
        $this->init();
        //extract data from flexform
        $this->initFlexform();
        //extract TCA columns from fe_users table
        $this->loadTCAField();
        //adds evaluation additional data
        $this->addFunctionReplace($this->conf['fieldConfiguration.'],$this->conf['fieldConfiguration.'],'');
        //merges data from flexform with ones from ts (after removing dots)
        $this->fieldsData = t3lib_div::array_merge_recursive_overrule($this->fieldsData,$this->removeDotFromArray($this->conf['fieldConfiguration.']));
        //update TCA config fields with fieldsData
        $this->mergeTCAFieldWithConfiguration();
        //Test action from url
        $this->argumentsFromUrlCheck();
        //debug($this->fieldsData);
        $this->setEmailFormat();

        //debug($this->piVars,'piVars');
        switch($this->conf['showtype']){
            case 'registration':
                if($this->piVars['sendConfirmation']){
                    $content = $this->endRegistration();
                }
                else{
                    $content = $this->getForm();
                }
                break;
            case 'delete':
                if($GLOBALS['TSFE']->loginUser){
                    if($this->externalAction['active']){
                        $content = $this->{$this->externalAction['type']}();
                    }
                    else{
                        $content = $this->showDeleteLink();
                    }

                }
                else{
                    debug('user is not logged');
                }
                break;
            case 'edit':
                if(count($this->user)>0){
                    if(!isset($this->piVars['submitted']) && !isset($this->piVars['sendConfirmation'])){
                        $content = $this->showProfile();
                    }
                    else{
                        $content = $this->getForm();
                    }
                }
                else{
                    debug('user is not logged');
                }
                break;
            case 'auto':
            default:
                if ($this->externalAction['active']){
                    //chiamata dall'url'
                    $content = $this->{$this->externalAction['type']}();
                }
                elseif($this->changeProfileCheck()){
                    $this->changeProfilePath = true;
                    $content = $this->showProfile();
                }
                else{
                    $content = $this->getForm();
                }
                break;
        }
        $content = $this->removeAllMarkers($content);
        if($this->conf['debuggingMode'] && t3lib_div::cmpIP(t3lib_div::getIndpEnv('REMOTE_ADDR'),$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'])){
            $checkClass = t3lib_div::makeInstance('tx_t3registration_checkstatus');
            $checkClass->initialize($this,$this->fieldsData);
            $content = $checkClass->main();
        }
        return $this->pi_wrapInBaseClass($content);
    }

    private function setEmailFormat(){
        $emailFormat = explode(',',$this->conf['emailFormat']);
        if(is_array($emailFormat)){
            if(in_array('html',$emailFormat)){
                $this->emailFormat = $this->emailFormat | 1;
            }
            if(in_array('text',$emailFormat)){
                $this->emailFormat = $this->emailFormat | 2;
            }
        }
    }

    /**
     * This function transforms recursively the .add property of javascript into key additionalEval
     * @param $arrayToTraverse array child array
     * @param $parentArray array root array
     * @param $parentKey string name of the parent key
     * @return void
     */
    private function addFunctionReplace($arrayToTraverse,&$parentArray,$parentKey = ''){
        if(is_array($arrayToTraverse)){
            foreach($arrayToTraverse as $key => $item){
                if($key === 'add'){
                    unset($parentArray[$parentKey]);
                    $parentArray['additionalEval'] = $item;
                }
                else{
                    if(array_key_exists($parentKey,$parentArray)){
                        $this->addFunctionReplace($parentArray[$parentKey][$key],$parentArray[$parentKey],$key);
                    }
                    else{
                        $this->addFunctionReplace($parentArray[$key],$parentArray,$key);
                    }
                }
            }
        }
    }


    /**
     * This function allow to extract a specific key from private variable $fieldsData
     * @param $name string key of array $fieldsData
     * @return array value of $this->fieldsData[$name]
     */
    public function getField($name){
        return (key_exists($name,$this->fieldsData))?$this->fieldsData[$name]:array();
    }

    /**
     * This function initializes the system
     * @return void
     */
    private function init(){
        if($GLOBALS['TSFE']->loginUser){
            $this->user = $GLOBALS['TSFE']->fe_user->user;
        }
        else{
            $this->user = array();
        }
        //initialize the language class to extract translation for label outside the actual plugin (example cms fe_users label)
        $this->languageObj = t3lib_div::makeInstance('language');
        //sets the correct language index
        $this->languageObj->init($this->LLkey);
    }

    /**
     * This function fetches flex data from flex form plugin and merge data into $this conf array.
     * @return void
     */
    private function initFlexform(){
        $this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
        $this->lConf = array(); // Setup our storage array...
        // Assign the flexform data to a local variable for easier access
        $piFlexForm = $this->cObj->data['pi_flexform'];
        // Traverse the entire array based on the language...
        // and assign each configuration option to $this->lConf array...
        if (is_array($piFlexForm['data'])){
            foreach ( $piFlexForm['data'] as $sheet => $data ){
                foreach ( $data as $lang => $value ){
                    foreach ( $value as $key => $val ){
                        $flexformValue = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
                        if(in_array($key,$this->flexformOverrideTs) && $flexformValue){
                            $this->conf[$key] = $flexformValue;
                        }
                        else{
                            if(!key_exists($key,$this->conf) || !$flexformValue){
                                $lConf[$key] = $flexformValue;
                            }
                        }
                    }
                }
            }
            if(isset($piFlexForm['data']['fieldsSheet']['lDEF']['fields']['el']) && is_array($piFlexForm['data']['fieldsSheet']['lDEF']['fields']['el'])){
                foreach($piFlexForm['data']['fieldsSheet']['lDEF']['fields']['el'] as $item){
                    foreach($item as $key => $val){
                        if(($key == 'databaseField' || $key == 'freeField') && ((isset($val['el']['name']['vDEF']) && strlen($val['el']['name']['vDEF'])>0) || (isset($val['el']['field']['vDEF']) && strlen($val['el']['field']['vDEF'])>0))){
                            $name = (isset($val['el']['name']['vDEF']) && strlen($val['el']['name']['vDEF'])>0)?$val['el']['name']['vDEF']:$val['el']['field']['vDEF'];
                            $fieldsList[] = $name;
                            $this->fieldsData[$name] = array();
                            $this->fieldsData[$name]['type'] = $key;
                            foreach($val['el'] as $fieldProperty => $fieldValue){
                                $this->fieldsData[$name][$fieldProperty] = $fieldValue['vDEF'];
                            }
                            $this->fieldsData[$name]['name'] = ($this->fieldsData[$name]['name'])?$this->fieldsData[$name]['name']:$this->fieldsData[$name]['field'];
                        }
                    }
                }
            }
            $lConf['fields'] = implode(',',$fieldsList);
            //merge lconf (flexform array data) with this->conf (typoscript data and flexformoverridets key)
            $this->conf=t3lib_div::array_merge_recursive_overrule($lConf,$this->conf);
        }
    }

    /**
     * This function loads TCA fields array into $this->TCAField array
     * @return void
     */
    protected function loadTCAField(){
        $GLOBALS['TSFE']->includeTCA();
        $this->TCAField = $GLOBALS['TCA']['fe_users']['columns'];
    }


    /**
     * This function merges TCA fields with configuration fields
     * return void
     */
    protected function mergeTCAFieldWithConfiguration(){
        foreach($this->fieldsData as $key => $item){
            if($item['type'] == 'databaseField' && isset($this->TCAField[$item['field']]) && is_array($this->TCAField[$item['field']]) ){
                if($this->testUploadFolderField($this->TCAField[$item['field']]) && $this->testUploadFolderField($this->fieldsData[$key])){
                    $this->fieldsData[$key]['config']['uploadfolder'] = UPLOAD_FOLDER;
                }
                $this->fieldsData[$key]=t3lib_div::array_merge_recursive_overrule($this->TCAField[$item['field']],$this->fieldsData[$key]);
            }
        }
    }

    /**
     * This function checks if upload folder key of config array of specified field is defined
     * @param $field array field configuration array
     * @return boolean false if not set, otherwise true
     */
    private function testUploadFolderField($field){
        if(isset($field['config']['internal_type']) && $field['config']['internal_type']=='file' && $field['config']['uploadfolder'] == 0) {
            return false;
        }
        else{
            return true;
        }
    }


    /**
     * This function defines the action to do when the page is load. If the user loads the page, the user-confiramtion is done. If the administrator
     * loads the page, the admin-confirmation is done (it happens in double-optin confirmation mode). If the "action" parameter value in piVars array is
     * "delete", when the page is load the deletion is confirmed.
     *  @return void
     */
    protected function argumentsFromUrlCheck(){
        $this->externalAction['active'] = false;
        $this->externalAction['type'] = '';
        if (isset($this->piVars['action'])){
            //ci sono parametri che possono definire
            switch ($this->piVars['action']){
                case 'userAuth':
                case 'adminAuth':
                    //call confirmation
                    $this->externalAction['type'] = 'confirmationProcessControl';
                    $this->externalAction['parameter'] = $this->piVars['action'];
                    $this->externalAction['active'] = true;
                    break;
                case 'delete':
                    $this->externalAction['type'] = 'emailDeletionSent';
                    $this->externalAction['active'] = true;
                    break;
                case 'userDeleteConfirmation':
                    $this->externalAction['type'] = 'confirmUserDeletion';
                    $this->externalAction['active'] = true;
                    break;
                case 'redirectOnLogin':
                    $this->externalAction['type'] = 'showOnAutoLogin';
                    $this->externalAction['active'] = true;
                    break;
            }
        }
    }


    /**
     * This function manages the render of form or preview infos
     * @return string the form HTML
     */
    private function getForm(){
        $content = $this->getTemplate();
        $form = '';
        $preview = false;
        if($this->piVars['submitted'] == 1){
            $error = $this->checkErrors();
            $preview = ($error)? false: true;
        }
        if($GLOBALS['TSFE']->loginUser){
            $buttons = array(
        'confirm' => 'confirmModificationProfileButton',
        'back' => 'modifyModificationProfileButton',
        'insert' => 'insertModificationProfileButton'
        );
        if($this->conf['useAnotherTemplateInChangeProfileMode'] == 1){
            if(!$preview){
                $content = $this->cObj->getSubpart($content,'T3REGISTRATION_FORM_UPDATEPROFILE');
            }else{
                $content = $this->cObj->getSubpart($content,'T3REGISTRATION_PREVIEW_UPDATEPROFILE');
            }
        }else{
            if(!$preview){
                $this->markerTitle = 'T3REGISTRATION_FORM';
                $content = $this->cObj->getSubpart($content,'T3REGISTRATION_FORM');
            }else{
                $this->markerTitle = 'T3REGISTRATION_PREVIEW';
                $content = $this->cObj->getSubpart($content,'T3REGISTRATION_PREVIEW');
            }
        }
        }else{
            if(!$preview){
                $this->markerTitle = 'T3REGISTRATION_FORM';
                $content = $this->cObj->getSubpart($content,'T3REGISTRATION_FORM');
            }else{
                $this->markerTitle = 'T3REGISTRATION_PREVIEW';
                $content = $this->cObj->getSubpart($content,'T3REGISTRATION_PREVIEW');
            }
            $buttons = array(
        'confirm' => 'confirmRegistrationButton',
        'back' => 'modifyRegistrationButton',
        'insert' => 'insertRegistrationButton'
        );
        }
        //if preview is disabled calls directly endRegistration and save user without showing a user preview
        if(($preview && !$this->conf['enablePreview']) || ($this->piVars['sendConfirmation'] == 1 && isset($this->piVars['confirmPreview']))){
            return $this->endRegistration();
        }

        $hiddenArray = array();
        $markerArray = array();

        foreach($this->fieldsData as $field){
            if($preview){
                $contentArray = $this->getAndReplaceSubpartPreview($field,$content,$contentArray);
                $hiddenArray[strtoupper($field['name'])] = sprintf('<input type="hidden" name="%s" value="%s" />',$this->prefixId.'[' . $field['name'] . ']',$this->htmlentities($this->piVars[$field['name']]));
            }else{
                $contentArray['###' . strtoupper($field['name']) . '_FIELD###'] = ($field['hideInChangeProfile'] == 1 && $GLOBALS['TSFE']->loginUser)?'':$this->getAndReplaceSubpart($field,$content);
            }
        }

        if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraMarkersRegistration'])) {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraMarkersRegistration'] as $markerFunction) {
                $params = array('preview' => $preview,'contentArray' => $contentArray,'hiddenArray' => $hiddenArray,'content' => $content);
                t3lib_div::callUserFunction($markerFunction, $params, $this);
                $contentArray = $params['contentArray'];
                $hiddenArray = $params['hiddenArray'];
            }
        }

        if($preview){
            $contentArray['###DELETE_BLOCK###'] = '';
            $hiddenArray['action'] = sprintf('<input type="hidden" name="%s" value="%s" />',$this->prefixId.'[sendConfirmation]','1');
            $content = $this->cObj->substituteMarkerArrayCached($content,$contentArray);
            $submitButton = sprintf('<input type="submit" %s name="' . $this->prefixId .'[confirmPreview]" value="%s" />',$this->cObj->stdWrap($this->conf['form.']['submitConfirm.']['params'],$this->conf['form.']['submitConfirm.']['params.']),$this->pi_getLL($buttons['confirm']));
            $submitButton = $this->cObj->stdWrap($submitButton,$this->conf['form.']['submitConfirm.']['stdWrap.']);
            $backButton = sprintf('<input type="submit" %s name="' . $this->prefixId .'[editPreview]" value="%s" />',$this->cObj->stdWrap($this->conf['form.']['submitBack.']['params'],$this->conf['form.']['submitBack.']['params.']),$this->pi_getLL($buttons['back']));
            $backButton = $this->cObj->stdWrap($backButton,$this->conf['form.']['submitBack.']['stdWrap.']);
            if($this->conf['form.']['markerButtons']){
                $markerArray['###FORM_BUTTONS###'] = sprintf('%s' . chr(10) . $backButton . chr(10) . $submitButton,implode(chr(10),$hiddenArray));
            }
            else{
                $endForm = sprintf('%s' . chr(10) . $backButton . chr(10) . $submitButton,implode(chr(10),$hiddenArray));
            }
        }else{
            $markerArray['###DELETE_BLOCK###'] = ($GLOBALS['TSFE']->loginUser)?$this->showDeleteLink():'';
            $hiddenArray['action'] = sprintf('<input type="hidden" name="%s" value="%s" />',$this->prefixId.'[submitted]','1');
            $submitButton = sprintf('<input type="submit" %s name="' . $this->prefixId .'[confirmPreview]" value="%s" />',$this->cObj->stdWrap($this->conf['form.']['submitButton.']['params'],$this->conf['form.']['submitButton.']['params.']),$this->pi_getLL($buttons['insert']));
            $submitButton = $this->cObj->stdWrap($submitButton,$this->conf['form.']['submitButton.']['stdWrap.']);
            if($this->conf['form.']['markerButtons']){
                $markerArray['###FORM_BUTTONS###'] = sprintf('%s' . chr(10) . $submitButton,implode(chr(10),$hiddenArray));
            }
            else{
                $endForm = sprintf('%s' . chr(10) . $submitButton,implode(chr(10),$hiddenArray));
            }

        }
        $content = $this->cObj->substituteMarkerArrayCached($content,$markerArray,$contentArray);
        $this->formId = ($this->conf['form.']['id'])?$this->conf['form.']['id']:'t3Registration-' . substr(md5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']),0,8);
        $action = $this->pi_getPageLink($GLOBALS['TSFE']->id);
        $content = sprintf('<form id="%s" action="%s" method="post" enctype="%s">' . chr(10) . '%s' . chr(10) . '%s' . chr(10) . '</form>',$this->formId,$action,$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],$content,$endForm);
        return $content;
    }

    /**
     * This function returns the wright template to use. If no content is found, the function returns false.
     * @return string the whole HTML template
     */
    private function getTemplate(){
        $content = $this->cObj->fileResource($this->cObj->stdWrap($this->conf['templateFile'],$this->conf['templateFile.']));
        if ($content){
            return $content;
        }
        return false;
    }


    /**
     * This function checks every fields errors. Descriptions of found errors are put into $this->errorArray.
     * return boolean true if one or more errors are found
     */
    private function checkErrors(){
        $error = false;
        foreach($this->fieldsData as $field){
            $this->errorArray['error'][$field['name']] = $this->checkField($field);
            if(!$this->errorArray['error'][$field['name']]) $error = true;
            //@todo verificare se serve
            /*if($field['field'] == 'password' && $this->conf['passwordTwice']){
            $this->errorArray['error'][$this->conf['passwordTwiceField']] = $this->errorArray['error'][$field['name']];
            $this->errorArray['required'][$this->conf['passwordTwiceField']] = $this->errorArray['required'][$field['name']];
            }*/
        }
        if(!$error){
            foreach($this->fileFields as $item){
                $tempArray = explode(',', $this->piVars[$item]);
                foreach($tempArray as $key => $value){
                    if(strlen($value) == 0){
                        unset($tempArray[$key]);
                    }
                }
                $this->piVars[$item] = implode(',', $tempArray);
            }
        }
        return $error;
    }

    /**
     * This function checks evaluation field types. Then it calls a method (evaluateField).
     * @param $field the field to check
     * @return boolean false if the field contains errors
     */
    protected function checkField($field){
        $field['config']['eval'] = $field['config']['eval']?$field['config']['eval']:'';
        if(isset($field['regexp']) && strlen($field['regexp'])>0){
            if(strlen($field['config']['eval'])>0){
                $evalArray = explode(',',$field['config']['eval']);
            }
            else{
                $evalArray = array();
            }
            $evalArray[] = 'regexp';
            $field['config']['eval'] = implode(',',$evalArray);
        }
        if(isset($field['config']['internal_type']) && $field['config']['internal_type'] === 'file'){
            if(strlen($field['config']['eval'])>0){
                $evalArray = explode(',',$field['config']['eval']);
            }
            else{
                $evalArray = array();
            }
            $evalArray[] = 'file';
            $field['config']['eval'] = implode(',',$evalArray);
        }
        $evaluation = array();
        if(isset($field['config']['eval'])){
            $evaluation = explode(',',$field['config']['eval']);
        }
        //evaluation from flexform
        if(isset($field['evaluation']) && strlen(trim($field['evaluation']))){
            $additionalEvaluationArray = array_diff(explode(',',$field['evaluation']),$evaluation);
            $evaluation = array_merge($evaluation,$additionalEvaluationArray);
        }
        //evaluation from typoscript add function
        if(isset($field['config']['additionalEval'])){
            $additionalEvaluationArray = array_diff(explode(',',$field['config']['additionalEval']),$evaluation);
            $evaluation = array_merge($evaluation,$additionalEvaluationArray);
        }
        $errorList = array();
        $error = true;
        foreach($evaluation as $item){
            //if error return false
            if(!$this->evaluateField($this->piVars[$field['name']],$item,$field)){
                //@todo controllare cosa fa errorHandlerError
                if(!(isset($field['config']['hookHandleError']) && $field['config']['hookHandleError'] == 1)){
                    $this->errorArray['errorDescription'][$field['name']][] = $item;
                }
                $error = false;
            }
        }

        return $error;
    }

    /**
     * This function checks if the field respects the evaluation rule passed.
     * @param $value the value to check
     * @param $evaluationRule the evaluation rule used to check the value
     * @param $field array field configuration
     * @return boolean true if the field respects the evaluation rule.
     */
    protected function evaluateField($value,$evaluationRule,$field = array()){
        switch($evaluationRule){
            case 'int':
                return preg_match('^/[0-9]+$/',$value);
                break;
            case 'alpha':
            case 'string':
                return preg_match('/^[a-zA-z]+$/',$value);
                break;
            case 'email':
                return preg_match('/^[a-zA-z0-9._%+-]+@[a-zA-z0-9.-]+\.[a-zA-z]{2,4}$/',$value);
                break;
            case 'regexp':
                return preg_match('/' . $field['regexp'] . '/',$value);
                break;
            case 'password':
            case 'unique':
                return $this->checkUniqueField($value,$field);
                break;
            case 'required':
                if(strlen($this->piVars[$field['name']]) == 0){
                    return false;
                }
                else{
                    return true;
                }
                break;
            case 'uniqueInPid':
                return $this->checkUniqueField($value,$field,$this->conf['userFolder']);
                break;
            case 'file':
                $files = array();
                $this->fileFields = $this->piVars[$field['name']];
                $noError = true;
                foreach($_FILES[$this->prefixId]['name'][$field['name']] as $key => $item){
                    if(strlen($item) > 0){
                        $file = $this->checkFileUploaded($item,$_FILES[$this->prefixId]['size'][$field['name']][$key],$_FILES[$this->prefixId]['tmp_name'][$field['name']][$key],$field);
                        if($file === true){
                            $noError = false;
                            $file = '';
                        }
                        else{
                            $this->fileFields[] = $file;
                        }
                    }
                }
                $this->piVars[$field['name']] = implode(',',$this->fileFields);
                return $noError;
                break;
            case 'hook':
                if(isset($field['config']['evalHook'])){
                    $params['field'] = $field;
                    $params['row'] = $this->piVars;
                    $params['value'] = $this->piVars[$field['name']];
                    return t3lib_div::callUserFunction($field['config']['evalHook'], $params, $this);
                }
                break;
            default:
                if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraEvaluationRules']))    {
                    foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraEvaluationRules'] as $evaluationFunction)    {
                        $params['field'] = $field;
                        $params['row'] = $this->piVars;
                        $params['value'] = $this->piVars[$field['name']];
                        $params['evaluationRule'] = $evaluationRule;
                        return t3lib_div::callUserFunction($evaluationFunction, $params, $this);
                    }
                }
                break;
        }
        return true;
    }

    /**
     * This function checks if the value inserted in the field by the user is unique.
     * @param $value the value to check
     * @param $field the field configuration
     * @param $folder
     * @return boolean true if the value is unique
     */
    protected function checkUniqueField($value,$field,$folder = 0){
        if($field['type'] === 'databaseField'){
            if(!is_int($value)){
                $value = $GLOBALS['TYPO3_DB']->fullQuoteStr($value,'fe_users');
            }
            $where = $field['field'] . '=' . $value;
            if($folder){
                $where .= ' AND pid=' . $folder;
            }
            //operation is an update, so you can insert a value equal own
            if(($GLOBALS['TSFE']->loginUser)){
                $where .= ' AND uid != ' . $GLOBALS['TSFE']->fe_user->user['uid'] . ' AND deleted = 0';
            }
            $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery($field['field'],'fe_users',$where);
            if($GLOBALS['TYPO3_DB']->sql_num_rows($resource)>0){
                return false;
            }
            else{
                return true;
            }
        }
        else{
            return true;
        }
    }

    /**
     * This function checks if the uploaded file is an allowed file.
     * @param $name complete name of the file
     * @param $size size of the file
     * @param $tmpFile
     * @param $field the field configuration
     * @return array the uploaded file features
     */
    protected function checkFileUploaded($name,$size,$tmpFile,$field){
        $tmpArray = explode('.',$name);
        $tmpArray = array_reverse($tmpArray);
        if(t3lib_div::inList($field['config']['allowed'],$tmpArray[0]) && ($size/1000) <= ($field['config']['max_size'])){
            $fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
            $filename = $fileFunc->getUniqueName($name, PATH_site .  $field['config']['uploadfolder']);
            t3lib_div::upload_copy_move($tmpFile,$filename);
            $extractFilename = explode('/',$filename);
            $extractFilename = array_reverse($extractFilename);
            return $extractFilename[0];
        }
        else{
            return true;
        }
    }

    /**
     * Check if the field is field is required.
     * @param $field the field configuration
     * @return true if the field is required and the value of the field is empty
     */
    protected function checkRequiredField($field){
        if($field['required'] == 1 && strlen($this->piVars[$field['name']]) == 0){
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * This function get and replace the subparts with the corresponding fields.
     * @param $field the field configuration
     * @param $content the html string that contains the markers
     * @return string the html code
     */
    public function getAndReplaceSubpart($field,$content){
        $fieldMarkerArray = array();
        $fieldContent = $this->cObj->getSubpart($content,'###' . strtoupper($field['name']) . '_FIELD###');
        if($this->piVars['submitted'] && !$this->errorArray['error'][$field['name']]){
            $fieldArray['subparts']['###ERROR_FIELD###'] = $this->getErrorSubpart($field,$fieldContent);
            $fieldArray['markers']['###CLASS_ERROR###'] = ($this->conf['errors.']['classError'])?$this->conf['errors.']['classError']:'';
        }
        else{
            $fieldArray['subparts']['###ERROR_FIELD###'] = '';
            $fieldArray['markers']['###CLASS_ERROR###'] = '';
        }
        if($field['type'] == 'databaseField'){
            $fieldArray['markers']['###AUTO_FIELD###'] = $this->getAutoField($field);
        }
        $fieldArray['markers']['###FIELD_LABEL###'] = ($this->pi_getLL($field['name'] . 'Label'))?$this->pi_getLL($field['name'] . 'Label'):((isset($field['label']))?$this->languageObj->sL($field['label'], true):'');
        $fieldArray['markers']['###FIELD_VALUE###'] = ($this->piVars[$field['name']])?$this->piVars[$field['name']]:(($field['config']['default'])?$field['config']['default']:'');
        $fieldArray['markers']['###FIELD_NAME###'] = $this->prefixId.'[' . $field['name'] . ']';
        //the first call is used to substitute subpart, the second one substitute error class markers on all template
        $fieldContent = $this->cObj->substituteMarkerArrayCached($fieldContent,$fieldArray['markers'],$fieldArray['subparts']);
        return $this->cObj->substituteMarkerArrayCached($fieldContent,$fieldArray['markers'],$fieldArray['subparts']);
    }

    /**
     * This function return the html code for every field passed according to the specified configuration.
     * @param $field the field configuration
     * @return string the html field code
     */
    private function getAutoField($field){
        $htmlBlock= '';
        switch ($field['config']['type']){
            //@todo aggiungere radio e checkbox
            case 'input':
                $type = (isset($field['config']['eval']) && t3lib_div::inList($field['config']['eval'],'password'))?'password':'text';
                $size = ($field['config']['size'])?$field['config']['size']:'15';
                $id = ($field['config']['id'])?' id="' . $field['config']['id'] . '" ':'';
                $maxchar = ($field['config']['maxchar'])?' maxchar="' . $field['config']['maxchar'] . '" ':'';
                $value = ($this->piVars[$field['name']])?$this->piVars[$field['name']]:(($field['config']['default'])?$field['config']['default']:'');
                $htmlBlock = sprintf('<input type="%s" %s name="%s" value="%s" size="%s" %s />',$type,$id,$this->prefixId.'[' . $field['name'] . ']',$value,$size,$maxchar);
                break;
            case 'group':
                if(isset($field['config']['internal_type']) && $field['config']['internal_type'] === 'file'){
                    $wrappingData = ($this->conf[$field['name'] . '.']['allWrap.'])?$this->conf[$field['name'] . '.']['allWrap.']:array();
                    $fileArray = explode(',', $this->piVars[$field['name']]);
                    for($i=1;$i<=$field['config']['maxitems'];$i++){
                        $file = (isset($fileArray[$i-1]))? $fileArray[$i-1] : '';
                        $htmlBlock .= $this->cObj->stdWrap($this->getUploadField($field,$file,$i),$wrappingData);
                    }
                }
                break;
            case 'select':
                $id = ($field['config']['id'])?' id="' . $field['config']['id'] . '" ':'';
                foreach ($field['config']['items'] as $item){
                    $text = (isset($item[0]))?(preg_match('/LLL:EXT:/',$item[0])?$GLOBALS['lang']->sl($item[0]):$item[0]):'';
                    $value = (isset($item[1]))?$item[1]:'';
                    $selected = ($this->piVars[$field['name']] == $value)?'selected':'';
                    $options[] = sprintf('<option value="%s" %s>%s</option>',$value,$selected,$text);
                }
                $value = ($this->piVars[$field['name']])?$this->piVars[$field['name']]:(($field['config']['default'])?$field['config']['default']:'');
                $htmlBlock = sprintf('<select %s name="%s" >%s</select>',$id,$this->prefixId.'[' . $field['name'] . ']',implode(chr(10),$options));
                break;
            case 'hook':
                if(isset($field['config']['hook'])){
                    $params['field'] = $field;
                    $params['row'] = $this->piVars;
                    $htmlBlock = t3lib_div::callUserFunction($field['config']['hook'], $params, $this);
                }
                break;
        }
        return $htmlBlock;
    }

    /**
     *This function...
     * @param $field
     * @param $value
     * @param $counter
     * @return string the field HTML code
     */
    protected function getUploadField($field,$value='',$counter = ''){
        $htmlBlock = '';
        $type = 'file';
        $name = $this->prefixId.'['.$field['name'].']['.$counter . ']';
        $hiddenValue = 'value=""';
        if($value){
            $hiddenValue = 'value="' . $value . '"';
            $classRef = 'class="t3registration_pi1_ref_' . $field['name'] . '_' . $counter . '"';
            $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<script type="text/javascript" src="typo3conf/ext/t3registration/res/javascript/registration.js"></script>';
            $fieldArray = (isset($this->conf[$field['name'] . '.']) && is_array($this->conf[$field['name'] . '.']))?$this->conf[$field['name'] . '.']:array();
            $fieldArray['file'] = $field['config']['uploadfolder'] . '/' . $value;
            $fieldArray['params'] = $classRef;
            $value = $this->cObj->IMAGE($fieldArray);
            $type = 'hidden';
            $confImage = (is_array($this->conf['form.']['trashImage.']))?$this->conf['form.']['trashImage.']:array();
            if(!isset($this->conf['form.']['trashImage.']['file'])){
                $confImage['file'] = t3lib_extMgm::siteRelPath('t3registration') . 'res/trash.png';
            }
            $confImage['params'] = 'class="t3registration_pi1_deleteImage" ref="t3registration_pi1_ref_' . $field['name'] . '_' . $counter . '"';
            $confImage['altText'] = $this->pi_getLL('deleteImageConfirmation');
            $confImage['titleText'] = $this->pi_getLL('deleteImage');
            $trash = $this->cObj->IMAGE($confImage);
            $htmlBlock = $value . $trash;
        }

        $htmlBlock .= sprintf('<input type="%s" %s name="%s" %s/>',$type,$classRef,$name,$hiddenValue);
        return $htmlBlock;
    }

    /**
     * This function replaces the subparts in preview mode.
     * @param $field the field configuration
     * @param $content the html string that contains the markers
     * @param $contentArray
     * @return string the field preview HTML code
     */
    public function getAndReplaceSubpartPreview($field,$content,$contentArray){
        if(isset($field['config']['internal_type']) && $field['config']['internal_type'] == 'file'){
            $images = explode(',',$this->piVars[$field['name']]);
            $imageList = array();
            foreach($images as $image){
                $fieldArray = (isset($this->conf[$field['name'] . '.']) && is_array($this->conf[$field['name'] . '.']))?$this->conf[$field['name'] . '.']:array();
                $fieldArray['file'] = $field['config']['uploadfolder'] . '/' . $image;
                $imageList[] = $this->cObj->IMAGE($fieldArray);
            }
            $contentArray['###' . strtoupper($field['name']) . '_LABEL###'] = ($field['hideInChangeProfile'] == 1 && $GLOBALS['TSFE']->loginUser)?'':$this->pi_getLL($field['name'] . 'Label');
            $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = ($field['hideInChangeProfile'] == 1 && $GLOBALS['TSFE']->loginUser)?'':implode('',$imageList);
        }
        else{
            $contentArray['###' . strtoupper($field['name']) . '_LABEL###'] = ($field['hideInChangeProfile'] == 1 && $GLOBALS['TSFE']->loginUser)?'':$this->pi_getLL($field['name'] . 'Label');
            //call $this->htmlentities to remove xss scripting side
            $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = ($field['hideInChangeProfile'] == 1 && $GLOBALS['TSFE']->loginUser)?'':$this->htmlentities($this->piVars[$field['name']]);
        }
        return $contentArray;
    }

    /**
     * This function gets the error subpart for the passed field and then it replaces the error marker with the error description.
     * @param $field the field configuration
     * @param $content the html string that contains the markers
     * @return string the HTML code with the error description
     * @tested 20111017
     */
    private function getErrorSubpart($field,$content){
        $errorContent = $this->cObj->getSubpart($content,'###ERROR_FIELD###');
        //needs because fields data remove dot and erroWrap. becomes errorWrap
        $field['errorWrap.'] = $this->conf['fieldConfiguration.'][$field['name'] . '.']['errorWrap.'];
        if(!isset($field['errorWrap.']) || !is_array($field['errorWrap.'])) {
            $field['errorWrap.'] = (is_array($this->conf['errors.']['standardErrorStdWrap.']))?$this->conf['errors.']['standardErrorStdWrap.']:array();
        }
        $singleError = (isset($field['singleErrorEvaluate']))?$field['singleErrorEvaluate']:$this->conf['errors.']['singleErrorEvaluate'];
        //fetch each single error description
        if(is_array($this->errorArray['errorDescription'][$field['name']]) && $singleError){
            $errorDescriptionArray = array();
            foreach($this->errorArray['errorDescription'][$field['name']] as $singleErrorDescription){
                $errorDescriptionArray[] = $this->cObj->stdWrap($this->pi_getLL($field['name'] . ucfirst($singleErrorDescription) . 'Error'),$field['errorWrap.']);
            }
            return preg_replace('/###ERROR_LABEL###/',implode('', $errorDescriptionArray),$errorContent);
        }
        else{
            return preg_replace('/###ERROR_LABEL###/',$this->cObj->stdWrap($this->pi_getLL($field['name'] . 'Error'),$field['errorWrap.']),$errorContent);
        }
    }

    /**
     * This function prepares the email to send user for deleting and create the auth code for authenticate the confirmation
     * Extract the "T3REGISTRATION_DELETE_SENTEMAIL" marker subpart and create the email body
     * @return string the registration final HTML template
     */
    private function emailDeletionSent(){
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content,'T3REGISTRATION_DELETE_SENTEMAIL');
        if(isset($GLOBALS['TSFE']->fe_user->user['uid'])){
            $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users','uid=' . $GLOBALS['TSFE']->fe_user->user['uid']);
            if($GLOBALS['TYPO3_DB']->sql_num_rows($resource)>0){
                $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                $user['user_auth_code'] = md5('delteAuth' . time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid='. $GLOBALS['TSFE']->fe_user->user['uid'],$user);

                foreach($this->fieldsData as $field){
                    $valueArray['###' . strtoupper($field['name']) . '###'] = $this->htmlentities($user[$field['field']]);
                }
                $contentArray['###DELETE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('textAfterDeleteRequest'),$valueArray);
                $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
                $this->prepareAndSendEmailSubpart('deleteRequest',$user);
            }
        }
        else{
            //TODO sistemare per recuperare lingua delle traduzioni
            $contentArray['###DELETE_TEXT###'] = 'user have to be logged';
            $contentArray['###SIGNATURE###'] = '';
        }
        return $this->cObj->substituteMarkerArrayCached($content,$contentArray);
    }

    /**
     * This function call the insertUser method.
     * @return string the registration final HTML template
     */
    private function endRegistration(){
        $content = $this->getTemplate();
        foreach($this->fieldsData as $field){
            $valueArray['###' . strtoupper($field['name']) . '###'] = htmlspecialchars($this->piVars[$field['name']]);
        }
        if($GLOBALS['TSFE']->loginUser){
            $this->updateUserProfile();
            $content = $this->cObj->getSubpart($content,'T3REGISTRATION_ENDUPDATEPROFILE');
            $contentArray['###UPDATE_PROFILE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('finalUpdateProfileText'),$valueArray);
            $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        }
        else{
            $this->insertUser();
            $content = $this->cObj->getSubpart($content,'T3REGISTRATION_ENDREGISTRATION');
            $contentArray['###FINAL_REGISTRATION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('finalRegistrationText'),$valueArray);
            $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        }
        return $this->cObj->substituteMarkerArrayCached($content,$contentArray);
    }

    /**
     * This method insert user in fe_users database table. If automatic password generation is set to true and no password is set by the user, a new
     * password is automatically generated. It also calls the methods for sending emails.
     * @return void
     */
    private function insertUser(){
        if($this->conf['passwordGeneration'] || !isset($this->piVars['password']) || strlen($this->piVars['password'])==0){
            $this->piVars['password'] = substr(md5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']),0,8);
        }
        $user = array();
        $user['usergroup'] = $this->conf['preUsergroup'];
        foreach($this->fieldsData as $field){
            if($field['type'] == 'databaseField' && (!isset($field['noHTMLEntities']) || (isset($field['noHTMLEntities']) && $field['noHTMLEntities'] == 1))){
                $user[$field['field']] = $this->htmlentities($this->piVars[$field['name']]);
            }
            else{
                $user[$field['field']] = $this->piVars[$field['name']];
            }
        }
        $user['username'] = $this->getUsername();
        $user['pid'] = ($this->conf['userFolder'])?$this->conf['userFolder']:$GLOBALS['TSFE']->id;
        $user = $this->setAuthCode($user);
        //TODO mettere un controllo per verificare che si possa disabilitare l'utente oppure mantenerlo abilitato
        $user['disable'] = ($this->conf['disabledBeforeConfirmation'] || $this->userAuth)?1:0;
        $user['crdate'] = time();
        $user['tstamp'] = $user['crdate'];
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeInsertUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeInsertUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
                $user = $params['user'];
            }
        }
        $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users',$user);
        $user['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterInsertUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterInsertUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
            }
        }
        if($this->userAuth){
            //send email
            $this->prepareAndSendEmailSubpart('confirmationRequest',$user);
        }
        if($this->adminAuth){
            //send email
            $this->prepareAndSendEmailSubpart('authorizationRequest',$user);
        }

    }



    /**
     * This method insert user in fe_users database table. If automatic password generation is set to true and no password is set by the user, a new
     * password is automatically generated. It also calls the methods for sending emails.
     * @return void
     */
    private function updateUserProfile(){
        if($GLOBALS['TSFE']->loginUser){
            foreach($this->fieldsData as $field){
                if($field['type'] == 'databaseField'){
                    $user[$field['field']] = $this->htmlentities($this->piVars[$field['name']]);
                }
            }
            //Inserire hook per aggiornare i campi
            $user['tstamp'] = time();
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateUser']))    {
                foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateUser'] as $userFunction)    {
                    $params['user'] = $user;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                    $user = $params['user'];
                }
            }
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid=' . $GLOBALS['TSFE']->fe_user->user['uid'],$user);

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateUser']))    {
                foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateUser'] as $userFunction)    {
                    $params['user'] = $user;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                }
            }
        }
    }

    /**
     * This function returns the username. If no one is specified by the user, it automatically generates a username.
     * @return string username
     */
    private function getUsername(){
        if (isset($this->piVars[$this->conf['usernameField']])){
            return $this->piVars[$this->conf['usernameField']];
        }
        else{
            return 'user-' . md5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
        }
    }

    /**
     * This function...
     * @param $user
     * @return array user
     */
    protected function setAuthCode($user){
        $authProcessList = explode(',',$this->conf['approvalProcess']);
        foreach($authProcessList as $process){
            switch($process){
                case 'doubleOptin':
                    $user['user_auth_code'] = md5('doubleOptin' . time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                    $this->userAuth = true;
                    break;
                case 'adminApproval':
                    $user['admin_auth_code'] = md5('adminApproval' . time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                    $this->adminAuth = true;
                    $user['admin_disable'] = 1;
                    break;
                    //TODO eventuale hook per codice personalizzato
            }
        }
        return $user;
    }


    /**
     * This function calls the methods for preparing and for sending the email.
     * @param $action the action to be performed. Possible values can be "deleteRequest", "confirmationRequest" or "authorizationRequest".
     * @param $user the target user of the email
     * @return void
     */
    protected function prepareAndSendEmailSubpart($action,$user){
        switch($action){
            case 'deleteRequest':
                $this->sendEmail($this->deleteEmail($user),$user,'mailToUserDeleteSubject');
                break;
            case 'confirmationRequest':
                $this->sendEmail($this->confirmationEmail($user),$user,'mailToUserSubject');
                break;
            case 'authorizationRequest':
                $this->sendEmail($this->authorizationEmail($user),$user,'mailToAdminSubject');
                break;
        }
    }



    /**
     * This function cheks if you're into change profile process
     * @return boolean true if user is in profile, false otherwise
     */
    protected function changeProfileCheck(){
        if($GLOBALS['TSFE']->loginUser && !isset($this->piVars['submitted']) && !isset($this->piVars['sendConfirmation'])){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * This function is called before getForm if user is logged, so it merge data from database into piVars, only if form is not submitted
     * @return string the form HTML code
     */
    protected function showProfile(){
        $user = $GLOBALS['TSFE']->fe_user->user;
        foreach($this->fieldsData as $field){
            if(isset($user[$field['field']])){
                $this->piVars[$field['name']] = $user[$field['field']];
            }
            elseif(isset($field['config']['fetchDataHook'])){
                $params = array();
                $params['user'] = $user;
                $params['piVars'] = $this->piVars;
                $this->piVars[$field['name']] = t3lib_div::callUserFunction($field['config']['fetchDataHook'], $params, $this);
            }
        }
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['profileFetchData']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['profileFetchData'] as $fieldFunction)    {
                $params = array('fields' => $this->fieldsData,'user' => $user,'data' => $this->piVars);
                $this->piVars = t3lib_div::callUserFunction($fieldFunction, $params, $this);
            }
        }
        return $this->getForm();
    }


    /**
     *
     * @return unknown_type
     */
    protected function showOnAutoLogin(){
        $sessionData = $GLOBALS['TSFE']->fe_user->getSessionData('autoLogin');
        if(isset($sessionData)){
            return $sessionData['text'];
        }
        else{
            $markerArray = array();
            $content = $this->getTemplate();
            $content = $this->cObj->getSubpart($content,'###T3REGISTRATION_CONFIRMEDONREDIRECT###');
            foreach($this->fieldsData as $field){
                $markerArray['###' . strtoupper($field['name']) . '###'] = $GLOBALS['TSFE']->fe_user->user[$field['field']];
            }
            $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('ConfirmedOnRedirectText'),$markerArray);
            $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
            return $this->cObj->substituteMarkerArrayCached($content,$markerArray);
        }
    }

    /**
     * This function checks if the user can be confirmed and it calls the method updateConfirmedUser for updating the user into database.
     * @return boolean true if the user was been correctly confirmed, false otherwise
     */
    protected function confirmUserDeletion(){
        $userAuthCode = $this->piVars['authcode'];
        $where = 'user_auth_code=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($userAuthCode,'fe_users');
        $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users',$where);
        if($GLOBALS['TYPO3_DB']->sql_num_rows($resource)>0){
            $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
            $this->deleteUser($user);
            $content = $this->getTemplate();
            $content = $this->cObj->getSubpart($content,'###T3REGISTRATION_DELETE_CONFIRMATION###');
            foreach($user as $key => $value){
                $markerArray['###' . strtoupper($key) . '###'] = $value;
            }
            $contentArray['###DELETE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deletionConfirmedText'),$markerArray);
            $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
            return $this->cObj->substituteMarkerArrayCached($content,$contentArray);
        }
        else{
            //@todo sistemare la gestione d'errore
            debug('user not found confirmUserDeletion');
        }
    }

    /**
     * This function delete user from database
     * @return boolean true if the user was been correctly confirmed, false otherwise
     */
    protected function deleteUser($user){
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeDeleteUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeDeleteUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
                $user = $params['user'];
            }
        }
        if(isset($this->conf['delete.']['deleteRow']) && $this->conf['delete.']['deleteRow']){
            $GLOBALS['TYPO3_DB']->exec_DELETEquery('fe_users','uid=' . $user['uid']);
        }
        else{
            $user['disable'] = 1;
            $user['deleted'] = 1;
            $user['tstamp'] = time();
            $user['user_auth_code'] = '';
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid='. $user['uid'],$user);
        }
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterDeleteUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterDeleteUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
            }
        }
    }

    /**
     *
     * @return unknown_type
     */
    protected function confirmationProcessControl(){
        switch($this->externalAction['parameter']){
            case 'userAuth':
                $userAuthCode = $this->piVars['authcode'];
                $where = 'user_auth_code=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($userAuthCode,'fe_users');
                $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users',$where);
                if($GLOBALS['TYPO3_DB']->sql_num_rows($resource)>0){
                    $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                    //call confirmation
                    $content = $this->confirmUser($user);
                    if(strlen($user['admin_auth_code']) == 0){
                        if($this->conf['autoLoginAfterConfirmation'] == 1){
                            $this->autoLogin($user['uid']);
                            $sessionData = array(
                                'text' => $content
                            );
                            $GLOBALS['TSFE']->fe_user->setAndSaveSessionData('autoLogin',$sessionData);
                            $redirectParametersArray = array(
                            $this->prefixId.'[' . 'action' . ']' => 'redirectOnLogin'
                            );
                            $redirectLink = $this->pi_getpageLink($GLOBALS['TSFE']->id,'',$redirectParametersArray);
                            header('Location: ' . t3lib_div::locationHeaderUrl($redirectLink));
                            exit;
                        }
                    }
                }else{
                    //@todo utente non trovato scrivere errore
                }
                break;
            case 'adminAuth':
                $adminAuthCode = $this->piVars['authcode'];
                $where = 'admin_auth_code=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($adminAuthCode,'fe_users');
                $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users',$where);
                if($GLOBALS['TYPO3_DB']->sql_num_rows($resource)>0){
                    $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                    $content = $this->authorizedUser($user);
                }else{
                    //@todo utente non trovato scrivere errore
                }
                break;
        }
        return $content;
    }

    /**
     * This function checks if the user can be confirmed and it calls the method updateConfirmedUser for updating the user into database.
     * @return boolean true if the user was been correctly confirmed, false otherwise
     */
    protected function confirmUser($user){
        $user = $this->updateConfirmedUser($user);
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content,'###T3REGISTRATION_CONFIRMEDUSER###');
        foreach($user as $key => $value){
            $markerArray['###' . strtoupper($key) . '###'] = $value;
        }
        $confirmationTex = (strlen($user['admin_auth_code']) == 0)?$this->pi_getLL('confirmationFinalText'):$this->pi_getLL('confirmationWaitingAuthText');
        $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($confirmationTex,$markerArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        return $this->cObj->substituteMarkerArrayCached($content,$markerArray);
    }


    /**
     * This function confirms the user by updating the user record into fe_users database table.
     * @param $user
     * @return void
     */
    protected function updateConfirmedUser($user){
        //put hook before
        $groupsBeforeConfirmation = explode(',',$this->conf['preUsergroup']);
        $groupsAfterConfirmation = explode(',',$this->conf['postUsergroup']);
        $usergroup = explode(',',$user['usergroup']);
        $newUserGroup=array();
        foreach($usergroup as $group){
            if(!in_array($group,$groupsBeforeConfirmation)){
                $newUserGroup[] = $group;
            }
        }
        foreach($groupsAfterConfirmation as $group){
            if(!in_array($group,$newUserGroup)){
                $newUserGroup[] = $group;
            }
        }
        $user['user_auth_code'] = '';
        $user['usergroup'] = implode(',',$newUserGroup);
        $user['tstamp'] = time();
        if(strlen($user['admin_auth_code']) == 0){
            $user['disable'] = 0;
            if($this->conf['autoLoginAfterConfirmation'] == 1){
                $this->autoLogin($user['uid']);
            }
        }
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateConfirmedUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateConfirmedUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
                $user = $params['user'];
            }
        }
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid=' . $user['uid'],$user);
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateConfirmedUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateConfirmedUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
            }
        }
        return $user;


    }

    /**
     * This function checks if the user can be authorized and it calls the method updateAdminAuthorizedUser for updating the user into database.
     * @return boolean true if the user was been correctly confirmed, false otherwise
     */
    protected function authorizedUser($user){
        $this->updateAdminAuthorizedUser($user);
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content,'###T3REGISTRATION_CONFIRMEDAUTHORIZEDUSER###');
        foreach($user as $key => $value){
            $markerArray['###' . strtoupper($key) . '###'] = $value;
        }
        $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAuthorizationText'),$markerArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        return $this->cObj->substituteMarkerArrayCached($content,$markerArray);
    }

    /**
     *This function authorizes user by upadating the user record into fe_users database table.
     * @param $user the user to be authorized
     * @return void
     */
    protected function updateAdminAuthorizedUser($user){
        $user['admin_auth_code'] = '';
        $user['admin_disable'] = 0;
        $user['uid'] = $user['uid'];
        if(strlen($user['user_auth_code']) == 0){
            $user['disable'] = 0;
        }
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeAdminAuthorizedUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeAdminAuthorizedUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
                $user = $params['user'];
            }
        }
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid=' . $user['uid'],$user);
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterAdminAuthorizedUser']))    {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterAdminAuthorizedUser'] as $userFunction)    {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
            }
        }
        //send email to user after Authorization
        if(!$user['disable']  && $this->conf['sendUserEmailAfterAuthorization']){
            $message = $this->sendAdviceAfterAuthorization($user);
            $this->sendEmail($message,$user,'mailToUserAfterAuthorizationSubject');
        }

    }

    /**
     * This function makes auto login form confirmed user
     * @param $uid id of confirmed user
     * @return void
     */
    protected function autoLogin($uid){
        $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users','uid=' . $uid);
        if(($feUser = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($resource)) !== FALSE){
            $loginData=array(
                'uname' => $feUser['username'], //username
                'uident'=> $feUser['password'], //password
                'status' =>'login'
                );
                //do not use a particular pid
                $GLOBALS['TSFE']->fe_user->checkPid=0;
                $info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
                $user = $GLOBALS['TSFE']->fe_user->fetchUserRecord(
                $info['db_user'] ,$loginData['uname']);
                if($GLOBALS['TSFE']->fe_user->compareUident($user, $loginData)) {
                    //login successfull
                    $GLOBALS['TSFE']->fe_user->createUserSession($user);
                    $GLOBALS["TSFE"]->fe_user->loginSessionStarted = TRUE;
                    $GLOBALS["TSFE"]->fe_user->user = $GLOBALS["TSFE"]->fe_user->fetchUserSession();
                }
        }
    }


    /**
     * This function shows the delete link.
     * @return string the link HTML code
     */
    protected function showDeleteLink(){
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content,'###T3REGISTRATION_DELETE###');
        foreach($GLOBALS['TSFE']->fe_user->user as $key => $value){
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $deleteArray = array(
        $this->prefixId . '[action]' => 'delete'
        );
        $link = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$deleteArray);
        $link = sprintf('<a href="%s">%s</a>',$link,$this->pi_getLL('deleteLinkText'));
        $contentArray['###DELETE_LINK###'] = $link;
        $valueArray['###DELETE_LINK###'] = $link;
        $contentArray['###DELETE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deleteDescriptionText'),$valueArray);
        return $this->cObj->substituteMarkerArrayCached($content,$contentArray);
    }




    /*******************EMAIL FUNCTION****************/

    /**
     * This function prepares the user deletion email message.
     * @param $user the target user of the email
     * @return array the type and the HTML content of the message.
     */
    protected function deleteEmail($user){
        $confirmationPage = ($this->conf['deletePage'])?$this->conf['deletePage']:$GLOBALS['TSFE']->id;
        $confirmationArray = array(
        $this->prefixId.'[' . 'action' . ']' => 'userDeleteConfirmation',
        $this->prefixId.'[' . 'authcode' . ']' => $user['user_auth_code']
        );
        $authLink = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getpageLink($confirmationPage,'',$confirmationArray);
        $authLink = sprintf('<a href="%s">%s</a>',$this->htmlentities($authLink),$this->htmlentities($this->pi_getLL('deleteLinkConfirmationText')));
        foreach($this->fieldsData as $field){
            $markerArray['###' . strtoupper($field['name']) . '###'] = $this->piVars[$field['name']];
        }
        $markerArray['###DELETE_LINK###'] = $authLink;
        foreach($user as $key => $value){
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray['###DELETE_LINK###'] = $authLink;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deleteTextHmtl'),$valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deleteTextText'),$valueArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        $message = $this->prepareEmailContent('###T3REGISTRATION_DELETE_EMAIL_HTML###','###T3REGISTRATION_DELETE_EMAIL_TEXT###',$markerArray);
        $message['type'] = 'user';
        return $message;
    }

/**
     * This function prepares email message to advice the user about admin authorization.
     * @param $user the target user of the email
     * @return array the type and the HTML content of the message.
     */
    protected function sendAdviceAfterAuthorization($user){
        foreach($user as $key => $value){
            $markerArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray = $markerArray;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAfterAuthorizationTextHmtl'),$valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAfterAuthorizationTextText'),$valueArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        $message = $this->prepareEmailContent('###T3REGISTRATION_CONFIRMEDONADMINAPPROVAL_EMAIL_HTML###','###T3REGISTRATION_CONFIRMEDONADMINAPPROVAL_EMAIL_TEXT###',$markerArray);
        $message['type'] = 'user';
        return $message;
    }

    //TODO le due funzioni confirmationEmail e authorizationEmail sono identiche, unirle

    /**
     * This function prepares the user confirmation email message.
     * @param $user the target user of the email
     * @return array the type and the HTML content of the message.
     */
    protected function confirmationEmail($user){
        $confirmationPage = ($this->conf['confirmationPage'])?$this->conf['confirmationPage']:$GLOBALS['TSFE']->id;
        $confirmationArray = array(
        $this->prefixId.'[' . 'action' . ']' => 'userAuth',
        $this->prefixId.'[' . 'authcode' . ']' => $user['user_auth_code']
        );
        $authLink = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getpageLink($confirmationPage,'',$confirmationArray);
        $authLink = sprintf('<a href="%s">%s</a>',$this->htmlentities($authLink),$this->htmlentities($this->pi_getLL('confirmLinkConfirmationText')));
        foreach($this->fieldsData as $field){
            $markerArray['###' . strtoupper($field['name']) . '###'] = $this->piVars[$field['name']];
        }
        $markerArray['###CONFIRMATION_LINK###'] = $authLink;
        foreach($user as $key => $value){
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray['###CONFIRMATION_LINK###'] = $authLink;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationTextHtml'),$valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationTextText'),$valueArray);
        $message = $this->prepareEmailContent('###T3REGISTRATION_CONFIRMATION_EMAIL_HTML###','###T3REGISTRATION_CONFIRMATION_EMAIL_TEXT###',$markerArray);
        $message['type'] = 'user';
        return $message;
    }

    /**
     * This function prepares the administrator confirmation email message.
     * @param $user the target user of the email
     * @return array the type and the HTML content of the message.
     */
    protected function authorizationEmail($user){
        $confirmationPage = ($this->conf['confirmationPage'])?$this->conf['confirmationPage']:$GLOBALS['TSFE']->id;
        $confirmationArray = array(
        $this->prefixId.'[' . 'action' . ']' => 'adminAuth',
        $this->prefixId.'[' . 'authcode' . ']' => $user['admin_auth_code']
        );
        $authLink = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getpageLink($confirmationPage,'',$confirmationArray);
        $authLink = sprintf('<a href="%s">%s</a>',$this->htmlentities($authLink),$this->htmlentities($this->pi_getLL('authorizationLinkConfirmationText')));
        foreach($this->fieldsData as $field){
            $markerArray['###' . strtoupper($field['name']) . '###'] = $this->piVars[$field['name']];
        }
        $markerArray['###CONFIRMATION_LINK###'] = $authLink;
        foreach($user as $key => $value){
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray['###CONFIRMATION_LINK###'] = $authLink;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAuthorizationEmailTextHtml'),$valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAuthorizationEmailTextText'),$valueArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        $message = $this->prepareEmailContent('###T3REGISTRATION_AUTHORIZATION_EMAIL_HTML###','###T3REGISTRATION_AUTHORIZATION_EMAIL_TEXT###',$markerArray);
        $message['type'] = 'admin';
        return $message;
    }

    /**
     * This function prepare the email content based on value from flexform
     * @param $subpartHTMLMarker string for HTML marker
     * @param $subpartTextMarker string for Text marker
     * @param $markers array of marker to substitute
     * @return array with content HTML and text parts
     */
    private function prepareEmailContent($subpartHTMLMarker,$subpartTextMarker,$markers){
        $content = $this->getTemplate();
        $contentText = $this->cObj->getSubpart($content,$subpartTextMarker);
        $contentHTML = $this->cObj->getSubpart($content,$subpartHTMLMarker);
        $message = array();
        if(strlen($contentHTML)>0 && ($this->emailFormat & HTML)){
            $message['contentHTML'] = $this->cObj->substituteMarkerArray($contentHTML,$markers);
        }
        if(strlen($contentText)>0 && ($this->emailFormat & TEXT)){
            $message['contentText'] = $this->cObj->substituteMarkerArray($contentText,$markers);
        }
        return $message;
    }

    /**
     *This function prepares and sends the email. It can be sent to the user or to the administrator.
     * @param $message the message content of the email
     * @param $user the target user of the email
     * @return void
     */
    private function sendEmail($message,$user,$subject){
        $mailObject = t3lib_div::makeInstance('t3lib_mail_Message');
        if(isset($message['contentText'])){
            $mailObject->addPart($message['contentText'], 'text/plain');
        }
        if(isset($message['contentHTML'])){
            $mailObject->setBody($message['contentHTML'],'text/html');
        }
        switch($message['type']){
            case 'user':
                $mailObject->setSubject($this->pi_getLL($subject));
                $mailObject->setTo(array($user['email']));
                break;
            case 'admin':
                if($this->conf['emailAdmin']){
                    $adminEmailList = explode(',',$this->conf['emailAdmin']);
                    foreach($adminEmailList as $email){
                        $emailAdminTemp = explode(':',$email);
                        if(count($emailAdminTemp) == 2){
                            $emailAdmin[$emailAdminTemp[0]] = $emailAdminTemp[1];
                        }
                        else{
                            $emailAdmin[] = $emailAdminTemp[1];
                        }

                    }
                }
                $mailObject->setSubject($this->pi_getLL($subject));
                $mailObject->setTo($emailAdmin);
                break;
        }
        $emailFrom = ($this->conf['emailFrom'])?$this->conf['emailFrom']:'';
        $emailFromName = ($this->conf['emailFromName'])?$this->conf['emailFromName']:'';
        if($this->conf['emailFromName']){
            $fromArray = array($emailFrom => $emailFromName);
        }
        else{
            $fromArray = array($emailFrom);
        }
        if($this->conf['emailFrom'] && $this->conf['emailAdmin']){
            $mailObject->setFrom(array($emailFrom => $emailFromName))
            ->setBody($message['contentHTML'],'text/html')
            ->addPart($message['contentText'], 'text/plain')
            ->send();
        }
        else{
            throw new t3lib_exception();
        }
    }



    /**
     * This function remove dots from keys of the passed array.
     * @param $sourceArray the array to be modified
     * @return array
     */
    private function removeDotFromArray($sourceArray){
        $finalArray = array();
        foreach ($sourceArray as $key => $item){
            if(is_array($item)){
                $finalArrayKey = preg_replace('/\./','',$key);
                $finalArrayItem = $this->removeDotFromArray($item);
            }
            else{
                $finalArrayItem = $item;
                $finalArrayKey = $key;
            }
            $finalArray[$finalArrayKey] = $finalArrayItem;
        }
        return $finalArray;
    }

    /**
     *
     * @param $field
     * @return void
     */
    protected function getSelectOptionFromConfig($field){

    }

    /**
     * This function execute the replacing of html entities with UTF-8 encoding
     * @param $string
     * @return unknown_type
     */
    private function htmlentities($string) {
        if($GLOBALS['TSFE']->tmpl->setup['config.']['renderCharset']){
            $encoding = $GLOBALS['TSFE']->tmpl->setup['config.']['renderCharset'];
        }
        else{
            $encoding = 'UTF-8';
        }
        return htmlentities($string,ENT_QUOTES,$encoding);
    }

    /**
     * Function to removes all marker into the template after replace process
     * @param string $content content to replace
     * @return string content cleared
     */
    private function removeAllMarkers($content){
        $markers = array();
        $subparts = array();
        preg_match_all('/<!--[\t]*###([A-Z_]*)_FIELD###/U',$content,$matches,PREG_PATTERN_ORDER);
        foreach($matches[1] as $key => $item){
            if(strpos($item,'ERROR')===false){
                if(!in_array($item,$markers)){
                    $subparts['###' . $item . '_FIELD###'] = '';
                }
            }
        }
        preg_match_all('/###([A-Z_]*)_[VALUE|LABEL]*###/U',$content,$matches,PREG_PATTERN_ORDER);
        foreach($matches[1] as $key => $item){
            if(!in_array($item,$markers)){
                $markers['###' . $item . '_VALUE###'] = '';
                $markers['###' . $item . '_LABEL###'] = '';
            }
        }
        $content = $this->cObj->substituteMarkerArrayCached($content,$markers,$subparts);
        return $content;
    }

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/pi1/class.tx_t3registration_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/pi1/class.tx_t3registration_pi1.php']);
}

?>