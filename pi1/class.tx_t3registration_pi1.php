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
 *  138: class tx_t3registration_pi1 extends tslib_pibase
 *  234:     public function main($content, $conf)
 *  362:     protected function init()
 *  374:     protected function initFlexform()
 *  428:     protected function getForm()
 *  565:     public function getAndReplaceSubpart($field, $content)
 *  595:     public function getAndReplaceSubpartPreview($field, $content, $contentArray)
 *  665:     protected function getAutoField($field)
 *  763:     protected function getUploadField($field, $value = '', $counter = '')
 *  798:     protected function endRegistration()
 *  834:     protected function showProfile($user = array())
 *  868:     protected function getErrorSubpart($field, $content)
 *  897:     protected function checkErrors()
 *  914:     public function getEvaluationRulesList($name)
 *  960:     protected function checkField($field)
 *  985:     protected function evaluateField($value, $evaluationRule, $field = array())
 * 1067:     protected function checkUniqueField($value, $field, $folder = 0)
 * 1100:     protected function checkLength($value, $field)
 * 1122:     protected function checkFileUploaded($name, $size, $tmpFile, $field)
 * 1148:     protected function emailDeletionSent($user = array())
 * 1188:     protected function deleteEmail($user)
 * 1218:     protected function sendAdviceAfterAuthorization($user)
 * 1244:     protected function confirmationEmail($user)
 * 1287:     protected function authorizationEmail($user)
 * 1325:     protected function prepareEmailContent($subpartHTMLMarker, $subpartTextMarker, $markers)
 * 1347:     protected function sendEmail($message, $user, $subject)
 * 1460:     public function getMailFormat()
 * 1470:     protected function setEmailFormat()
 * 1492:     protected function argumentsFromUrlCheck()
 * 1532:     protected function prepareAndSendEmailSubpart($action, $user)
 * 1552:     protected function confirmUserDeletion()
 * 1572:     protected function confirmUserDeletionTemplate($user)
 * 1589:     protected function deleteUser($user)
 * 1636:     protected function confirmationProcessControl()
 * 1686:     protected function confirmUser($user)
 * 1706:     protected function updateConfirmedUser($user)
 * 1771:     protected function authorizedUser($user)
 * 1789:     protected function updateAdminAuthorizedUser($user)
 * 1835:     protected function userIsRegistered($lastEvent, $user)
 * 1851:     protected function autoLogin($uid)
 * 1879:     protected function showDeleteLink()
 * 1908:     protected function addFunctionReplace($arrayToTraverse, &$parentArray, $parentKey = '')
 * 1934:     public function getField($name)
 * 1944:     protected function controlIfUsernameIsCorrect()
 * 1969:     protected function controlEmailAndMethod()
 * 1988:     protected function loadTCAField()
 * 2000:     protected function mergeTCAFieldWithConfiguration()
 * 2017:     protected function testUploadFolderField($field)
 * 2031:     protected function getTextToResendConfirmationEmail()
 * 2042:     protected function getTemplate()
 * 2057:     protected function fileFieldTransform2Array($fieldName)
 * 2068:     protected function insertUser()
 * 2145:     protected function sendAgainConfirmationEmail()
 * 2206:     protected function updateUserProfile()
 * 2247:     protected function getUsername()
 * 2262:     protected function setAuthCode()
 * 2287:     protected function changeProfileCheck()
 * 2300:     protected function showOnAutoLogin()
 * 2325:     protected function removeDotFromArray($sourceArray)
 * 2348:     protected function htmlentities($string)
 * 2364:     protected function removeAllMarkers($content)
 * 2391:     protected function testTemplateProcess()
 * 2512:     protected function testGetUser($correct = true)
 *
 * TOTAL FUNCTIONS: 62
 * (This index is automatically created/updated by the extension "extdeveval")
 *
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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('t3registration') . 'library/class.tx_t3registration_checkstatus.php');
require_once(t3lib_extMgm::extPath('t3registration') . 'library/class.tx_t3registration_tcaexternalfunctions.php');


/**
 * Plugin 'Registration' for the 't3registration' extension.
 *
 * @author    Federico Bernardin <federico@bernardin.it>
 * @package    TYPO3
 * @subpackage    tx_t3registration
 */
class tx_t3registration_pi1 extends tslib_pibase {
    var $prefixId = 'tx_t3registration_pi1'; // Same as class name
    var $scriptRelPath = 'pi1/class.tx_t3registration_pi1.php'; // Path to this script relative to the extension dir.
    var $extKey = 't3registration'; // The extension key.
    var $pi_checkCHash = true;

    /**
     * contains fields will be override from ts
     * @var array
     */
    protected $flexformOverrideTs = array('testXMLFile', 'useAnotherTemplateInChangeProfileMode', 'contactEmailMode', 'approvalProcess', 'userFolder', 'templateFile', 'autoLoginAfterConfirmation', 'emailFrom', 'emailFromName', 'emailAdmin', 'stepToTest', 'enableTemplateTest', 'confirmationPage', 'preUsergroup', 'postUsergroup', 'passwordGeneration');

    /**
     * Contains fields with its configuration to rendering form fields
     * @var array
     */
    protected $fieldsData = array();

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

    /**
     * useful in hook to know if it's a change profile process
     * @var array
     */
    protected $changeProfilePath = false;

    /**
     * It can be 1,2 or 3 is binary value and bit 1 is HTML format and bit 2 is TEXT format for mail
     * @var int
     */
    protected $emailFormat = 0;

    /**
     * @var array contains data relative to url parameters
     */
    protected $externalAction;

    /**
     * @var array contains errors data
     */
    public $errorArray;

    /**
     * @var integer level of debugging (0: no debug, 1:devlog, 2:exception with sql query)
     */
    protected $debugLevel = 0;

    /**
     * @var string name of subpart
     */
    public $markerTitle = '';

    /**
     * @var array contains all error description
     */
    protected $fullErrorsList = array();
    
    /**
     * @var array contains data of logged-in user
     */
    protected $feLoggedInUser = array();

    /**
     * @var boolean true if user is logged
     */
    protected $userLogged = false;

    /**
     * This constant contains the maximum code number of exception self managed by extension
     */
    const MAXIMUM_ERROR_NUMBER = 1000;

    /**
     * This constant contains the minimum code number of exception self managed by extension
     */
    const MINIMUM_ERROR_NUMBER = 100;

    const XML_TEST_FILE_ERROR = 110;


    /*******************************MAIN AND INIT FUNCION******************/

    /**
     * The main method of the PlugIn
     *
     * @param    string        $content: The PlugIn content
     * @param    array         $conf: The PlugIn configuration
     * @return    string        that is displayed on the website
     */
    public function main($content, $conf) {
        try {
            if ($this->conf['javascriptsInclusion.']['jquery']) {
                $GLOBALS['TSFE']->additionalHeaderData['t3registrationJQuery'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('t3registration') . 'res/javascript/initialize.js"></script>';
            }
            $this->conf = $conf;

            //set errorLevel
            $this->debugLevel = (isset($this->conf['debugLevel'])) ? $this->conf['debugLevel'] : $this->debugLevel;
            //debug($this->conf);
            $this->pi_setPiVarDefaults();
            $this->pi_loadLL();
            //initialize language object used in label translation from TCA
            $this->init();
            //extract data from flexform
            $this->initFlexform();
            //extract TCA columns from fe_users table
            $this->loadTCAField();
            //adds evaluation additional data
            $this->addFunctionReplace($this->conf['fieldConfiguration.'], $this->conf['fieldConfiguration.'], '');
            //merges data from flexform with ones from ts (after removing dots)
            $fieldsFromTS = $this->removeDotFromArray($this->conf['fieldConfiguration.']);
            $this->fieldsData = t3lib_div::array_merge_recursive_overrule($this->fieldsData, $this->removeUnusedFields($fieldsFromTS));
            //update TCA config fields with fieldsData
            $this->mergeTCAFieldWithConfiguration();
            //Test action from url
            $this->argumentsFromUrlCheck();
            //debug($this->fieldsData);
            $this->setEmailFormat();

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeActionInit'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeActionInit'] as $fieldFunction) {
                    $params = array('fields' => $this->fieldsData, 'data' => $this->piVars, 'content' => $content);
                    $exitFromPlugin = t3lib_div::callUserFunction($fieldFunction, $params, $this);
                    $this->piVars = $params['data'];
                    if ($exitFromPlugin === true && isset($this->conf['exitBeforeActionInitHook']) && $this->conf['exitBeforeActionInitHook'] == $fieldFunction) {
                        $content = $params['content'];
                        return (isset($this->conf['noWrapPlugin']) && $this->conf['noWrapPlugin'] == 1) ? $this->pi_wrapInBaseClass($content) : $content;
                    }
                }
            }
            //call test environment if enabled
            if ($this->conf['enableTemplateTest']) {
                $content = $this->testTemplateProcess();
            } else {
                //debug($this->piVars,'piVars');
                switch ($this->conf['showtype']) {
                    case 'sendConfirmationEmail':
                        $content = $this->sendAgainConfirmationEmail();
                        break;
                    case 'delete':
                        if ($this->userLogged) {
                            if ($this->externalAction['active']) {
                                $content = $this->{$this->externalAction['type']}();
                            } else {
                                $content = $this->showDeleteLink();
                            }

                        } else {
                            if ($this->debugLevel) {
                                if (TYPO3_DLOG) {
                                    t3lib_div::devLog('showtype is delete, but user is not logged, nothing is shown.', $this->extKey, 2);
                                }
                            }
                        }
                        break;
                    case 'edit':
                        if ($this->userLogged) {
                            if (!isset($this->piVars['submitted']) && !isset($this->piVars['sendConfirmation'])) {
                                $content = $this->showProfile();
                            } else {
                                $content = $this->getForm();
                            }
                        } else {
                            if ($this->debugLevel) {
                                if (TYPO3_DLOG) {
                                    t3lib_div::devLog('showtype is edit, but user is not logged, nothing is shown.', $this->extKey, 2);
                                }
                            }
                        }
                        break;
                    case 'auto':
                    default:
                        if ($this->externalAction['active']) {
                            //operation from url
                            if ($this->externalAction['location'] == 'local') {
                                $content = $this->{$this->externalAction['type']}();
                            } else {
                                $params = array('data' => $this->piVars);
                                $content = t3lib_div::callUserFunction($this->externalAction['type'], $params, $this);
                            }
                        } elseif ($this->changeProfileCheck()) {
                            $this->changeProfilePath = true;
                            $content = $this->showProfile();
                        }
                        else {
                            $content = $this->getForm();
                        }
                        break;
                }
            }
            $content = $this->removeAllMarkers($content);
            if ($this->conf['debuggingMode'] && t3lib_div::cmpIP(t3lib_div::getIndpEnv('REMOTE_ADDR'), $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'])) {
                $checkClass = t3lib_div::makeInstance('tx_t3registration_checkstatus');
                $checkClass->initialize($this, $this->fieldsData);
                $content = $checkClass->main();
            } else {
                $error = '';
                $checkApprovalProcess = $this->controlEmailAndMethod();
                if ($checkApprovalProcess !== true) {
                    $checkClass = t3lib_div::makeInstance('tx_t3registration_checkstatus');
                    $checkClass->initialize($this, $this->fieldsData);
                    $error .= $checkClass->getMessage($this->pi_getLL('approvalProcessConfigurationError'), $checkApprovalProcess, 'error');
                }
                $checkUsername = $this->controlIfUsernameIsCorrect();
                if ($checkUsername !== true) {
                    $checkClass = t3lib_div::makeInstance('tx_t3registration_checkstatus');
                    $checkClass->initialize($this, $this->fieldsData);
                    $error .= $checkClass->getMessage($this->pi_getLL('usernameConfigurationError'), $checkUsername, 'error');
                }
                $content = ($error) ? $error : $content;
            }
            return $this->pi_wrapInBaseClass($content);
        } catch (t3lib_exception $exception) {
            $code = $exception->getCode();
            if ($code > self::MINIMUM_ERROR_NUMBER && $code < self::MAXIMUM_ERROR_NUMBER) {
                $checkClass = t3lib_div::makeInstance('tx_t3registration_checkstatus');
                $checkClass->initialize($this, $this->fieldsData);
                $content = $checkClass->getMessage($this->pi_getLL('exceptionalError'), $this->pi_getLL($exception->getMessage()), 'error');
                return $this->pi_wrapInBaseClass($content);
            }
        }
    }


    /**
     * This function initializes the system
     *
     * @return    void
     */
    protected function init() {
        //initialize the language class to extract translation for label outside the actual plugin (example cms fe_users label)
        $this->languageObj = t3lib_div::makeInstance('language');
        //sets the correct language index
        $this->languageObj->init($this->LLkey);
        // Initialize the feLoggedIn data array
        if ($GLOBALS['TSFE']->loginUser) {
        	$this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
            $this->userLogged = true;
        }
        /*This hook could be called to update user data*/
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['userLogged'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['userLogged'] as $fieldFunction) {
                $params = array('user' => $this->feLoggedInUser, 'piVars' => $this->piVars, 'userLogged' => $this->userLogged);
                t3lib_div::callUserFunction($fieldFunction, $params, $this);
                $this->feLoggedInUser = $params['user'];
                $this->userLogged = $params['userLogged'];
                $this->piVars = $params['piVars'];
            }
        }
    }

    protected function preElaborateData($user){

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['profileFetchData'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['profileFetchData'] as $fieldFunction) {
                $params = array('fields' => $this->fieldsData, 'user' => $user, 'data' => $this->piVars);
                $this->piVars = t3lib_div::callUserFunction($fieldFunction, $params, $this);
            }
        }
        foreach ($this->fieldsData as $field) {
            if(!isset($this->conf['fieldConfiguration.'][$field['name'] . '.']['preElaborateDateDisable']) || !$this->conf['fieldConfiguration.'][$field['name'] . '.']['preElaborateDateDisable']){
                $evaluationRulesList = $this->getEvaluationRulesList($field['name']);
                if(in_array('date',$evaluationRulesList) && $user[$field['field']] && (!version_compare(phpversion(), '5.3', '<'))){
                    if(($date = $this->getDateFromTimestamp($user[$field['field']],$field['name'])) !== false){
                        $this->piVars[$field['field']] = $date;
                    }

                }
            }
        }
    }

    protected function getDateFromTimestamp($timestamp,$fieldname){
        $timezone = ($this->fieldsData[$fieldname]['config']['date']['timezone'])?:'UTC';
        date_default_timezone_set($timezone);
        if(isset($this->fieldsData[$fieldname]['config']['date']['strftime']) && $this->fieldsData[$fieldname]['config']['date']['strftime']){
            return date($this->fieldsData[$fieldname]['config']['date']['strftime'],$timestamp);
        }
        else{
            return false;
        }
    }

    protected function getTimestampFromDate($date,$field){
        $timezone = ($field['config']['date']['timezone'])?:'UTC';
        date_default_timezone_set($timezone);
        if(isset($field['config']['date']['strftime'])){
            $parsedArray = date_parse_from_format($field['config']['date']['strftime'], $date);
            if($parsedArray['error_count'] == 0){
                if($parsedArray['warning_count'] >0){
                    return false;
                }
                else{
                    $timestamp = mktime(0,0,0,$parsedArray['month'],$parsedArray['day'],$parsedArray['year']);
                    return $timestamp;
                }
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    protected function postElaborateData(){

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['postElaborateData'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['postElaborateData'] as $fieldFunction) {
                $params = array('fields' => $this->fieldsData, 'data' => $this->piVars);
                $this->piVars = t3lib_div::callUserFunction($fieldFunction, $params, $this);
            }
        }
        foreach ($this->fieldsData as $field) {
            if(!isset($this->conf['fieldConfiguration.'][$field['name'] . '.']['preElaborateDateDisable']) || !$this->conf['fieldConfiguration.'][$field['name'] . '.']['preElaborateDateDisable']){
                $evaluationRulesList = $this->getEvaluationRulesList($field['name']);
                if(in_array('date',$evaluationRulesList) && $this->piVars[$field['field']]){
                    if(($date = $this->getTimestampFromDate($this->piVars[$field['field']],$field)) !== false){
                        $this->piVars[$field['field']] = $date;
                    }

                }
            }
        }
    }

    /**
     * This function fetches flex data from flex form plugin and merge data into $this conf array.
     *
     * @return    void
     */
    protected function initFlexform() {
        $fieldsList = array();
        $this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
        $this->lConf = array(); // Setup our storage array...
        // Assign the flexform data to a local variable for easier access
        $piFlexForm = $this->cObj->data['pi_flexform'];
        // Traverse the entire array based on the language...
        // and assign each configuration option to $this->lConf array...
        if (is_array($piFlexForm['data'])) {
            foreach ($piFlexForm['data'] as $sheet => $data) {
                foreach ($data as $lang => $value) {
                    foreach ($value as $key => $val) {
                        $flexformValue = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
                        if (in_array($key, $this->flexformOverrideTs) && $flexformValue) {
                            $this->conf[$key] = $flexformValue;
                        } else {
                            if (!array_key_exists($key, $this->conf) || !$flexformValue) {
                                $lConf[$key] = $flexformValue;
                            }
                        }
                    }
                }
            }
            if (isset($piFlexForm['data']['fieldsSheet']['lDEF']['fields']['el']) && is_array($piFlexForm['data']['fieldsSheet']['lDEF']['fields']['el'])) {
                foreach ($piFlexForm['data']['fieldsSheet']['lDEF']['fields']['el'] as $item) {
                    foreach ($item as $key => $val) {
                        if (($key == 'databaseField' || $key == 'freeField') && ((isset($val['el']['name']['vDEF']) && strlen($val['el']['name']['vDEF']) > 0) || (isset($val['el']['field']['vDEF']) && strlen($val['el']['field']['vDEF']) > 0))) {
                            $name = (isset($val['el']['name']['vDEF']) && strlen($val['el']['name']['vDEF']) > 0) ? $val['el']['name']['vDEF'] : $val['el']['field']['vDEF'];
                            $fieldsList[] = $name;
                            $this->fieldsData[$name] = array();
                            $this->fieldsData[$name]['type'] = $key;
                            foreach ($val['el'] as $fieldProperty => $fieldValue) {
                                $this->fieldsData[$name][$fieldProperty] = $fieldValue['vDEF'];
                            }
                            $this->fieldsData[$name]['name'] = ($this->fieldsData[$name]['name']) ? $this->fieldsData[$name]['name'] : $this->fieldsData[$name]['field'];
                        }
                    }
                }
            }

            $lConf['fields'] = implode(',', $fieldsList);
            //merge lconf (flexform array data) with this->conf (typoscript data and flexformoverridets key)
            $this->conf = t3lib_div::array_merge_recursive_overrule($lConf, $this->conf);
        }
    }

    /***************************************************FORM AND FIELDS MANAGEMENT********************/

    /**
     * This function manages the render of form or preview infos
     *
     * @return    string        the form HTML
     */
    protected function getForm() {
        $content = $this->getTemplate();
        $preview = false;
        $error = false;
        if ($this->piVars['submitted'] == 1 || ($this->piVars['sendConfirmation'] == 1 && isset($this->piVars['confirmPreview']))) {
            $error = $this->checkErrors();
            $preview = ($error) ? false : true;
        }

        /*This hook could be called to fill the pivars during first loading of form (check preview and error)*/
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeFormElaboration'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeFormElaboration'] as $fieldFunction) {
                $params = array('fields' => $this->fieldsData, 'content' => $content, 'data' => $this->piVars, 'preview' => $preview, 'error' => $error);
                $this->piVars = t3lib_div::callUserFunction($fieldFunction, $params, $this);
            }
        }

        if ($this->userLogged) {
            $buttons = array(
                'confirm' => 'confirmModificationProfileButton',
                'back'    => 'modifyModificationProfileButton',
                'insert'  => 'insertModificationProfileButton'
            );
            if ($this->conf['useAnotherTemplateInChangeProfileMode'] == 1) {
                if (!$preview) {
                    $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_FORM_UPDATEPROFILE');
                } else {
                    $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_PREVIEW_UPDATEPROFILE');
                }
            } else {
                if (!$preview) {
                    $this->markerTitle = 'T3REGISTRATION_FORM';
                    $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_FORM');
                } else {
                    $this->markerTitle = 'T3REGISTRATION_PREVIEW';
                    $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_PREVIEW');
                }
            }
        } else {
            if (!$preview) {
                $this->markerTitle = 'T3REGISTRATION_FORM';
                $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_FORM');
            } else {
                $this->markerTitle = 'T3REGISTRATION_PREVIEW';
                $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_PREVIEW');
            }
            $buttons = array(
                'confirm' => 'confirmRegistrationButton',
                'back'    => 'modifyRegistrationButton',
                'insert'  => 'insertRegistrationButton'
            );
        }
        //if preview is disabled calls directly endRegistration and save user without showing a user preview
        if (($preview && !$this->conf['enablePreview']) || ($this->piVars['sendConfirmation'] == 1 && isset($this->piVars['confirmPreview']) && $preview)) {
            return $this->endRegistration();
        }

        $hiddenArray = array();
        $markerArray = array();

        $contentArray = array();
        foreach ($this->fieldsData as $field) {
            if ($preview) {
                $contentArray = $this->getAndReplaceSubpartPreview($field, $content, $contentArray);
                //multiple check box
                if ($field['config']['type'] == 'check' && isset($field['config']['items']) && is_array($field['config']['items'])) {
                    $multipleCheckHiddenArray = array();
                    for ($counter = 0; $counter < count($field['config']['items']); $counter++) {
                        if (isset($this->piVars[$field['name']][$counter]) && $this->piVars[$field['name']][$counter] == '1') {
                            $multipleCheckHiddenArray[] = sprintf('<input type="hidden" name="%s" value="1" />', $this->prefixId . '[' . $field['name'] . '][' . $counter . ']');
                        }
                    }
                    if (count($multipleCheckHiddenArray)) {
                        $hiddenArray[strtoupper($field['name'])] = implode(chr(10), $multipleCheckHiddenArray);
                    }
                } else {
                    //multiple check box must be an array, but not file
                    $this->piVars[$field['name']] = (is_array($this->piVars[$field['name']])) ? implode(',', $this->piVars[$field['name']]) : $this->piVars[$field['name']];
                    $hiddenArray[strtoupper($field['name'])] = sprintf('<input type="hidden" name="%s" value="%s" />', $this->prefixId . '[' . $field['name'] . ']', $this->htmlentities($this->piVars[$field['name']]));
                }

            } else {
                $contentArray['###' . strtoupper($field['name']) . '_FIELD###'] = ($field['hideInChangeProfile'] == 1 && $this->userLogged) ? '' : $this->getAndReplaceSubpart($field, $content);
            }
        }

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraMarkersRegistration'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraMarkersRegistration'] as $markerFunction) {
                $params = array('preview' => $preview, 'contentArray' => $contentArray, 'hiddenArray' => $hiddenArray, 'content' => $content);
                t3lib_div::callUserFunction($markerFunction, $params, $this);
                $contentArray = $params['contentArray'];
                $hiddenArray = $params['hiddenArray'];
            }
        }


        if ($preview) {
            $markerArray['###DELETE_BLOCK###'] = '';
            $hiddenArray['action'] = sprintf('<input type="hidden" name="%s" value="%s" />', $this->prefixId . '[sendConfirmation]', '1');
            $content = $this->cObj->substituteMarkerArrayCached($content, $contentArray);
            $confirmText = ($this->conf['form.']['submitConfirm.']['imageSubmit']) ? '<input type="image" %s src="' . $this->conf['form.']['submitConfirm.']['imagesrc'] . '" name="' . $this->prefixId . '[confirmPreview]" value="%s" />' : '<input type="submit" %s name="' . $this->prefixId . '[confirmPreview]" value="%s" />';
            $submitButton = sprintf($confirmText, $this->cObj->stdWrap($this->conf['form.']['submitConfirm.']['params'], $this->conf['form.']['submitConfirm.']['params.']), $this->pi_getLL($buttons['confirm']));
            $submitButton = $this->cObj->stdWrap($submitButton, $this->conf['form.']['submitConfirm.']['stdWrap.']);
            $confirmText = ($this->conf['form.']['submitBack.']['imageSubmit']) ? '<input type="image" %s src="' . $this->conf['form.']['submitBack.']['imagesrc'] . '" name="' . $this->prefixId . '[editPreview]" value="%s" />' : '<input type="submit" %s name="' . $this->prefixId . '[editPreview]" value="%s" />';
            $backButton = sprintf($confirmText, $this->cObj->stdWrap($this->conf['form.']['submitBack.']['params'], $this->conf['form.']['submitBack.']['params.']), $this->pi_getLL($buttons['back']));
            $backButton = $this->cObj->stdWrap($backButton, $this->conf['form.']['submitBack.']['stdWrap.']);
            if ($this->conf['form.']['markerButtons']) {
                $markerArray['###FORM_BUTTONS###'] = sprintf('%s' . chr(10) . $backButton . chr(10) . $submitButton, implode(chr(10), $hiddenArray));
            } else {
                $endForm = sprintf('%s' . chr(10) . $backButton . chr(10) . $submitButton, implode(chr(10), $hiddenArray));
            }
        } else {
            if ($this->conf['form.']['resendConfirmationCode'] && !$this->userLogged) {
                $markerArray['###RESEND_CONFIRMATION_CODE_BLOCK###'] = $this->getTextToResendConfirmationEmail();
            } else {
                $markerArray['###RESEND_CONFIRMATION_CODE_BLOCK###'] = '';
            }
            $markerArray['###DELETE_BLOCK###'] = ($this->userLogged) ? $this->showDeleteLink() : '';
            if (count($this->fullErrorsList) && $this->conf['errors.']['showFullList']) {
                $contentErrorsListTemplate = $this->cObj->getSubpart($content, 'ERROR_DESCRIPTION_FULL_BLOCK');
                $contentErrorsList = '';
                foreach ($this->fullErrorsList as $key => $item) {
                    $fullErrorsMarkerArray['###NAME###'] = ($this->pi_getLL($key . 'Label')) ? $this->pi_getLL($key . 'Label') : ((isset($this->fieldsData[$key]['label'])) ? $this->languageObj->sL($this->fieldsData[$key]['label'], true) : '');
                    $fullErrorsMarkerArray['###ERRORS###'] = implode('', $item);
                    $fullErrorsMarkerArray['###DESCRIPTION###'] = $this->cObj->substituteMarkerArray($this->pi_getLL('fullErrorBlockDescriptionString'), $fullErrorsMarkerArray);
                    $contentErrorsList .= $this->cObj->substituteMarkerArrayCached($contentErrorsListTemplate, $fullErrorsMarkerArray);
                }

                $contentArray['###ERROR_DESCRIPTION_FULL_BLOCK###'] = $this->cObj->stdWrap($contentErrorsList, $this->conf['errors.']['fullErrorWrap.']['allWrap.']);
            } else {
                $contentArray['###ERROR_DESCRIPTION_FULL_BLOCK###'] = '';
            }
            $hiddenArray['action'] = sprintf('<input type="hidden" name="%s" value="%s" />', $this->prefixId . '[submitted]', '1');
            $confirmText = ($this->conf['form.']['submitButton.']['imageSubmit']) ? '<input type="image" %s src="' . $this->conf['form.']['submitButton.']['imagesrc'] . '" name="' . $this->prefixId . '[confirmPreview]" value="%s" />' : '<input type="submit" %s name="' . $this->prefixId . '[confirmPreview]" value="%s" />';
            $submitButton = sprintf($confirmText, $this->cObj->stdWrap($this->conf['form.']['submitButton.']['params'], $this->conf['form.']['submitButton.']['params.']), $this->pi_getLL($buttons['insert']));
            $submitButton = $this->cObj->stdWrap($submitButton, $this->conf['form.']['submitButton.']['stdWrap.']);
            if ($this->conf['form.']['markerButtons']) {
                $markerArray['###FORM_BUTTONS###'] = sprintf('%s' . chr(10) . $submitButton, implode(chr(10), $hiddenArray));
            } else {
                $endForm = sprintf('%s' . chr(10) . $submitButton, implode(chr(10), $hiddenArray));
            }

        }
        $content = $this->cObj->substituteMarkerArrayCached($content, $markerArray, $contentArray);
        $this->formId = ($this->conf['form.']['id']) ? $this->conf['form.']['id'] : 't3Registration-' . substr(md5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']), 0, 8);
        $action = $this->pi_getPageLink($GLOBALS['TSFE']->id);
        $content = sprintf('<form id="%s" action="%s" method="post" enctype="%s">' . chr(10) . '%s' . chr(10) . '%s' . chr(10) . '</form>', $this->formId, $action, $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'], $content, $endForm);
        return $content;
    }

    /**
     * This function get and replace the subparts with the corresponding fields.
     *
     * @param    array $field        the field configuration
     * @param    string $content        the html string that contains the markers
     * @return    string        the html code
     */
    public function getAndReplaceSubpart($field, $content) {
        $fieldContent = $this->cObj->getSubpart($content, '###' . strtoupper($field['name']) . '_FIELD###');
        if (($this->piVars['submitted'] || ($this->piVars['sendConfirmation'] && isset($this->piVars['confirmPreview']))) && !$this->errorArray['error'][$field['name']]) {
            $fieldArray['subparts']['###ERROR_FIELD###'] = $this->getErrorSubpart($field, $fieldContent);
            $fieldArray['markers']['###CLASS_ERROR###'] = ($this->conf['errors.']['classError']) ? $this->conf['errors.']['classError'] : '';
        } else {
            $fieldArray['subparts']['###ERROR_FIELD###'] = '';
            $fieldArray['markers']['###CLASS_ERROR###'] = '';
        }
        if ($field['type'] == 'databaseField' || ($field['type'] == 'freeField' && isset($field['config']['type']))) {
            $fieldArray['markers']['###AUTO_FIELD###'] = $this->getAutoField($field);
        }
        $fieldArray['markers']['###FIELD_LABEL###'] = ($this->pi_getLL($field['name'] . 'Label')) ? $this->pi_getLL($field['name'] . 'Label') : ((isset($field['label'])) ? $this->languageObj->sL($field['label'], true) : '');
        $fieldArray['markers']['###FIELD_LABEL###'] = $this->cObj->stdWrap($fieldArray['markers']['###FIELD_LABEL###'], $this->conf['form.']['standardLabelWrap.']);
        $fieldArray['markers']['###FIELD_VALUE###'] = (isset($this->piVars[$field['name']])) ? $this->piVars[$field['name']] : (($field['config']['default']) ? $field['config']['default'] : '');
        $fieldArray['markers']['###FIELD_NAME###'] = $this->prefixId . '[' . $field['name'] . ']';
        $fieldArray['markers']['###FIELD_NAME_ID###'] = $this->getIdForField($field, true);
        //the first call is used to substitute subpart, the second one substitute error class markers on all template
        $fieldContent = $this->cObj->substituteMarkerArrayCached($fieldContent, $fieldArray['markers'], $fieldArray['subparts']);
        return $this->cObj->substituteMarkerArrayCached($fieldContent, $fieldArray['markers'], $fieldArray['subparts']);
    }

    /**
     * This function replaces the subparts in preview mode.
     *
     * @param    array         $field the field configuration
     * @param    string        $content the html string that contains the markers
     * @param    string        $contentArray
     * @return    string        the field preview HTML code
     */
    public function getAndReplaceSubpartPreview($field, $content, $contentArray) {
        if ($field['hideInChangeProfile'] == 1 && $this->userLogged) {
            $contentArray['###' . strtoupper($field['name']) . '_LABEL###'] = '';
            $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = '';
        } else {
            $contentArray['###' . strtoupper($field['name']) . '_LABEL###'] = (($field['hideInChangeProfile'] == 1 && $this->userLogged) || (strlen($this->piVars[$field['name']]) == 0) && (isset($this->conf['form.']['hideInPreviewIfEmpty']) && $this->conf['form.']['hideInPreviewIfEmpty'] == 1)) ? '' : (($this->pi_getLL($field['name'] . 'Label')) ? $this->cObj->stdWrap($this->pi_getLL($field['name'] . 'Label'), $this->conf['form.']['standardPreviewLabelWrap.']) : ((isset($field['label'])) ? $this->cObj->stdWrap($this->languageObj->sL($field['label'], true), $this->conf['form.']['standardPreviewLabelWrap.']) : ''));
            switch ($field['config']['type']) {
                case 'input':
                case 'text':
                    $this->piVars[$field['name']] = (is_array($this->piVars[$field['name']])) ? implode(',', $this->piVars[$field['name']]) : $this->piVars[$field['name']];
                    //call $this->htmlentities to remove xss scripting side
                    $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = (($field['hideInChangeProfile'] == 1 && $this->userLogged) || strlen($this->piVars[$field['name']]) == 0) ? '' : (($field['noHTMLEntities']) ? $this->cObj->stdWrap($this->piVars[$field['name']], $this->conf['form.']['standardPreviewFieldWrap.']) : $this->cObj->stdWrap($this->htmlentities($this->piVars[$field['name']]), $this->conf['form.']['standardPreviewFieldWrap.']));
                    break;
                case 'group':
                    if (isset($field['config']['internal_type']) && $field['config']['internal_type'] === 'file') {
                        $images = explode(',', $this->piVars[$field['name']]);
                        $imageList = array();
                        foreach ($images as $image) {
                            $fieldArray = (isset($this->conf[$field['name'] . '.']) && is_array($this->conf[$field['name'] . '.'])) ? $this->conf[$field['name'] . '.'] : array();
                            $fieldArray['file'] = $field['config']['uploadfolder'] . '/' . $image;
                            $imageList[] = $this->cObj->IMAGE($fieldArray);
                        }
                        $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = (($field['hideInChangeProfile'] == 1 && $this->userLogged) || strlen($this->piVars[$field['name']]) == 0) ? '' : implode('', $imageList);
                    }
                    break;
                case 'select':
                case 'radio':
                    $tca = new tx_t3registration_tcaexternalfunctions();
                    $items = $tca->getForeignTableData($field);
                    foreach ($items as $item) {
                        $text = (isset($item[0])) ? (preg_match('/LLL:EXT:/', $item[0]) ? $this->languageObj->sl($item[0]) : $item[0]) : '';
                        $value = (isset($item[1])) ? $item[1] : '';
                        if ($this->piVars[$field['name']] == $value) {
                            $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = (($field['hideInChangeProfile'] == 1 && $this->userLogged) || strlen($this->piVars[$field['name']]) == 0) ? '' : $this->cObj->stdWrap($text, $this->conf['form.']['standardPreviewFieldWrap.']);
                        }
                    }
                    break;
                case 'check':
                    if (isset($field['config']['items']) && is_array($field['config']['items'])) {
                        for ($counter = 0; $counter < count($field['config']['items']); $counter++) {
                            if (isset($this->piVars[$field['name']][$counter]) && $this->piVars[$field['name']][$counter] == '1') {
                                $values[] = (isset($field['config']['items'][$counter][0])) ? (preg_match('/LLL:EXT:/', $field['config']['items'][$counter][0]) ? $this->languageObj->sl($field['config']['items'][$counter][0]) : $field['config']['items'][$counter][0]) : '';
                            }
                        }
                        $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = (($field['hideInChangeProfile'] == 1 && $this->userLogged) || count($this->piVars[$field['name']]) == 0) ? '' : $this->cObj->stdWrap(implode(',', $values), $this->conf['form.']['standardPreviewFieldWrap.']);
                    } else {
                        if (isset($this->piVars[$field['name']]) && $this->piVars[$field['name']] == '1') {
                            //todo to explain in the manual
                            $contentArray['###' . strtoupper($field['name']) . '_VALUE###'] = (isset($field['config']['text'])) ? ((preg_match('/LLL:EXT:/', $field['config']['text']) ? $this->cObj->stdWrap($this->languageObj->sl($field['config']['text']), $this->conf['fieldConfiguration.'][$field['name'] . '.']['config.']['text.']['stdWrap.']) : $this->cObj->stdWrap($field['config']['text'], $this->conf['fieldConfiguration.'][$field['name'] . '.']['config.']['text.']['stdWrap.']))) : '';
                        }
                    }
                    break;
                case 'hook':
                    if (isset($field['config']['hook'])) {
                        $params['field'] = $field;
                        $params['row'] = $this->piVars;
                        $params['preview'] = true;
                        $params['contentArray'] = $contentArray;
                        $contentArray = t3lib_div::callUserFunction($field['config']['hook'], $params, $this);
                    }
                    break;
            }
        }
        return $contentArray;
    }

    /**
     * This function return the html code for every field passed according to the specified configuration.
     *
     * @param    array $field        the field configuration
     * @return    string        the html field code
     */
    protected function getAutoField($field) {
        $htmlBlock = '';
        switch ($field['config']['type']) {
            case 'input':
                $type = (isset($field['config']['eval']) && t3lib_div::inList($field['config']['eval'], 'password')) ? 'password' : 'text';
                $size = ($field['config']['size']) ? $field['config']['size'] : '15';
                //@todo adds id and class into manual
                $id = $this->getIdForField($field);
                $title = $this->pi_getLL($field['name'] . 'Title') ? 'title="' . $this->pi_getLL($field['name'] . 'Title') . '"' : '';
                $extra = ($field['config']['extra']) ? $field['config']['extra'] : (($this->conf['form.']['standardFieldExtra']) ? $this->conf['form.']['standardFieldExtra'] : '');
                $maxchar = ($field['config']['maxchar']) ? ' maxchar="' . $field['config']['maxchar'] . '" ' : '';
                $value = (isset($this->piVars[$field['name']])) ? $this->piVars[$field['name']] : (($field['config']['default']) ? $field['config']['default'] : '');
                $htmlBlock = $this->cObj->stdWrap(sprintf('<input type="%s" %s name="%s" value="%s" size="%s" %s %s %s/>', $type, $id, $this->prefixId . '[' . $field['name'] . ']', $value, $size, $maxchar, $extra, $title), $this->conf['form.']['standardFieldWrap.']);
                break;
            case 'text':
                $cols = ($field['config']['cols']) ? $field['config']['cols'] : '40';
                $rows = ($field['config']['rows']) ? $field['config']['rows'] : '20';
                $id = $this->getIdForField($field);
                $extra = ($field['config']['extra']) ? $field['config']['extra'] : (($this->conf['form.']['standardFieldExtra']) ? $this->conf['form.']['standardFieldExtra'] : '');
                $value = (isset($this->piVars[$field['name']])) ? $this->piVars[$field['name']] : (($field['config']['default']) ? $field['config']['default'] : '');
                $htmlBlock = $this->cObj->stdWrap(sprintf('<textarea %s name="%s" cols="%s"  rows="%s" %s>%s</textarea>', $id, $this->prefixId . '[' . $field['name'] . ']', $cols, $rows, $extra, $value), $this->conf['form.']['standardFieldWrap.']);
                break;
            case 'group':
                if (isset($field['config']['internal_type']) && $field['config']['internal_type'] === 'file') {
                    $wrappingData = ($this->conf[$field['name'] . '.']['allWrap.']) ? $this->conf[$field['name'] . '.']['allWrap.'] : array();
                    $fileArray = explode(',', $this->piVars[$field['name']]);
                    for ($i = 1; $i <= $field['config']['maxitems']; $i++) {
                        $file = (isset($fileArray[$i - 1])) ? $fileArray[$i - 1] : '';
                        $htmlBlock .= $this->cObj->stdWrap($this->getUploadField($field, $file, $i), $wrappingData);
                    }
                }
                break;
            case 'select':
                $tca = new tx_t3registration_tcaexternalfunctions();
                $items = $tca->getForeignTableData($field, $field['config']['items']);
                $items = $tca->getItemsProcFunc($field, $items);
                $id = $this->getIdForField($field);
                $extra = ($field['config']['extra']) ? $field['config']['extra'] : (($this->conf['form.']['standardFieldExtra']) ? $this->conf['form.']['standardFieldExtra'] : '');
                $this->piVars[$field['name']] = (isset($this->piVars[$field['name']])) ? $this->piVars[$field['name']] : (($field['config']['default']) ? $field['config']['default'] : '');
                foreach ($items as $item) {
                    $text = (isset($item[0])) ? (preg_match('/LLL:EXT:/', $item[0]) ? $this->languageObj->sl($item[0]) : $item[0]) : '';
                    $value = (isset($item[1])) ? $item[1] : '';
                    $selected = ($this->piVars[$field['name']] == $value) ? 'selected' : '';
                    $options[] = sprintf('<option value="%s" %s>%s</option>', $value, $selected, $text);
                }
                $htmlBlock = $this->cObj->stdWrap(sprintf('<select %s name="%s" %s>%s</select>', $id, $this->prefixId . '[' . $field['name'] . ']', $extra, implode(chr(10), $options)), $this->conf['form.']['standardFieldWrap.']);
                break;
            case 'radio':
                $tca = new tx_t3registration_tcaexternalfunctions();
                $items = $tca->getForeignTableData($field, $field['config']['items']);
                $items = $tca->getItemsProcFunc($field, $items);
                $this->piVars[$field['name']] = (isset($this->piVars[$field['name']])) ? $this->piVars[$field['name']] : (($field['config']['default']) ? $field['config']['default'] : '');
                $options = array();
                $extra = ($field['config']['extra']) ? $field['config']['extra'] : (($this->conf['form.']['standardFieldExtra']) ? $this->conf['form.']['standardFieldExtra'] : '');
                $counter = 0;
                foreach ($items as $item) {
                    $counter++;
                    $text = (isset($item[0])) ? (preg_match('/LLL:EXT:/', $item[0]) ? $this->languageObj->sl($item[0]) : $item[0]) : '';
                    $reference = ($field['usingCounterAsId'] || $this->conf['form.']['usingCounterAsIdAll']) ? $counter : str_replace(' ', '_', (((isset($item[1]) || strlen($item[1] >0))) ? $item[1] : 'empty'));
                    $id = ($field['config']['id']) ? ' id="' . $field['config']['id'] . '_' . $reference . '" ' : (($this->conf['form.']['standardFieldId']) ? ' id="' . $this->conf['form.']['standardFieldId.']['pre'] . $field['name'] . '_' . $reference . '" ' : '');
                    $value = (isset($item[1])) ? $item[1] : '';
                    $selected = ($this->piVars[$field['name']] == $value) ? 'checked' : '';
                    $options[] = $this->cObj->stdWrap(sprintf('<input type="radio" name="%s" value="%s" %s %s %s>%s', $this->prefixId . '[' . $field['name'] . ']', $value, $id, $selected, $extra, $text), $this->conf['fieldConfiguration.'][$field['name'] . '.']['config.']['stdWrap.']);
                }
                $htmlBlock = $this->cObj->stdWrap(implode(chr(10), $options), $this->conf['form.']['standardFieldWrap.']);
                break;
            case 'check':
                if (isset($field['config']['items']) && is_array($field['config']['items'])) {
                    if (!isset($this->piVars[$field['name']])) {
                        if ($field['config']['default']) {
                            for ($counter = 0; $counter < count($field['config']['items']); $counter++) {
                                $this->piVars[$field['name']][$counter] = (($field['config']['default'] & pow(2, $counter)) > 0) ? 1 : 0;
                            }
                        }
                    } else {
                        if (!is_array($this->piVars[$field['name']])) {
                            $piVars = array();
                            for ($counter = 0; $counter < count($field['config']['items']); $counter++) {
                                $piVars[$counter] = (($this->piVars[$field['name']] & pow(2, $counter)) > 0) ? 1 : 0;
                            }
                            $this->piVars[$field['name']] = $piVars;
                        }
                    }
                    $options = array();
                    $extra = ($field['config']['extra']) ? $field['config']['extra'] : (($this->conf['form.']['standardFieldExtra']) ? $this->conf['form.']['standardFieldExtra'] : '');
                    foreach ($field['config']['items'] as $key => $item) {
                        $text = (isset($item[0])) ? (preg_match('/LLL:EXT:/', $item[0]) ? $this->languageObj->sl($item[0]) : $item[0]) : '';
                        $value = '1';
                        $selected = (isset($this->piVars[$field['name']][$key]) && $this->piVars[$field['name']][$key] == '1') ? 'checked="checked"' : '';
                        $options[] = $this->cObj->stdWrap(sprintf('<input type="checkbox" name="%s" value="%s" %s %s>%s', $this->prefixId . '[' . $field['name'] . '][' . $key . ']', $value, $selected, $extra, $text), $this->conf['fieldConfiguration.'][$field['name'] . '.']['config.']['stdWrap.']);
                    }
                    $htmlBlock = $this->cObj->stdWrap(implode(chr(10), $options), $this->conf['form.']['standardFieldWrap.']);
                } else {
                    $this->piVars[$field['name']] = (isset($this->piVars[$field['name']])) ? $this->piVars[$field['name']] : (($field['config']['default']) ? $field['config']['default'] : '');
                    $text = (preg_match('/LLL:EXT:/', $field['config']['text']) ? $this->cObj->stdWrap($this->languageObj->sl($field['config']['text']), $this->conf['fieldConfiguration.'][$field['name'] . '.']['config.']['text.']['stdWrap.']) : $this->cObj->stdWrap($field['config']['text'], $this->conf['fieldConfiguration.'][$field['name'] . '.']['config.']['text.']['stdWrap.']));
                    $value = '1';
                    $selected = ($this->piVars[$field['name']] == $value) ? 'checked="checked"' : '';
                    $htmlBlock = $this->cObj->stdWrap(sprintf('<input type="checkbox" name="%s" value="%s" %s>%s', $this->prefixId . '[' . $field['name'] . ']', $value, $selected, $text), $this->conf['fieldConfiguration.'][$field['name'] . '.']['config.']['stdWrap.']);
                }
                break;
            case 'hook':
                if (isset($field['config']['hook'])) {
                    $params['field'] = $field;
                    $params['row'] = $this->piVars;
                    $htmlBlock = t3lib_div::callUserFunction($field['config']['hook'], $params, $this);
                }
                break;
        }
        return $htmlBlock;
    }


    /**
     * This function elaborates configuration to create an Id for Field
     * @param array $field field configuration
     * @param boolean  $onlyIdValue
     * @return string id
     */
    protected function getIdForField($field, $onlyIdValue = false) {
        if (!(isset($this->conf['fieldConfiguration.'][$field['name'] . '.']['disableAutoId']) && $this->conf['fieldConfiguration.'][$field['name'] . '.']['disableAutoId'] == 1) && !$this->conf['form.']['disableAllAutoId']) {
            $idWrapper = ($this->conf['fieldConfiguration.'][$field['name'] . '.']['idFieldWrap.']) ? $this->conf['fieldConfiguration.'][$field['name'] . '.']['idFieldWrap.'] : (($this->conf['form.']['idFormWrap.']) ? $this->conf['form.']['idFormWrap.'] : array());
            $autoId = $this->cObj->stdWrap($field['name'], $idWrapper);
            $id = ($field['config']['id']) ? $field['config']['id'] : (($this->conf['form.']['standardFieldId']) ? $this->conf['form.']['standardFieldId.']['pre'] . $field['name'] : $autoId);
            return (!$onlyIdValue) ? ' id="' . $id . '"' : $id;
        } else {
            return '';
        }
    }

    /**
     * This function manages the render process of single field
     *
     * @param    array $field array field data
     * @param    string $value value to insert into hidden field
     * @param    string $counter counter for images
     * @return    string        the field HTML code
     */
    protected function getUploadField($field, $value = '', $counter = '') {
        $htmlBlock = '';
        $type = 'file';
        $name = $this->prefixId . '[' . $field['name'] . '][' . $counter . ']';
        $hiddenValue = 'value=""';
        if ($value) {
            $hiddenValue = 'value="' . $value . '"';
            $classRef = 'class="t3registration_pi1_ref_' . $field['name'] . '_' . $counter . '"';
            if ($this->conf['javascriptsInclusion.']['imageRemove']) {
                $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('t3registration') . 'res/javascript/registration.js"></script>';
            }
            $fieldArray = (isset($this->conf[$field['name'] . '.']) && is_array($this->conf[$field['name'] . '.'])) ? $this->conf[$field['name'] . '.'] : array();
            $fieldArray['file'] = $field['config']['uploadfolder'] . '/' . $value;
            $fieldArray['params'] = $classRef;
            $value = $this->cObj->IMAGE($fieldArray);
            $type = 'hidden';
            $confImage = (is_array($this->conf['form.']['trashImage.'])) ? $this->conf['form.']['trashImage.'] : array();
            if (!isset($this->conf['form.']['trashImage.']['file'])) {
                $confImage['file'] = t3lib_extMgm::siteRelPath('t3registration') . 'res/trash.png';
            }
            $confImage['params'] = 'class="t3registration_pi1_deleteImage" ref="t3registration_pi1_ref_' . $field['name'] . '_' . $counter . '"';
            $confImage['altText'] = $this->pi_getLL('deleteImageConfirmation');
            $confImage['titleText'] = $this->pi_getLL('deleteImage');
            $trash = $this->cObj->IMAGE($confImage);
            $htmlBlock = $value . $trash;
        }

        $htmlBlock .= sprintf('<input type="%s" %s name="%s" %s/>', $type, $classRef, $name, $hiddenValue);
        return $htmlBlock;
    }


    /**
     * This function call the insertUser method.
     *
     * @return    string        the registration final HTML template
     */
    protected function endRegistration() {
        $content = $this->getTemplate();
        foreach ($this->fieldsData as $field) {
            if ($field['config']['type'] == 'check' && isset($field['config']['items']) && is_array($field['config']['items'])) {
                $multipleCheckValue = 0;
                for ($counter = 0; $counter < count($field['config']['items']); $counter++) {
                    if (isset($this->piVars[$field['name']][$counter]) && $this->piVars[$field['name']][$counter] == '1') {
                        $multipleCheckValue += pow(2, $counter);
                    }
                }
                $this->piVars[$field['name']] = $multipleCheckValue;
            }
            $valueArray['###' . strtoupper($field['name']) . '###'] = htmlspecialchars($this->piVars[$field['name']]);
        }
        if ($this->userLogged) {
            $this->updateUserProfile();
            $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_ENDUPDATEPROFILE');
            $contentArray['###UPDATE_PROFILE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('finalUpdateProfileText'), $valueArray);
            $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        } else {
            $this->insertUser();
            $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_ENDREGISTRATION');
            $contentArray['###FINAL_REGISTRATION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('finalRegistrationText'), $valueArray);
            $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        }
        return $this->cObj->substituteMarkerArrayCached($content, $contentArray);
    }


    /**
     * This function is called before getForm if user is logged, so it merge data from database into piVars, only if form is not submitted
     *
     * @param    array        value to overwrite standard data (used in template test)
     * @return    string        the form HTML code
     */
    protected function showProfile($user = array()) {
        if (!is_array($user) || count($user) == 0) {
            $uid = $this->feLoggedInUser['uid'];
            $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid=' . $uid);
            $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
        }
        foreach ($this->fieldsData as $field) {
            if (isset($user[$field['field']])) {
                $this->piVars[$field['name']] = $user[$field['field']];
            } elseif (isset($field['config']['fetchDataHook'])) {
                $params = array();
                $params['user'] = $user;
                $params['piVars'] = $this->piVars;
                $this->piVars[$field['name']] = t3lib_div::callUserFunction($field['config']['fetchDataHook'], $params, $this);
            }
        }
        $this->preElaborateData($user);
        return $this->getForm();
    }

    /**
     * This function gets the error subpart for the passed field and then it replaces the error marker with the error description.
     *
     * @param    array $field        the field configuration
     * @param    string $content        the html string that contains the markers
     * @return    string        the HTML code with the error description
     * @tested 20111017
     */
    protected function getErrorSubpart($field, $content) {
        $errorContent = $this->cObj->getSubpart($content, '###ERROR_FIELD###');
        //needs because fields data remove dot and erroWrap. becomes errorWrap
        $field['errorWrap.'] = $this->conf['fieldConfiguration.'][$field['name'] . '.']['errorWrap.'];
        if (!isset($field['errorWrap.']) || !is_array($field['errorWrap.'])) {
            $field['errorWrap.'] = (is_array($this->conf['errors.']['standardErrorStdWrap.'])) ? $this->conf['errors.']['standardErrorStdWrap.'] : array();
        }
        $singleError = (isset($field['singleErrorEvaluate'])) ? $field['singleErrorEvaluate'] : $this->conf['errors.']['singleErrorEvaluate'];
        //fetch each single error description
        if (is_array($this->errorArray['errorDescription'][$field['name']]) && $singleError) {
            $errorDescriptionArray = array();
            foreach ($this->errorArray['errorDescription'][$field['name']] as $singleErrorDescription) {
                $errorDescriptionArray[] = $this->cObj->stdWrap($this->pi_getLL($field['name'] . ucfirst($singleErrorDescription) . 'Error'), $field['errorWrap.']);
                $this->fullErrorsList[$field['name']][] = $this->cObj->stdWrap($this->pi_getLL($field['name'] . ucfirst($singleErrorDescription) . 'Error'), $this->conf['errors.']['fullErrorWrap.']['singleErrorWrap.']);
            }
            return preg_replace('/###ERROR_LABEL###/', implode('', $errorDescriptionArray), $errorContent);
        } else {
            $this->fullErrorsList[$field['name']][] = $this->cObj->stdWrap($this->pi_getLL($field['name'] . 'Error'), $this->conf['errors.']['fullErrorWrap.']['singleErrorWrap.']);
            return preg_replace('/###ERROR_LABEL###/', $this->cObj->stdWrap($this->pi_getLL($field['name'] . 'Error'), $field['errorWrap.']), $errorContent);
        }
    }

    /***************************************EVALUATE FUNCTIONS******************************/

    /**
     * This function checks every fields errors. Descriptions of found errors are put into $this->errorArray.
     * return boolean true if one or more errors are found
     *
     * @return    boolean true if error was found
     */
    protected function checkErrors() {
        $error = false;
        foreach ($this->fieldsData as $field) {
            //call only fields if you can enable an error you have to user this code into your hook
            $this->errorArray['error'][$field['name']] = ($field['hideInChangeProfile'] == 1 && $this->userLogged) ? true : $this->checkField($field);
            if (!$this->errorArray['error'][$field['name']]) $error = true;
        }
        return $error;
    }

    /**
     * This function fetches all evaluation from field
     *
     * @param    string        $name name of field to evaluate
     * @return    array        evaluation array list
     */
    public function getEvaluationRulesList($name) {
        $field = $this->fieldsData[$name];
        $evaluation = array();
        $field['config']['eval'] = $field['config']['eval'] ? $field['config']['eval'] : '';
        if (isset($field['regexp']) && strlen($field['regexp']) > 0) {
            if (strlen($field['config']['eval']) > 0) {
                $evalArray = explode(',', $field['config']['eval']);
            } else {
                $evalArray = array();
            }
            $evalArray[] = 'regexp';
            $field['config']['eval'] = implode(',', $evalArray);
        }
        if (isset($field['config']['internal_type']) && $field['config']['internal_type'] === 'file') {
            if (strlen($field['config']['eval']) > 0) {
                $evalArray = explode(',', $field['config']['eval']);
            } else {
                $evalArray = array();
            }
            $evalArray[] = 'file';
            $field['config']['eval'] = implode(',', $evalArray);
        }
        if (isset($field['config']['eval'])) {
            $evaluation = explode(',', $field['config']['eval']);
        }
        //evaluation from flexform
        if (isset($field['evaluation']) && strlen(trim($field['evaluation']))) {
            $additionalEvaluationArray = array_diff(explode(',', $field['evaluation']), $evaluation);
            $evaluation = array_merge($evaluation, $additionalEvaluationArray);
        }
        //evaluation from typoscript add function
        if (isset($field['config']['additionalEval'])) {
            $additionalEvaluationArray = array_diff(explode(',', $field['config']['additionalEval']), $evaluation);
            $evaluation = array_merge($evaluation, $additionalEvaluationArray);
        }
        return $evaluation;
    }

    /**
     * This function checks evaluation field types. Then it calls a method (evaluateField).
     *
     * @param    array $field        the field to check
     * @return    boolean        false if the field contains errors
     */
    protected function checkField($field) {
        $evaluation = $this->getEvaluationRulesList($field['name']);
        $error = true;
        foreach ($evaluation as $item) {
            //if error return false
            if (!$this->evaluateField($this->piVars[$field['name']], $item, $field)) {
                //if hookHandleError is not set create error description, otherwise hook create by itself the error
                if (!(isset($field['config']['hookHandleError']) && $field['config']['hookHandleError'] == 1)) {
                    $this->errorArray['errorDescription'][$field['name']][] = $item;
                }
                $error = false;
            }
        }

        return $error;
    }

    /**
     * This function checks if the field respects the evaluation rule passed.
     *
     * @param    string $value        the value to check
     * @param    string $evaluationRule        the evaluation rule used to check the value
     * @param    array $field        array field configuration
     * @return    boolean        true if the field respects the evaluation rule.
     */
    protected function evaluateField($value, $evaluationRule, $field = array()) {
        switch ($evaluationRule) {
            case 'int':
                return t3lib_div::testInt($value);
                break;
            case 'alpha':
            case 'string':
                return preg_match('/^[a-zA-Z]+$/', $value);
                break;
            case 'email':
                return t3lib_div::validEmail($value);
                break;
            case 'regexp':
                return preg_match('/' . $field['regexp'] . '/', $value);
                break;
            case 'password':
                return $this->checkLength($value, $field);
                break;
            case 'unique':
                if ($value) {
                    return $this->checkUniqueField($value, $field);
                } else {
                    return true;
                }
                break;
            case 'required':
                if (strlen($this->piVars[$field['name']]) > 0 || (is_array($this->piVars[$field['name']]) && count($this->piVars[$field['name']]) > 0)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'uniqueInPid':
                if ($value) {
                    return $this->checkUniqueField($value, $field, $this->conf['userFolder']);
                } else {
                    return true;
                }
                break;
            case 'file':
                $fileFields[$field['name']] = $this->piVars[$field['name']];
                $noError = true;
                foreach ($_FILES[$this->prefixId]['name'][$field['name']] as $key => $item) {
                    if (strlen($item) > 0) {
                        $file = $this->checkFileUploaded($item, $_FILES[$this->prefixId]['size'][$field['name']][$key], $_FILES[$this->prefixId]['tmp_name'][$field['name']][$key], $field);
                        if ($file === true) {
                            $noError = false;
                            $file = '';
                        } else {
                            $fileFields[$field['name']][] = $file;
                        }
                    }
                }
                $this->piVars[$field['name']] = (is_array($fileFields[$field['name']])) ? implode(',', $fileFields[$field['name']]) : $fileFields[$field['name']];
                return $noError;
                break;
            case 'date':
                return $this->evaluateDate($this->piVars[$field['name']],$field);
            break;
            case 'hook':
                if (isset($field['config']['evalHook'])) {
                    $params['field'] = $field;
                    $params['row'] = $this->piVars;
                    $params['value'] = $this->piVars[$field['name']];
                    return t3lib_div::callUserFunction($field['config']['evalHook'], $params, $this);
                }
                break;
            default:
                if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraEvaluationRules'])) {
                    foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['extraEvaluationRules'] as $evaluationFunction) {
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

    protected function checkDateRange($timestamp,$field){

        if(isset($field['config']['date']['maxDate'])){
            if(($maxDate = $this->getTimestampFromDate($field['config']['date']['maxDate'],$field)) !== false){
                if($timestamp>$maxDate){
                    return false;
                }
            }
            else{
                return false;
            }
        }
        if(isset($field['config']['date']['minDate'])){
            if(($minDate = $this->getTimestampFromDate($field['config']['date']['minDate'],$field)) !== false){
                if($timestamp<$minDate){
                    return false;
                }
            }
            else{
                return false;
            }
        }
        if(isset($field['config']['date']['dateHasToBeIn'])){
            if(($now = $this->getTimestampFromDate(date($field['config']['date']['strftime']),$field)) !== false){
                if($field['config']['date']['dateHasToBeIn'] == 'future' && $timestamp<$now){
                    return false;
                }
                elseif($field['config']['date']['dateHasToBeIn'] == 'past' && $timestamp>$now){
                    return false;
                }
            }
            else{
                return false;
            }
        }
        return true;
    }


    protected function evaluateDate($date,$field){
        if(($timestamp = $this->getTimestampFromDate($date,$field)) !== false){
            return $this->checkDateRange($timestamp,$field);
        }
        else{
            return false;
        }
    }

    /**
     * This function checks if the value inserted in the field by the user is unique.
     *
     * @param    string $value        the value to check
     * @param    array $field        the field configuration
     * @param    integer $folder    if set user is found inside the folder id
     * @return    boolean        true if the value is unique
     */
    protected function checkUniqueField($value, $field, $folder = 0) {
        if ($field['type'] === 'databaseField') {
            if (!is_int($value)) {
                $value = $GLOBALS['TYPO3_DB']->fullQuoteStr($value, 'fe_users');
            }
            $where = $field['field'] . '=' . $value . ' AND deleted = 0';
            if ($folder) {
                $where .= ' AND pid=' . $folder;
            }
            //operation is an update, so you can insert a value equal own
            if (($this->userLogged)) {
                $where .= ' AND uid != ' . $this->feLoggedInUser['uid'];
            }
            $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery($field['field'], 'fe_users', $where);
            if ($GLOBALS['TYPO3_DB']->sql_num_rows($resource) > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * This function control if string is greater than config.maxchars and less than config.minchars
     *
     * @param    string $value        value to check
     * @param    array $field        field configuration array
     * @return    boolean        true if rule is satisfied otherwise false
     */
    protected function checkLength($value, $field) {
        $error = true;
        if (t3lib_div::testInt($field['config']['maxchars']) && $field['config']['maxchars'] > 0) {
            if (strlen($value) > $field['config']['maxchars'])
                $error = false;
        }
        if (t3lib_div::testInt($field['config']['minchars']) && $field['config']['minchars'] > 0) {
            if (strlen($value) < $field['config']['minchars'])
                $error = false;
        }
        return $error;
    }

    /**
     * This function checks if the uploaded file is an allowed file.
     *
     * @param    string $name        complete name of the file
     * @param    integer $size        size of the file
     * @param    string $tmpFile
     * @param    array $field        the field configuration
     * @return    array        the uploaded file features
     */
    protected function checkFileUploaded($name, $size, $tmpFile, $field) {
        $tmpArray = explode('.', $name);
        $tmpArray = array_reverse($tmpArray);
        if (t3lib_div::inList($field['config']['allowed'], $tmpArray[0]) && ($size / 1000) <= ($field['config']['max_size'])) {
            $fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
            $filename = $fileFunc->getUniqueName($name, PATH_site . $field['config']['uploadfolder']);
            t3lib_div::upload_copy_move($tmpFile, $filename);
            $extractFilename = explode('/', $filename);
            $extractFilename = array_reverse($extractFilename);
            return $extractFilename[0];
        } else {
            return true;
        }
    }


    /********************************************GENERAL MAIL FUNCTIONS******************************/

    /**
     * This function prepares the email to send user for deleting and create the auth code for authenticate the confirmation
     * Extract the "T3REGISTRATION_DELETE_SENTEMAIL" marker subpart and create the email body
     *
     * @param    array          user to overwrite data
     * @return    string        the registration final HTML template
     */
    protected function emailDeletionSent($user = array()) {
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_DELETE_SENTEMAIL');
        if (isset($this->feLoggedInUser['uid'])) {
            if (!is_array($user) || count($user) == 0) {
                $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid=' . $this->feLoggedInUser['uid']);
                $user = array();
                if ($GLOBALS['TYPO3_DB']->sql_num_rows($resource) > 0) {
                    $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                    $user['user_auth_code'] = md5('deleteAuth' . time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                    $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid=' . $this->feLoggedInUser['uid'], $user);
                }
            }
            //$user could be empty if no user found
            if (count($user)) {
                foreach ($this->fieldsData as $field) {
                    $valueArray['###' . strtoupper($field['name']) . '###'] = $this->htmlentities($user[$field['field']]);
                }
                $contentArray['###DELETE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('textAfterDeleteRequest'), $valueArray);
                $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
                $this->prepareAndSendEmailSubpart('deleteRequest', $user);
            } else {
                $contentArray['###DELETE_TEXT###'] = $this->pi_getLL('userNotFound');
                $contentArray['###SIGNATURE###'] = '';
            }
        } else {
            $contentArray['###DELETE_TEXT###'] = $this->pi_getLL('userMustBeLogged');
            $contentArray['###SIGNATURE###'] = '';
        }
        return $this->cObj->substituteMarkerArrayCached($content, $contentArray);
    }

    /**
     * This function prepares the user deletion email message.
     *
     * @param    array $user        the target user of the email
     * @return    array        the type and the HTML content of the message.
     */
    protected function deleteEmail($user) {
        $confirmationPage = ($this->conf['deletePage']) ? $this->conf['deletePage'] : $GLOBALS['TSFE']->id;
        $confirmationArray = array(
            $this->prefixId . '[' . 'action' . ']'   => 'userDeleteConfirmation',
            $this->prefixId . '[' . 'authcode' . ']' => $user['user_auth_code']
        );
        $authLink = t3lib_div::locationHeaderUrl($this->pi_getpageLink($confirmationPage, '', $confirmationArray));
        $deleteLinkParams = ($this->conf['email.']['delete.']['linkParams']) ? $this->conf['email.']['delete.']['linkParams'] : '';
        $authLink = sprintf('<a href="%s" %s>%s</a>', $authLink, $deleteLinkParams, $this->htmlentities($this->pi_getLL('deleteLinkConfirmationText')));
        foreach ($this->fieldsData as $field) {
            $markerArray['###' . strtoupper($field['name']) . '###'] = $this->piVars[$field['name']];
        }
        $markerArray['###DELETE_LINK###'] = $authLink;
        foreach ($user as $key => $value) {
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray['###DELETE_LINK###'] = $authLink;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deleteTextHtml'), $valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deleteTextText'), $valueArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        $message = $this->prepareEmailContent('###T3REGISTRATION_DELETE_EMAIL_HTML###', '###T3REGISTRATION_DELETE_EMAIL_TEXT###', $markerArray);
        $message['type'] = 'user';
        return $message;
    }

    /**
     * This function prepares email message to advice the user about admin authorization.
     *
     * @param    array $user        the target user of the email
     * @return    array        the type and the HTML content of the message.
     */
    protected function sendAdviceAfterAuthorization($user) {
        $markerArray = array();
        foreach ($user as $key => $value) {
            $markerArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray = $markerArray;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAfterAuthorizationTextHtml'), $valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAfterAuthorizationTextText'), $valueArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['sendAdviceAfterAuthorization'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['sendAdviceAfterAuthorization'] as $markerFunction) {
                $params = array('markerArray' => $markerArray, 'user' => $user);
                $markerArray = t3lib_div::callUserFunction($markerFunction, $params, $this);
            }
        }
        $message = $this->prepareEmailContent('###T3REGISTRATION_CONFIRMEDONADMINAPPROVAL_EMAIL_HTML###', '###T3REGISTRATION_CONFIRMEDONADMINAPPROVAL_EMAIL_TEXT###', $markerArray);
        $message['type'] = 'user';
        return $message;
    }

    /**
     * This function prepares the user confirmation email message.
     *
     * @param    array $user        the target user of the email
     * @return    array        the type and the HTML content of the message.
     */
    protected function confirmationEmail($user) {
        $confirmationPage = ($this->conf['confirmationPage']) ? $this->conf['confirmationPage'] : $GLOBALS['TSFE']->id;
        $confirmationArray = array(
            $this->prefixId . '[' . 'action' . ']'   => 'userAuth',
            $this->prefixId . '[' . 'authcode' . ']' => $user['user_auth_code']
        );
        $deletingArray = array(
            $this->prefixId . '[' . 'action' . ']'   => 'userDelRequest',
            $this->prefixId . '[' . 'authcode' . ']' => $user['user_auth_code']
        );
        $authLink = t3lib_div::locationHeaderUrl($this->pi_getpageLink($confirmationPage, '', $confirmationArray));
        $deleteLink = t3lib_div::locationHeaderUrl($this->pi_getpageLink($confirmationPage, '', $deletingArray));
        $confirmationLinkParams = ($this->conf['email.']['confirmation.']['linkParams']) ? $this->conf['email.']['confirmation.']['linkParams'] : '';
        $deleteLinkParams = ($this->conf['email.']['confirmationDelete.']['linkParams']) ? $this->conf['email.']['confirmationDelete.']['linkParams'] : '';
        $authLink = sprintf('<a href="%s" %s>%s</a>', $authLink, $confirmationLinkParams, $this->htmlentities($this->pi_getLL('confirmLinkConfirmationText')));
        $deleteLink = sprintf('<a href="%s" %s>%s</a>', $deleteLink, $deleteLinkParams, $this->htmlentities($this->pi_getLL('deletingLinkConfirmationText')));
        if (is_array($this->fieldsData) && count($this->fieldsData)) {
            foreach ($this->fieldsData as $field) {
                $markerArray['###' . strtoupper($field['name']) . '###'] = $this->piVars[$field['name']];
            }
        } else {
            foreach ($user as $key => $value) {
                $markerArray['###' . strtoupper($key) . '###'] = $value;
            }
        }
        $markerArray['###CONFIRMATION_LINK###'] = $authLink;
        $markerArray['###DELETING_LINK###'] = $deleteLink;
        foreach ($user as $key => $value) {
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray['###CONFIRMATION_LINK###'] = $authLink;
        $valueArray['###DELETING_LINK###'] = $deleteLink;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationTextHtml'), $valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationTextText'), $valueArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['confirmationEmail'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['confirmationEmail'] as $markerFunction) {
                $params = array('markerArray' => $markerArray, 'user' => $user);
                $markerArray = t3lib_div::callUserFunction($markerFunction, $params, $this);
            }
        }
        $message = $this->prepareEmailContent('###T3REGISTRATION_CONFIRMATION_EMAIL_HTML###', '###T3REGISTRATION_CONFIRMATION_EMAIL_TEXT###', $markerArray);
        $message['type'] = 'user';
        return $message;
    }

    /**
     * This function prepares the administrator confirmation email message.
     *
     * @param    array $user        the target user of the email
     * @return    array        the type and the HTML content of the message.
     */
    protected function authorizationEmail($user) {
        $confirmationPage = ($this->conf['confirmationPage']) ? $this->conf['confirmationPage'] : $GLOBALS['TSFE']->id;
        $confirmationArray = array(
            $this->prefixId . '[' . 'action' . ']'   => 'adminAuth',
            $this->prefixId . '[' . 'authcode' . ']' => $user['admin_auth_code']
        );
        $authLink = t3lib_div::locationHeaderUrl($this->pi_getpageLink($confirmationPage, '', $confirmationArray));
        $confirmationLinkParams = ($this->conf['email.']['confirmationModerator.']['linkParams']) ? $this->conf['email.']['confirmationModerator.']['linkParams'] : '';
        $authLink = sprintf('<a href="%s" %s>%s</a>', $authLink, $confirmationLinkParams, $this->htmlentities($this->pi_getLL('authorizationLinkConfirmationText')));
        foreach ($this->fieldsData as $field) {
            $markerArray['###' . strtoupper($field['name']) . '###'] = $this->piVars[$field['name']];
        }
        $markerArray['###CONFIRMATION_LINK###'] = $authLink;
        foreach ($user as $key => $value) {
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $valueArray['###CONFIRMATION_LINK###'] = $authLink;
        $markerArray['###DESCRIPTION_HTML_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAuthorizationEmailTextHtml'), $valueArray);
        $markerArray['###DESCRIPTION_TEXT_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAuthorizationEmailTextText'), $valueArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['authorizationEmail'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['authorizationEmail'] as $markerFunction) {
                $params = array('markerArray' => $markerArray, 'user' => $user);
                $markerArray = t3lib_div::callUserFunction($markerFunction, $params, $this);
            }
        }
        $message = $this->prepareEmailContent('###T3REGISTRATION_AUTHORIZATION_EMAIL_HTML###', '###T3REGISTRATION_AUTHORIZATION_EMAIL_TEXT###', $markerArray);
        $message['type'] = 'admin';
        return $message;
    }

    /**
     * This function prepare the email content based on value from flexform
     *
     * @param    string $subpartHTMLMarker        string for HTML marker
     * @param    string $subpartTextMarker        string for Text marker
     * @param    array $markers        array of marker to substitute
     * @return    array        with content HTML and text parts
     */
    protected function prepareEmailContent($subpartHTMLMarker, $subpartTextMarker, $markers) {
        $content = $this->getTemplate();
        $contentText = $this->cObj->getSubpart($content, $subpartTextMarker);
        $contentHTML = $this->cObj->getSubpart($content, $subpartHTMLMarker);
        $message = array();
        if (strlen($contentHTML) > 0 && ($this->emailFormat & HTML)) {
            $message['contentHTML'] = $this->cObj->substituteMarkerArray($contentHTML, $markers);
        }
        if (strlen($contentText) > 0 && ($this->emailFormat & TEXT)) {
            $message['contentText'] = $this->cObj->substituteMarkerArray($contentText, $markers);
        }
        return $message;
    }

    /**
     * This function prepares and sends the email. It can be sent to the user or to the administrator.
     *
     * @param    string $message        the message content of the email
     * @param    array $user        the target user of the email
     * @param    string $subject   subject for email
     * @return    void
     */
    protected function sendEmail($message, $user, $subject) {
        if (t3lib_div::int_from_ver(TYPO3_version) < t3lib_div::int_from_ver('4.5.0')) {
            $mailObject = t3lib_div::makeInstance('t3lib_htmlmail');
            $mailObject->start();
            $mailObject->mailer = 'TYPO3 HTMLMail';
            if (isset($message['contentText'])) {
                $mailObject->addPlain($message['contentText']);
            }
            if (isset($message['contentHTML'])) {
                $mailObject->theParts['html']['content'] = $message['contentHTML'];
                $mailObject->theParts['html']['path'] = '';
                $mailObject->extractMediaLinks();
                $mailObject->extractHyperLinks();
                $mailObject->fetchHTMLMedia();
                $mailObject->substMediaNamesInHTML(0); // 0 = relative
                $mailObject->substHREFsInHTML();
                $mailObject->setHTML($mailObject->encodeMsg($mailObject->theParts['html']['content']));
            }
            switch ($message['type']) {
                case 'user':
                    $mailObject->subject = $this->pi_getLL($subject);
                    $mailObject->setRecipient($user['email']);
                    break;
                case 'admin':
                    if ($this->conf['emailAdmin']) {
                        $adminEmailList = explode(',', $this->conf['emailAdmin']);
                        foreach ($adminEmailList as $email) {
                            $emailAdminTemp = explode(':', $email);
                            $emailAdmin[] = $emailAdminTemp[0];
                        }
                    }
                    $mailObject->subject = $this->pi_getLL($subject);
                    $mailObject->setRecipient(implode(',', $emailAdmin));

                    break;
            }
            $emailFrom = ($this->conf['emailFrom']) ? $this->conf['emailFrom'] : '';
            $emailFromName = ($this->conf['emailFromName']) ? $this->conf['emailFromName'] : '';
            if ($this->conf['emailFrom'] && ($message['type'] != 'admin' || ($this->conf['emailAdmin'] && $message['type'] == 'admin'))) {
                $mailObject->from_email = $emailFrom;
                $mailObject->from_name = $emailFromName;
                $mailObject->setHeaders();
                $mailObject->setContent();
                $mailObject->returnPath = $emailFrom;
                $mailObject->replyto_email = $emailFrom;
                $mailObject->sendtheMail();
            } else {
                if ($this->debugLevel > 0) {
                    if (TYPO3_DLOG) {
                        t3lib_div::devLog('Error during sending email with t3lib_htmlmail', $this->extKey, t3lib_div::SYSLOG_SEVERITY_FATAL, $this->conf);
                    }
                    if ($this->debugLevel > 1) {
                        throw new t3lib_exception('Error during sending email with t3lib_htmlmail');
                    }
                }
            }
        } else {
            $mailObject = t3lib_div::makeInstance('t3lib_mail_Message');
            if (isset($message['contentText'])) {
                $mailObject->addPart($message['contentText'], 'text/plain');
            }
            if (isset($message['contentHTML'])) {
                $mailObject->setBody($message['contentHTML'], 'text/html');
            }
            switch ($message['type']) {
                case 'user':
                    $mailObject->setSubject($this->pi_getLL($subject));
                    $mailObject->setTo(array($user['email']));
                    break;
                case 'admin':
                    if ($this->conf['emailAdmin']) {
                        $adminEmailList = explode(',', $this->conf['emailAdmin']);
                        foreach ($adminEmailList as $email) {
                            $emailAdminTemp = explode(':', $email);
                            if (count($emailAdminTemp) == 2) {
                                $emailAdmin[$emailAdminTemp[0]] = $emailAdminTemp[1];
                            } else {
                                $emailAdmin[] = $emailAdminTemp[0];
                            }

                        }
                    }
                    $mailObject->setSubject($this->pi_getLL($subject));
                    $mailObject->setTo($emailAdmin);
                    break;
            }
            $emailFrom = ($this->conf['emailFrom']) ? $this->conf['emailFrom'] : '';
            $emailFromName = ($this->conf['emailFromName']) ? $this->conf['emailFromName'] : '';
            if ($this->conf['emailFrom'] && ($message['type'] != 'admin' || ($this->conf['emailAdmin'] && $message['type'] == 'admin'))) {
                $mailObject->setFrom(array($emailFrom => $emailFromName))->send();
            } else {
                if ($this->debugLevel > 0) {
                    if (TYPO3_DLOG) {
                        t3lib_div::devLog('Error during sending email with t3lib_mail_Message', $this->extKey, t3lib_div::SYSLOG_SEVERITY_FATAL, $this->conf);
                    }
                    if ($this->debugLevel > 1) {
                        throw new t3lib_exception('Error during sending email with t3lib_mail_Message');
                    }
                }
            }
        }

    }

    /**
     * This function is used by external hook or library to obtain the email format choise
     *
     * @return    string email format
     */
    public function getMailFormat() {
        return $this->emailFormat;
    }


    /**
     * This function defines the emailFormat class variable and set it to 1,2 or 3 depends on the type of mail format user chose
     *
     * @return    void
     */
    protected function setEmailFormat() {
        $emailFormat = ($this->conf['contactEmailMode']) ? $this->conf['contactEmailMode'] : '';
        $emailFormat = explode(',', $emailFormat);
        if (is_array($emailFormat)) {
            if (in_array('html', $emailFormat)) {
                $this->emailFormat = $this->emailFormat | 1;
            }
            if (in_array('text', $emailFormat)) {
                $this->emailFormat = $this->emailFormat | 2;
            }
        }
    }


    /**************************************AUTHORIZATION PROCESS***********************************/

    /**
     * This function defines the action to do when the page is load. If the user loads the page, the user-confiramtion is done. If the administrator
     * loads the page, the admin-confirmation is done (it happens in double-optin confirmation mode). If the "action" parameter value in piVars array is
     * "delete", when the page is load the deletion is confirmed.
     *
     * @return    void
     */
    protected function argumentsFromUrlCheck() {
        $this->externalAction['active'] = false;
        $this->externalAction['type'] = '';
        $this->externalAction['location'] = 'local';
        if (isset($this->piVars['action'])) {
            //ci sono parametri che possono definire
            switch ($this->piVars['action']) {
                case 'userAuth':
                case 'adminAuth':
                case 'userDelRequest':
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
                case 'resendConfirmationCode':
                    $this->externalAction['type'] = 'sendAgainConfirmationEmail';
                    $this->externalAction['active'] = true;
                    break;
                default:
                    if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['externalAction'])) {
                        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['externalAction'] as $fieldFunction) {
                            $params = array('externalAction' => $this->externalAction, 'data' => $this->piVars);
                            t3lib_div::callUserFunction($fieldFunction, $params, $this);
                            $this->externalAction = $params['externalAction'];
                        }
                    }
            }
        }
    }

    /**
     * This function calls the methods for preparing and for sending the email.
     *
     * @param    string $action        the action to be performed. Possible values can be "deleteRequest", "confirmationRequest" or "authorizationRequest".
     * @param    array $user        the target user of the email
     * @return    void
     */
    protected function prepareAndSendEmailSubpart($action, $user) {
        switch ($action) {
            case 'deleteRequest':
                $this->sendEmail($this->deleteEmail($user), $user, 'mailToUserDeleteSubject');
                break;
            case 'confirmationRequest':
            case 'sendConfirmationRequest':
                $this->sendEmail($this->confirmationEmail($user), $user, 'mailToUserSubject');
                break;
            case 'authorizationRequest':
                $this->sendEmail($this->authorizationEmail($user), $user, 'mailToAdminSubject');
                break;
        }
    }

    /**
     * This function checks if the user can be confirmed and it calls the method updateConfirmedUser for updating the user into database.
     *
     * @return    boolean        true if the user was been correctly confirmed, false otherwise
     */
    protected function confirmUserDeletion() {
        $userAuthCode = $this->piVars['authcode'];
        $where = 'user_auth_code=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($userAuthCode, 'fe_users');
        $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', $where);
        if ($GLOBALS['TYPO3_DB']->sql_num_rows($resource) > 0) {
            $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
            $this->deleteUser($user);
            return $this->confirmUserDeletionTemplate($user);
        } else {
            return $this->cObj->stdWrap($this->pi_getLL('confirmationLinkNotFound'), $this->conf['error.']['confirmedErrorWrap.']);
        }
    }

    /**
     * This function creates the HTML code to show
     *
     * @param    array $user        array user to delete
     * @return    string        HTML content to show
     */
    protected function confirmUserDeletionTemplate($user) {
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content, '###T3REGISTRATION_DELETE_CONFIRMATION###');
        foreach ($user as $key => $value) {
            $markerArray['###' . strtoupper($key) . '###'] = $value;
        }
        $contentArray['###DELETE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deletionConfirmedText'), $markerArray);
        $contentArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        return $this->cObj->substituteMarkerArrayCached($content, $contentArray);
    }

    /**
     * This function delete user from database
     *
     * @param    array          user data to overwrite
     * @return    boolean        true if the user was been correctly confirmed, false otherwise
     */
    protected function deleteUser($user) {
        if (!$this->conf['enableTemplateTest']) {
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeDeleteUser'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeDeleteUser'] as $userFunction) {
                    $params['user'] = $user;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                    $user = $params['user'];
                }
            }
            if (isset($this->conf['delete.']['deleteRow']) && $this->conf['delete.']['deleteRow']) {
                $GLOBALS['TYPO3_DB']->exec_DELETEquery('fe_users', 'uid=' . $user['uid']);
                //used for writing log
                $query = $GLOBALS['TYPO3_DB']->DELETEquery('fe_users', 'uid=' . $user['uid']);
            } else {
                $user['disable'] = 1;
                $user['deleted'] = 1;
                $user['tstamp'] = time();
                $user['user_auth_code'] = '';
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid=' . $user['uid'], $user);
                //used for writing log
                $query = $GLOBALS['TYPO3_DB']->UPDATEquery('fe_users', 'uid=' . $user['uid'], $user);

            }

            if ($this->debugLevel && TYPO3_DLOG) {
                t3lib_div::devLog('user ' . $user['username'] . ' was deleted', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
                t3lib_div::sysLog('user ' . $user['username'] . ' was deleted', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO);
                if ($this->debugLevel > 1) {
                    t3lib_div::devLog('user ' . $user['username'] . ' was deleted QUERY: <b>' . $query . '</b>', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
                }
            }

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterDeleteUser'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterDeleteUser'] as $userFunction) {
                    $params['user'] = $user;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                }
            }
        }
    }

    /**
     * This function manages the confirmation process with double optin or moderation control
     *
     * @return    string HTML        code
     */
    protected function confirmationProcessControl() {
        switch ($this->externalAction['parameter']) {
            case 'userAuth':
                $userAuthCode = $this->piVars['authcode'];
                $where = 'user_auth_code=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($userAuthCode, 'fe_users');
                $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', $where);
                if ($GLOBALS['TYPO3_DB']->sql_num_rows($resource) > 0) {
                    $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                    //call confirmation
                    $content = $this->confirmUser($user);
                    if (strlen($user['admin_auth_code']) == 0) {
                        if ($this->conf['autoLoginAfterConfirmation'] == 1) {
                            $this->autoLogin($user['uid']);
                            $sessionData = array(
                                'text' => $content
                            );
                            $GLOBALS['TSFE']->fe_user->setAndSaveSessionData('autoLogin', $sessionData);
                            $redirectParametersArray = array(
                                $this->prefixId . '[' . 'action' . ']' => 'redirectOnLogin'
                            );
                            $redirectLink = $this->pi_getpageLink($GLOBALS['TSFE']->id, '', $redirectParametersArray);
                            header('Location: ' . t3lib_div::locationHeaderUrl($redirectLink));
                            exit;
                        }
                    }
                } else {
                    return $this->cObj->stdWrap($this->pi_getLL('confirmationLinkNotFound'), $this->conf['error.']['confirmedErrorWrap.']);
                }
                break;
            case 'adminAuth':
                $adminAuthCode = $this->piVars['authcode'];
                $where = 'admin_auth_code=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($adminAuthCode, 'fe_users');
                $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', $where);
                if ($GLOBALS['TYPO3_DB']->sql_num_rows($resource) > 0) {
                    $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                    $content = $this->authorizedUser($user);
                } else {
                    return $this->cObj->stdWrap($this->pi_getLL('confirmationLinkNotFound'), $this->conf['error.']['confirmedErrorWrap.']);
                }
                break;
            case 'userDelRequest':
                return $this->confirmUserDeletion();
                break;
        }
        return $content;
    }

    /**
     * This function checks if the user can be confirmed and it calls the method updateConfirmedUser for updating the user into database.
     *
     * @param    array          user data
     * @return    boolean        true if the user was been correctly confirmed, false otherwise
     */
    protected function confirmUser($user) {
        $user = $this->updateConfirmedUser($user);
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content, '###T3REGISTRATION_CONFIRMEDUSER###');
        foreach ($user as $key => $value) {
            $markerArray['###' . strtoupper($key) . '###'] = $value;
        }
        $confirmationText = (strlen($user['admin_auth_code']) == 0) ? $this->pi_getLL('confirmationFinalText') : $this->pi_getLL('confirmationWaitingAuthText');
        $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($confirmationText, $markerArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        return $this->cObj->substituteMarkerArrayCached($content, $markerArray);
    }


    /**
     * This function confirms the user by updating the user record into fe_users database table.
     *
     * @param    array $user user data
     * @return    void
     */
    protected function updateConfirmedUser($user) {
        $groupsBeforeConfirmation = (strpos($this->conf['preUsergroup'],','))?explode(',', $this->conf['preUsergroup']):array();
        $groupsAfterConfirmation = (strpos($this->conf['postUsergroup'],','))?explode(',', $this->conf['postUsergroup']):array();
        $usergroup = explode(',', $user['usergroup']);
        $newUserGroup = array();
        foreach ($usergroup as $group) {
            if (!in_array($group, $groupsBeforeConfirmation)) {
                $newUserGroup[] = $group;
            }
        }
        foreach ($groupsAfterConfirmation as $group) {
            if (!in_array($group, $newUserGroup)) {
                $newUserGroup[] = $group;
            }
        }
        $user['user_auth_code'] = '';
        $user['usergroup'] = implode(',', $newUserGroup);
        $user['tstamp'] = time();
        if (strlen($user['admin_auth_code']) == 0) {
            $user['disable'] = 0;
        }

        if (!$this->conf['enableTemplateTest']) {
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateConfirmedUser'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateConfirmedUser'] as $userFunction) {
                    $params['user'] = $user;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                    $user = $params['user'];
                }
            }
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid=' . $user['uid'], $user);

            if ($this->debugLevel && TYPO3_DLOG) {
                t3lib_div::devLog('user ' . $user['username'] . ' has just confirmed', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
                t3lib_div::sysLog('user ' . $user['username'] . ' was just confirmed', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO);
                if ($this->debugLevel > 1) {
                    t3lib_div::devLog('user ' . $user['username'] . ' has just confirmed QUERY: <b>' . $GLOBALS['TYPO3_DB']->UPDATEquery('fe_users', 'uid=' . $user['uid'], $user) . '</b>', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
                }
            }

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateConfirmedUser'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateConfirmedUser'] as $userFunction) {
                    $params['user'] = $user;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                }
            }
        }
        if (strlen($user['admin_auth_code']) == 0) {
            if ($this->conf['autoLoginAfterConfirmation'] == 1) {
                $this->autoLogin($user['uid']);
            }
            $this->userIsRegistered('userAuth', $user);
        }
        return $user;


    }

    /**
     * This function checks if the user can be authorized and it calls the method updateAdminAuthorizedUser for updating the user into database.
     *
     * @param    array $user user data
     * @return    boolean        true if the user was been correctly confirmed, false otherwise
     */
    protected function authorizedUser($user) {
        $this->updateAdminAuthorizedUser($user);
        $content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content, '###T3REGISTRATION_CONFIRMEDAUTHORIZEDUSER###');
        foreach ($user as $key => $value) {
            $markerArray['###' . strtoupper($key) . '###'] = $value;
        }
        $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('confirmationAuthorizationText'), $markerArray);
        $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
        return $this->cObj->substituteMarkerArrayCached($content, $markerArray);
    }

    /**
     * This function authorizes user by upadating the user record into fe_users database table.
     *
     * @param    array $user        the user to be authorized
     * @return    void
     */
    protected function updateAdminAuthorizedUser($user) {
        $user['admin_auth_code'] = '';
        $user['admin_disable'] = 0;
        $user['uid'] = $user['uid'];
        if (strlen($user['user_auth_code']) == 0) {
            $user['disable'] = 0;
        }
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeAdminAuthorizedUser'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeAdminAuthorizedUser'] as $userFunction) {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
                $user = $params['user'];
            }
        }
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid=' . $user['uid'], $user);

        if ($this->debugLevel && TYPO3_DLOG) {
            t3lib_div::devLog('user ' . $user['username'] . ' has just confirmed by administrator', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
            t3lib_div::sysLog('user ' . $user['username'] . ' has just confirmed by administrator', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO);
            if ($this->debugLevel > 1) {
                t3lib_div::devLog('user ' . $user['username'] . ' has just confirmed by administrator QUERY: <b>' . $GLOBALS['TYPO3_DB']->UPDATEquery('fe_users', 'uid=' . $user['uid'], $user) . '</b>', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO);
            }
        }

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterAdminAuthorizedUser'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterAdminAuthorizedUser'] as $userFunction) {
                $params['user'] = $user;
                t3lib_div::callUserFunction($userFunction, $params, $this);
            }
        }
        //send email to user after Authorization
        if (!$user['disable'] && $this->conf['sendUserEmailAfterAuthorization']) {
            $message = $this->sendAdviceAfterAuthorization($user);
            $this->userIsRegistered('adminAuth', $user);
            $this->sendEmail($message, $user, 'mailToUserAfterAuthorizationSubject');
        }

    }

    /**
     * This method is used to trigger hook after user registration
     *
     * @param    string specify the name of the event (it could be authorization by admin or user registration)
     * @param    string user data
     * @return   void
     */
    protected function userIsRegistered($lastEvent, $user) {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['confirmedProcessComplete'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['confirmedProcessComplete'] as $userFunction) {
                $params = array('user' => $user, 'lastEvent' => $lastEvent);
                t3lib_div::callUserFunction($userFunction, $params, $this);
            }
        }

    }

    /**
     * This function makes auto login form confirmed user
     *
     * @param    integer $uid        integer id of confirmed user
     * @return    void
     */
    protected function autoLogin($uid) {
        $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid=' . $uid);
        if (($feUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource)) !== FALSE) {
            $loginData = array(
                'uname'  => $feUser['username'], //username
                'uident' => $feUser['password'], //password
                'status' => 'login'
            );
            //do not use a particular pid
            $GLOBALS['TSFE']->fe_user->checkPid = ($this->conf['userFolder']) ? 1 : 0;
            $GLOBALS['TSFE']->fe_user->checkPid_value = ($this->conf['userFolder']) ? $this->conf['userFolder'] : $GLOBALS['TSFE']->id;
            $info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
            $user = $GLOBALS['TSFE']->fe_user->fetchUserRecord($info['db_user'], $loginData['uname']);
            if ($GLOBALS['TSFE']->fe_user->compareUident($user, $loginData)) {
                //login successfull
                $GLOBALS['TSFE']->fe_user->createUserSession($user);
                $GLOBALS['TSFE']->fe_user->loginSessionStarted = TRUE;
                $GLOBALS['TSFE']->fe_user->user = $GLOBALS["TSFE"]->fe_user->fetchUserSession();
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
            }
        }
    }


    /**
     * This function shows the delete link.
     *
     * @return    string        the link HTML code
     */
    protected function showDeleteLink() {
        if ($this->conf['disableDeleteLink']==1)
        	return '';
    	$content = $this->getTemplate();
        $content = $this->cObj->getSubpart($content, '###T3REGISTRATION_DELETE###');
        foreach ($this->feLoggedInUser as $key => $value) {
            $valueArray['###' . strtoupper($key) . '###'] = $value;
        }
        $deleteArray = array(
            $this->prefixId . '[action]' => 'delete'
        );
        $link = $this->pi_getPageLink($GLOBALS['TSFE']->id, '', $deleteArray);
        $link = sprintf('<a href="%s">%s</a>', $link, $this->pi_getLL('deleteLinkText'));
        $contentArray['###DELETE_LINK###'] = $link;
        $valueArray['###DELETE_LINK###'] = $link;
        $contentArray['###DELETE_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('deleteDescriptionText'), $valueArray);
        return $this->cObj->substituteMarkerArrayCached($content, $contentArray);
    }


    /******************************************GENERIC FUNCTIONS*****************/


    /**
     * This function transforms recursively the .add property of javascript into key additionalEval
     *
     * @param    array $arrayToTraverse        array child array
     * @param    array $parentArray        array root array
     * @param    string $parentKey        string name of the parent key
     * @return    void
     */
    protected function addFunctionReplace($arrayToTraverse, &$parentArray, $parentKey = '') {
        if (is_array($arrayToTraverse)) {
            foreach ($arrayToTraverse as $key => $item) {
                if ($key === 'add') {
                    unset($parentArray[$parentKey]);
                    $parentArray['additionalEval'] = $item;
                } else {
                    if (array_key_exists($parentKey, $parentArray)) {
                        $this->addFunctionReplace($parentArray[$parentKey][$key], $parentArray[$parentKey], $key);
                    } else {
                        $this->addFunctionReplace($parentArray[$key], $parentArray, $key);
                    }
                }
            }
        }
    }


    /**
     * This function allow to extract a specific key from private variable $fieldsData
     *
     * @param    string $name        string key of array $fieldsData
     * @return    array        value of $this->fieldsData[$name]
     */
    public function getField($name) {
        return (array_key_exists($name, $this->fieldsData)) ? $this->fieldsData[$name] : array();
    }


    /**
     * This function checks if username contains required and unique, looking for it into alternative username fields
     *
     * @return    boolean        if true all ok, otherwise return error description
     */
    protected function controlIfUsernameIsCorrect() {
        if (isset($this->conf['usernameField']) && strlen($this->conf['usernameField'])) {
            if (isset($this->fieldsData[$this->conf['usernameField']]) && is_array($this->fieldsData[$this->conf['usernameField']])) {
                $evaluation = $this->getEvaluationRulesList($this->conf['usernameField']);
                if (in_array('required', $evaluation) && (in_array('unique', $evaluation) || in_array('uniqueInPid', $evaluation))) {
                    return true;
                } else {
                    return $this->pi_getLL('usernameIsNotCorrect');
                }
            } else {
                return $this->pi_getLL('usernameIsNotDefined');
            }
        } else {
            return $this->pi_getLL('usernameIsNotDefined');
        }
    }

    /**
     * Method to test the email data before show registration form
     *
     * @return    boolean true if test is passed
     */
    protected function controlEmailAndMethod() {
        if ($this->conf['approvalProcess']) {
            if (!isset($this->fieldsData['email'])) {
                return $this->pi_getLL('approvalProcessIsNotDefined');
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * This function loads TCA fields array into $this->TCAField array
     *
     * @return    void
     */
    protected function loadTCAField() {
        $GLOBALS['TSFE']->includeTCA();
        $this->TCAField = $GLOBALS['TCA']['fe_users']['columns'];
    }


    /**
     * This function merges TCA fields with configuration fields
     * return void
     *
     * @return   void
     */
    protected function mergeTCAFieldWithConfiguration() {
        foreach ($this->fieldsData as $key => $item) {
            if ($item['type'] == 'databaseField' && isset($this->TCAField[$item['field']]) && is_array($this->TCAField[$item['field']])) {
                if ($this->testUploadFolderField($this->TCAField[$item['field']]) && $this->testUploadFolderField($this->fieldsData[$key])) {
                    $this->fieldsData[$key]['config']['uploadfolder'] = UPLOAD_FOLDER;
                }
                $this->fieldsData[$key] = t3lib_div::array_merge_recursive_overrule($this->TCAField[$item['field']], $this->fieldsData[$key]);
            }
        }
    }

    /**
     * This function checks if upload folder key of config array of specified field is defined
     *
     * @param    array $field        array field configuration array
     * @return    boolean        false if not set, otherwise true
     */
    protected function testUploadFolderField($field) {
        if (isset($field['config']['internal_type']) && $field['config']['internal_type'] == 'file' && $field['config']['uploadfolder'] == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Text to insert for resending email with confirmation
     *
     * @return    string text
     */
    protected function getTextToResendConfirmationEmail() {
        $text = $this->pi_linkToPage($this->pi_getLL('toResendConfirmationEmailText'), $GLOBALS['TSFE']->id, '', array($this->prefixId . '[action]' => 'resendConfirmationCode'));
        $text = $this->cObj->stdWrap($text, $this->conf['form.']['resendConfirmationCode.']['stdWrap.']);
        return $text;
    }

    /**
     * This function returns the wright template to use. If no content is found, the function returns false.
     *
     * @return    string        the whole HTML template
     */
    protected function getTemplate() {
        $content = $this->cObj->fileResource($this->cObj->stdWrap($this->conf['templateFile'], $this->conf['templateFile.']));
        if ($content) {
            return $content;
        }
        return false;
    }


    /**
     * This function convert a piVars field comma separated values into array
     *
     * @param    string $fieldName        string field to transform
     * @return    void
     */
    protected function fileFieldTransform2Array($fieldName) {
        $this->piVars[$fieldName] = explode(',', $this->piVars[$fieldName]);
    }


    /**
     * This method insert user in fe_users database table. If automatic password generation is set to true and no password is set by the user, a new
     * password is automatically generated. It also calls the methods for sending emails.
     *
     * @return    void
     */
    protected function insertUser() {
        $this->postElaborateData();
        if ($this->conf['passwordGeneration'] || !isset($this->piVars['password']) || strlen($this->piVars['password']) == 0) {
            $this->piVars['password'] = substr(md5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']), 0, 8);
        }
        //call function to verify the type of verification
        $user = $this->setAuthCode();
        $user['usergroup'] = ($this->userAuth || $this->adminAuth) ? $this->conf['preUsergroup'] : $this->conf['postUsergroup'];
        foreach ($this->fieldsData as $field) {
            if ($field['type'] == 'databaseField') {
                if (!isset($field['dbHTMLEntities']) || (isset($field['dbHTMLEntities']) && $field['dbHTMLEntities'] == 0)) {
                    $user[$field['field']] = (is_array($this->piVars[$field['name']])) ? implode(',', $this->piVars[$field['name']]) : $this->piVars[$field['name']];
                } else {
                    $user[$field['field']] = (is_array($this->piVars[$field['name']])) ? implode(',', $this->piVars[$field['name']]) : $this->htmlentities($this->piVars[$field['name']]);
                }
            }
        }
        $user['username'] = $this->getUsername();

        //this situation happens only if simultaneously 2 or more users use the same username
        $folder = (in_array('unique', $this->getEvaluationRulesList($this->conf['usernameField']))) ? 0 : (($this->conf['userFolder']) ? $this->conf['userFolder'] : $GLOBALS['TSFE']->id);
        if (!$this->checkUniqueField($user['username'], $this->fieldsData[$this->conf['usernameField']], $folder)) {
            $this->getForm();
        }

        $user['pid'] = ($this->conf['userFolder']) ? $this->conf['userFolder'] : $GLOBALS['TSFE']->id;
        $user['disable'] = ($this->conf['disabledBeforeConfirmation'] && ($this->userAuth || $this->adminAuth)) ? 1 : 0;
        $user['crdate'] = time();
        $user['tstamp'] = $user['crdate'];
        if (!$this->conf['enableTemplateTest']) {
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeInsertUser'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeInsertUser'] as $userFunction) {
                    $params['user'] = $user;
                    $params['piVars'] = $this->piVars;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                    $user = $params['user'];
                }
            }
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $user);
            $user['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();

            if ($this->debugLevel && TYPO3_DLOG) {
                t3lib_div::devLog('user ' . $user['username'] . ' was created', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
                t3lib_div::sysLog('user ' . $user['username'] . ' was created', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO);
                if ($this->debugLevel > 1) {
                    t3lib_div::devLog('user ' . $user['username'] . ' was created QUERY: <b>' . $GLOBALS['TYPO3_DB']->INSERTquery('fe_users', $user) . '</b>', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO);
                }
            }

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterInsertUser'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterInsertUser'] as $userFunction) {
                    $params['user'] = $user;
                    $params['piVars'] = $this->piVars;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                }
            }
        }
        if ($this->userAuth) {
            //send email
            $this->prepareAndSendEmailSubpart('confirmationRequest', $user);
        }
        if ($this->adminAuth) {
            //send email
            $this->prepareAndSendEmailSubpart('authorizationRequest', $user);
        }
        if (!$this->userAuth && !$this->adminAuth) {
            if ($this->conf['autoLoginAfterConfirmation'] == 1) {
                $this->autoLogin($user['uid']);
            }
            $this->userIsRegistered('noAuth', $user);
        }

    }


    /**
     * This function resend the confirmation code and show the form to request it
     *
     * @return    string        HTML code to display
     */
    protected function sendAgainConfirmationEmail() {
        if ($this->piVars['posted'] == 1 && $this->piVars[$this->conf['usernameField']] && t3lib_div::inList($this->conf['approvalProcess'], 'doubleOptin')) {
            $resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', $this->conf['usernameField'] . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->piVars[$this->conf['usernameField']], 'fe_users') . ' AND deleted=0 AND disable=1');
            if ($GLOBALS['TYPO3_DB']->sql_num_rows($resource) == 1) {
                //invia la mail
                $user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource);
                $this->prepareAndSendEmailSubpart('sendConfirmationRequest', $user);
                $content = $this->getTemplate();
                $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_SENDCONFIRMATIONEMAIL_TEXT');
                $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->stdWrap($this->pi_getLL('sendConfirmationCodeTextUserFound'), $this->conf['sendConfirmationObject.']['text.']['stdWrap.']);
                $content = $this->cObj->substituteMarkerArrayCached($content, $markerArray);
                return $content;
            } else {
                if ($this->conf['sendConfirmationObject.']['showNotFoundText']) {
                    $content = $this->getTemplate();
                    $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_SENDCONFIRMATIONEMAIL_TEXT');
                    $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->stdWrap($this->pi_getLL('sendConfirmationCodeTextUserNotFound'), $this->conf['sendConfirmationObject.']['text.']['stdWrap.']);
                    $content = $this->cObj->substituteMarkerArrayCached($content, $markerArray);
                    return $content;
                }
            }
        } else {
            if (t3lib_div::inList($this->conf['approvalProcess'], 'doubleOptin')) {
                $content = $this->getTemplate();
                $content = $this->cObj->getSubpart($content, 'T3REGISTRATION_SENDCONFIRMATIONEMAIL_FORM');
                $confirmationPage = ($this->conf['confirmationPage']) ? $this->conf['confirmationPage'] : $GLOBALS['TSFE']->id;
                $confirmationPage = $this->pi_getpageLink($confirmationPage);
                $id = ($this->conf['sendConfirmationObject.']['params']) ? $this->conf['sendConfirmationObject.']['params'] : '';
                $requestInput = sprintf('<input type="text" %s name="%s" />', $id, $this->prefixId . '[' . $this->conf['usernameField'] . ']');
                $markerArray['###REQUEST###'] = $this->cObj->stdWrap($requestInput, $this->conf['sendConfirmationObject.']['stdWrap.']);
                $formId = ($this->conf['form.']['id']) ? $this->conf['form.']['id'] : 't3Registration-' . substr(md5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']), 0, 8);
                $action = $this->pi_getPageLink($GLOBALS['TSFE']->id);
                $submitButton = sprintf('<input type="submit" %s name="' . $this->prefixId . '[submit]" value="%s" />', $this->cObj->stdWrap($this->conf['form.']['submitButton.']['params'], $this->conf['form.']['submitButton.']['params.']), $this->pi_getLL('sendConfirmationCode'));
                $submitButton = $this->cObj->stdWrap($submitButton, $this->conf['form.']['submitButton.']['stdWrap.']);
                $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->stdWrap($this->pi_getLL('sendConfirmationCodeText'), $this->conf['sendConfirmationObject.']['text.']['stdWrap.']);
                $markerArray['###LABEL###'] = ($this->pi_getLL($this->conf['usernameField'] . 'Label')) ? $this->pi_getLL($this->conf['usernameField'] . 'Label') : ((isset($this->fieldsData[$this->conf['usernameField']]['label'])) ? $this->languageObj->sL($this->fieldsData[$this->conf['usernameField']]['label'], true) : '');
                $hiddenArray[] = '<input type="hidden" name="' . $this->prefixId . '[posted]" value="1" />';
                if ($this->conf['form.']['markerButtons']) {
                    $markerArray['###FORM_BUTTONS###'] = sprintf('%s' . chr(10) . $submitButton, implode(chr(10), $hiddenArray));
                    $endForm = '';
                } else {
                    $endForm = sprintf('%s' . chr(10) . $submitButton, implode(chr(10), $hiddenArray));
                }
                $content = $this->cObj->substituteMarkerArrayCached($content, $markerArray);
                $content = sprintf('<form id="%s" action="%s" method="post" enctype="%s">' . chr(10) . '%s' . chr(10) . '%s' . chr(10) . '</form>', $formId, $confirmationPage, $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'], $content, $endForm);
                return $content;
            }
            return '';
        }
    }


    /**
     * This method insert user in fe_users database table. If automatic password generation is set to true and no password is set by the user, a new
     * password is automatically generated. It also calls the methods for sending emails.
     *
     * @return    void
     */
    protected function updateUserProfile() {
        if ($this->userLogged) {
            $this->postElaborateData();
            foreach ($this->fieldsData as $field) {
                if ($field['type'] == 'databaseField' && $field['hideInChangeProfile'] == 0) {
                    if (!isset($field['dbHTMLEntities']) || (isset($field['dbHTMLEntities']) && $field['dbHTMLEntities'] == 0)) {
                        $user[$field['field']] = (is_array($this->piVars[$field['name']])) ? implode(',', $this->piVars[$field['name']]) : $this->piVars[$field['name']];
                    } else {
                        $user[$field['field']] = (is_array($this->piVars[$field['name']])) ? implode(',', $this->piVars[$field['name']]) : $this->htmlentities($this->piVars[$field['name']]);
                    }
                }
            }
            //Inserire hook per aggiornare i campi
            $user['tstamp'] = time();

            if (!$this->conf['enableTemplateTest']) {
                if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateUser'])) {
                    foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['beforeUpdateUser'] as $userFunction) {
                        $params['user'] = $user;
                        t3lib_div::callUserFunction($userFunction, $params, $this);
                        $user = $params['user'];
                    }
                }
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid=' . $this->feLoggedInUser['uid'], $user);
                if ($this->debugLevel && TYPO3_DLOG) {
                    t3lib_div::devLog('user ' . $user['email'] . ' updates his profile', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
                    t3lib_div::sysLog('user ' . $user['username'] . ' updates his profile', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO);
                    if ($this->debugLevel > 1) {
                        t3lib_div::devLog('user ' . $user['email'] . ' updates his profile QUERY: <b>' . $GLOBALS['TYPO3_DB']->UPDATEquery('fe_users', 'uid=' . $this->feLoggedInUser['uid'], $user) . '</b>', $this->extKey, t3lib_div::SYSLOG_SEVERITY_INFO, $user);
                    }
                }
            }
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateUser'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3registration']['afterUpdateUser'] as $userFunction) {
                    $params['user'] = $user;
                    t3lib_div::callUserFunction($userFunction, $params, $this);
                }
            }
        }
    }

    /**
     * This function returns the username. If no one is specified by the user, it automatically generates a username.
     *
     * @return    string        username
     */
    protected function getUsername() {
        if (isset($this->piVars[$this->conf['usernameField']])) {
            return $this->piVars[$this->conf['usernameField']];
        } else {
            return 'user-' . md5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
        }
    }

    /**
     * This function set the auth code for user confirmation or moderation confirmation
     *
     * @param    array $user user data
     * @return    array        user data updated
     */
    protected function setAuthCode() {
        $user = array();
        $authProcessList = explode(',', $this->conf['approvalProcess']);
        foreach ($authProcessList as $process) {
            switch ($process) {
                case 'doubleOptin':
                    $user['user_auth_code'] = md5('doubleOptin' . time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                    $this->userAuth = true;
                    break;
                case 'adminApproval':
                    $user['admin_auth_code'] = md5('adminApproval' . time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                    $this->adminAuth = true;
                    $user['admin_disable'] = 1;
                    break;
            }
        }
        return $user;
    }


    /**
     * This function cheks if you're into change profile process
     *
     * @return    boolean        true if user is in profile, false otherwise
     */
    protected function changeProfileCheck() {
        if ($this->userLogged && !isset($this->piVars['submitted']) && !isset($this->piVars['sendConfirmation'])) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * This function shows HTML after redirect for autologin
     * @return    string HTML
     */
    protected function showOnAutoLogin() {
        $sessionData = $GLOBALS['TSFE']->fe_user->getSessionData('autoLogin');
        if (isset($sessionData)) {
            return $sessionData['text'];
        } else {
            $markerArray = array();
            $content = $this->getTemplate();
            $content = $this->cObj->getSubpart($content, '###T3REGISTRATION_CONFIRMEDONREDIRECT###');
            foreach ($this->fieldsData as $field) {
                $markerArray['###' . strtoupper($field['name']) . '###'] = $this->feLoggedInUser[$field['field']];
            }
            $markerArray['###DESCRIPTION_TEXT###'] = $this->cObj->substituteMarkerArrayCached($this->pi_getLL('ConfirmedOnRedirectText'), $markerArray);
            $markerArray['###SIGNATURE###'] = $this->pi_getLL('signature');
            return $this->cObj->substituteMarkerArrayCached($content, $markerArray);
        }
    }


    /**
     * This function remove dots from keys of the passed array.
     *
     * @param    array $sourceArray        the array to be modified
     * @return    array
     */
    public function removeDotFromArray($sourceArray) {
        $finalArray = array();
        foreach ($sourceArray as $key => $item) {
            if (is_array($item)) {
                $finalArrayKey = preg_replace('/\./', '', $key);
                $finalArrayItem = $this->removeDotFromArray($item);
            } else {
                $finalArrayItem = $item;
                $finalArrayKey = $key;
            }
            $finalArray[$finalArrayKey] = $finalArrayItem;
        }
        return $finalArray;
    }

    /**
     * This function remove all TS configuration for fields not in plugin.
     *
     * @param    array $arrayToElaborate        the array to be modified
     * @return    array array without unused field
     */
    protected function removeUnusedFields($arrayToElaborate) {
        foreach ($arrayToElaborate as $key => $value) {
            if (!isset($this->fieldsData[$key])) {
                unset($arrayToElaborate[$key]);
            }
        }
        return $arrayToElaborate;
    }


    /**
     * This function execute the replacing of html entities with UTF-8 encoding
     *
     * @param    string $string string to manipulate
     * @return    string htmlentitiesed string
     */
    protected function htmlentities($string) {
        if ($GLOBALS['TSFE']->tmpl->setup['config.']['renderCharset']) {
            $encoding = $GLOBALS['TSFE']->tmpl->setup['config.']['renderCharset'];
        } else {
            $encoding = 'UTF-8';
        }
        return htmlentities($string, ENT_QUOTES, $encoding);
    }

    /**
     * Function to removes all marker into the template after replace process
     *
     * @param    string        $content content to replace
     * @return    string        content cleared
     */
    protected function removeAllMarkers($content) {
        $markers = array();
        $subparts = array();
        preg_match_all('/<!--[\t]*###([A-Z_0-9]*)_FIELD###/U', $content, $matches, PREG_PATTERN_ORDER);
        foreach ($matches[1] as $key => $item) {
            if (strpos($item, 'ERROR') === false) {
                if (!in_array($item, $markers)) {
                    $subparts['###' . $item . '_FIELD###'] = '';
                }
            }
        }
        preg_match_all('/###([A-Z_0-9]*)_[VALUE|LABEL]*###/U', $content, $matches, PREG_PATTERN_ORDER);
        foreach ($matches[1] as $key => $item) {
            if (!in_array($item, $markers)) {
                $markers['###' . $item . '_VALUE###'] = '';
                $markers['###' . $item . '_LABEL###'] = '';
            }
        }
        $content = $this->cObj->substituteMarkerArrayCached($content, $markers, $subparts);
        return $content;
    }

    /*****************************************TEMPLATE TEST FUNCTIONS*****************/


    /**
     * Function to show specific test of the template
     * @return string template to show
     */
    protected function testTemplateProcess() {
        $warning = tx_t3registration_checkstatus::getMessage($this->pi_getll('testWarningTitle'), $this->pi_getll('testWarningMessage'), 'warning');
        switch ($this->conf['stepToTest']) {
            case 'registration':
                $this->piVars = array();
                return $warning . $this->getForm();
                break;
            case 'registrationPostWithErrors':
                $this->piVars = $this->testGetUser(false);
                $this->piVars['submitted'] = 1;
                return $warning . $this->getForm();
                break;
            case 'preview':
                $this->piVars = $this->testGetUser();
                $this->piVars['submitted'] = 1;
                return $warning . $this->getForm();
                break;
            case 'userSaved':
                $this->piVars = $this->testGetUser();
                return $warning . $this->endRegistration();
                break;
            case 'confirmationEmail':
                $user = $this->testGetUser();
                $this->prepareAndSendEmailSubpart('confirmationRequest', $user);
                return tx_t3registration_checkstatus::getMessage($this->pi_getll('testMailTitle'), sprintf($this->pi_getll('testConfirmationUserEmailSent'), $user['email']), 'info');
                break;
            case 'adminEmail':
                $user = $this->testGetUser();
                $this->prepareAndSendEmailSubpart('authorizationRequest', $user);
                return tx_t3registration_checkstatus::getMessage($this->pi_getll('testMailTitle'), $this->pi_getll('testConfirmationAdminEmailSent'), 'info');
                break;
            case 'userConfirmationPageWithAdminConfirmed':
                $user = $this->testGetUser();
                $user['admin_auth_code'] = 0;
                return $warning . $this->confirmUser($user);
                break;
            case 'adminConfirmationPage':
                $user = $this->testGetUser();
                return $warning . $this->authorizedUser($user);
                break;
            case 'userConfirmationPageWithoutAdminConfirmed':
                $user = $this->testGetUser();
                $user['admin_auth_code'] = 1;
                return $warning . $this->confirmUser($user);
                break;
            case 'wrongLink':
                $this->piVars['authcode'] = '';
                $this->externalAction['parameter'] = 'userAuth';
                return $warning . $this->confirmationProcessControl();
                break;
            case 'editOK':
                $previousLoginUser = $GLOBALS['TSFE']->loginUser;
                $previousLoggedUser = ($GLOBALS['TSFE']->loginUser) ? $this->feLoggedInUser : array();
                $GLOBALS['TSFE']->loginUser = 1;
                $GLOBALS['TSFE']->fe_user->user = $this->testGetUser();
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                $this->piVars = $GLOBALS['TSFE']->fe_user->user;
                $content = $warning . $this->endRegistration();
                $GLOBALS['TSFE']->loginUser = $previousLoginUser;
                $GLOBALS['TSFE']->fe_user->user = $previousLoggedUser;
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                return $content;
            case 'delete':
                $previousLoginUser = $GLOBALS['TSFE']->loginUser;
                $previousLoggedUser = ($GLOBALS['TSFE']->loginUser) ? $GLOBALS['TSFE']->fe_user->user : array();
                $GLOBALS['TSFE']->loginUser = 1;
                $GLOBALS['TSFE']->fe_user->user = $this->testGetUser();
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                $this->piVars = $GLOBALS['TSFE']->fe_user->user;
                $content = $warning . $this->showDeleteLink();
                $GLOBALS['TSFE']->loginUser = $previousLoginUser;
                $GLOBALS['TSFE']->fe_user->user = $previousLoggedUser;
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                return $content;
                break;
            case 'deleteSentEmail':
                $user = $this->testGetUser();
                return $warning . $this->emailDeletionSent($user);
                break;
            case 'deleteConfirmationEmail':
                $user = $this->testGetUser();
                $user['user_auth_code'] = md5('deleteAuth' . time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                $this->prepareAndSendEmailSubpart('deleteRequest', $user);
                ;
                return tx_t3registration_checkstatus::getMessage($this->pi_getll('testMailTitle'), sprintf($this->pi_getll('testConfirmationUserDeleteSent'), $user['email']), 'info');
                break;
            case 'userConfirmationDeletePage':
                $user = $this->testGetUser();
                return $warning . $this->confirmUserDeletionTemplate($user);
                break;

            case 'edit':
                $this->piVars = array();
                $previousLoginUser = $GLOBALS['TSFE']->loginUser;
                $previousLoggedUser = ($GLOBALS['TSFE']->loginUser) ? $GLOBALS['TSFE']->fe_user->user : array();
                $GLOBALS['TSFE']->loginUser = 1;
                $GLOBALS['TSFE']->fe_user->user = $this->testGetUser();
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                $user = $GLOBALS['TSFE']->fe_user->user;
                $content = $warning . $this->showProfile($user);
                $GLOBALS['TSFE']->loginUser = $previousLoginUser;
                $GLOBALS['TSFE']->fe_user->user = $previousLoggedUser;
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                return $content;
                break;
            case 'editWithErrors':
                $this->piVars = array();
                $previousLoginUser = $GLOBALS['TSFE']->loginUser;
                $previousLoggedUser = ($GLOBALS['TSFE']->loginUser) ? $GLOBALS['TSFE']->fe_user->user : array();
                $GLOBALS['TSFE']->loginUser = 1;
                $GLOBALS['TSFE']->fe_user->user = $this->testGetUser(true);
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                $this->piVars = $this->testGetUser(false);
                $this->piVars['submitted'] = 1;
                $content = $warning . $this->getForm();
                $GLOBALS['TSFE']->loginUser = $previousLoginUser;
                $GLOBALS['TSFE']->fe_user->user = $previousLoggedUser;
                $this->feLoggedInUser = $GLOBALS['TSFE']->fe_user->user;
                return $content;
                break;
        }
    }

    /**
     * This function fetches user from xml data
     * @param boolean $correct if true fetches correct user, otherwise wrong user
     * @return array user
     * @throws t3lib_exception
     */
    protected function testGetUser($correct = true) {
        if (!is_array($this->xmlTestUser)) {
            $xmlFile = (isset($this->conf['testXMLFile'])) ? $this->conf['testXMLFile'] : '';
            $content = $this->cObj->fileResource($xmlFile);
            $this->xmlTestUser = t3lib_div::xml2array($content);
        }
        if (!is_array($this->xmlTestUser) || !isset($this->xmlTestUser['correctUser']) || !isset($this->xmlTestUser['wrongUser'])) {
            throw new t3lib_exception('testXMLFileNotFoundOrWrong', self::XML_TEST_FILE_ERROR);
        }
        return ($correct) ? $this->xmlTestUser['correctUser'] : $this->xmlTestUser['wrongUser'];
    }

    /**
     * Function to implement getter method for user logged data
     * @return array user logged data
     */
    public function getLoggedUserData(){
        return $this->feLoggedInUser;
    }


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/pi1/class.tx_t3registration_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3registration/pi1/class.tx_t3registration_pi1.php']);
}

?>
