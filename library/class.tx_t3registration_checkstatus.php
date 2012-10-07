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
 *   50: class tx_t3registration_checkstatus
 *   77:     public function initialize($parentObject, $fields)
 *   90:     public function main()
 *  131:     private function getMarkerSubPart()
 *  165:     private function checkMail()
 *  210:     private function getTemplate()
 *  228:     private function getSubpart($markers, $errorClass = 'error')
 *  243:     private function evaluationCheck()
 *  289:     private function getHTMLData()
 *  313:     private function setMessage($title, $message, $status)
 *  343:     public static function getMessage($title, $message, $status)
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * This class checks all configuration of T3Registration
 *
 */
class tx_t3registration_checkstatus {

    /**
     * @var object tslib_content class
     */
    private $cObj;

    /**
     * @var array conf array of pibase class
     */
    private $configurationArray;

    /**
     * @var array list of fields
     */
    private $fieldsData;

    /**
     * @var object reference to tslib parent object
     */
    private $parentObject;

    /**
 * @param	$parentObject		object reference to tslib parent object
 * @param	$fields		array fields list
 * @return	[type]		...
 */
    public function initialize($parentObject, $fields) {
        $this->fieldsData = $fields;
        $this->cObj = $parentObject->cObj;
        $this->configurationArray = $parentObject->conf;
        $this->parentObject = $parentObject;
        $GLOBALS['TSFE']->additionalHeaderData['t3registrationMessage'] = '<link href="' . t3lib_extMgm::siteRelPath('t3registration') . 'res/message.css" rel="stylesheet" type="text/css"/>';
    }

    /**
 * Function called by reference
 *
 * @return	string		content to show
 */
    public function main() {
        $emailFormat = $this->parentObject->getMailFormat();
        $this->getTemplate();
        $this->getSubpart('T3REGISTRATION_FORM');
        $this->getSubpart('T3REGISTRATION_PREVIEW');
        $this->getSubpart('T3REGISTRATION_DELETE');
        $this->getSubpart('T3REGISTRATION_DELETE_SENTEMAIL');
        $this->getSubpart('T3REGISTRATION_FORM');
        $this->getSubpart('T3REGISTRATION_DELETE_CONFIRMATION');
        $this->getSubpart('T3REGISTRATION_ENDREGISTRATION');
        $this->getSubpart('T3REGISTRATION_ENDUPDATEPROFILE');
        $this->getSubpart('T3REGISTRATION_CONFIRMEDUSER');
        $this->getSubpart('T3REGISTRATION_CONFIRMEDAUTHORIZEDUSER');
        $this->getSubpart('T3REGISTRATION_CONFIRMEDONREDIRECT');
        if ($emailFormat & HTML) {
            $this->getSubpart('T3REGISTRATION_CONFIRMATION_EMAIL_HTML');
            $this->getSubpart('T3REGISTRATION_DELETE_EMAIL_HTML');
            $this->getSubpart('T3REGISTRATION_CONFIRMEDONADMINAPPROVAL_EMAIL_HTML');
            $this->getSubpart('T3REGISTRATION_AUTHORIZATION_EMAIL_HTML');
        }
        if ($emailFormat & TEXT) {
            $this->getSubpart('T3REGISTRATION_CONFIRMATION_EMAIL_TEXT');
            $this->getSubpart('T3REGISTRATION_DELETE_EMAIL_TEXT');
            $this->getSubpart('T3REGISTRATION_AUTHORIZATION_EMAIL_TEXT');
            $this->getSubpart('T3REGISTRATION_CONFIRMEDONADMINAPPROVAL_EMAIL_TEXT');
        }
        $this->getSubpart('T3REGISTRATION_SENDCONFIRMATIONEMAIL_FORM', 'warning');
        $this->getSubpart('T3REGISTRATION_SENDCONFIRMATIONEMAIL_TEXT', 'warning');

        $this->getMarkerSubPart();
        $this->checkMail();
        $this->evaluationCheck();
        $this->getHTMLData();
        return implode('', $this->messages);
    }

    /**
 * Fetches subpart from template
 *
 * @return	[type]		...
 */
    private function getMarkerSubPart() {
        $markers = array();
        $fields = array_keys($this->fieldsData);
        $subpart = $this->cObj->getSubpart($this->content, 'T3REGISTRATION_FORM');
        if ($subpart) {
            preg_match_all('/<!--[\t]*###([0-9A-Z_]*)_FIELD###/U', $subpart, $matches, PREG_PATTERN_ORDER);
            foreach ($matches[1] as $item) {
                if (strpos($item, 'ERROR') === false) {
                    if (!in_array($item, $markers)) {
                        $markers[] = $item;
                    }
                }
            }
            foreach ($markers as $key => $item) {
                $markers[$key] = strtolower($item);
            }
            $this->setMessage($this->parentObject->pi_getLL('fieldsTitle'), sprintf($this->parentObject->pi_getLL('fieldsBody'), implode('<br />', $fields)), 'info');
            $this->setMessage($this->parentObject->pi_getLL('markersTitle'), sprintf($this->parentObject->pi_getLL('markersBody'), implode('<br />', $markers)), 'info');
            $diffMarkers = array_diff($markers, array_keys($this->fieldsData));
            $diffMarkersText = (count($diffMarkers)) ? implode('<br />', $diffMarkers) : $this->parentObject->pi_getLL('noDiff');
            $type = (count($diffMarkers)) ? 'warning' : 'ok';
            $this->setMessage($this->parentObject->pi_getLL('markersDiffTitle'), sprintf($this->parentObject->pi_getLL('markersDiffBody'), $diffMarkersText), $type);
            $diffFields = array_diff(array_keys($this->fieldsData), $markers);
            $diffFieldsText = (count($diffFields)) ? implode('<br />', $diffFields) : $this->parentObject->pi_getLL('noDiff');
            $type = (count($diffFields)) ? 'warning' : 'ok';
            $this->setMessage($this->parentObject->pi_getLL('fieldsDiffTitle'), sprintf($this->parentObject->pi_getLL('fieldsDiffBody'), $diffFieldsText), $type);
        }
    }

    /**
 * Checks email features
 *
 * @return	[type]		...
 */
    private function checkMail() {
        if (t3lib_div::inList($this->configurationArray['approvalProcess'], 'adminApproval')) {
            if ($this->configurationArray['emailAdmin']) {
                if (strstr($this->configurationArray['emailAdmin'], ',') !== false) {
                    $adminEmailList = explode(',', $this->configurationArray['emailAdmin']);
                    foreach ($adminEmailList as $email) {
                        $emailAdminTemp = explode(':', $email);
                        if (count($emailAdminTemp) == 2) {
                            $emailAdmin[$emailAdminTemp[0]] = $emailAdminTemp[1];
                        }

                    }
                }
                else {
                    $emailAdmin[0] = $this->configurationArray['emailAdmin'];
                }
            }
            if (is_array($emailAdmin)) {
                $this->setMessage($this->parentObject->pi_getLL('emailAddressCheckTitle'), $this->parentObject->pi_getLL('emailAddressCheckBody'), 'ok');
            }
            else {
                $this->setMessage($this->parentObject->pi_getLL('emailAddressCheckTitle'), $this->parentObject->pi_getLL('emailAddressCheckErrorBody'), 'error');
            }
        }
        if (t3lib_div::inList($this->configurationArray['approvalProcess'], 'doubleOptin')) {
            if ($this->configurationArray['emailFrom'] || $this->configurationArray['email.']['From.']['email']) {
                $this->setMessage($this->parentObject->pi_getLL('emailFromPresentTitle'), $this->parentObject->pi_getLL('emailFromPresentBody'), 'ok');
            }
            else {
                $this->setMessage($this->parentObject->pi_getLL('emailFromPresentTitle'), $this->parentObject->pi_getLL('emailFromPresentErrorBody'), 'error');
            }
            if ($this->configurationArray['emailFromName'] || $this->configurationArray['email.']['From.']['name']) {
                $this->setMessage($this->parentObject->pi_getLL('emailFromNamePresentTitle'), $this->parentObject->pi_getLL('emailFromNamePresentBody'), 'ok');
            }
            else {
                $this->setMessage($this->parentObject->pi_getLL('emailFromNamePresentTitle'), $this->parentObject->pi_getLL('emailFromNamePresentErrorBody'), 'error');
            }
        }
    }

    /**
 * Retrieves template
 *
 * @return	[type]		...
 */
    private function getTemplate() {
        $templateFile = $this->cObj->stdWrap($this->configurationArray['templateFile'], $this->configurationArray['templateFile.']);
        $this->content = $this->cObj->fileResource($templateFile);
        if ($this->content) {
            $this->setMessage($this->parentObject->pi_getLL('templateFoundTitle'), sprintf($this->parentObject->pi_getLL('templateFound'), $templateFile), 'ok');
        }
        else {
            $this->setMessage($this->parentObject->pi_getLL('templateFoundTitle'), sprintf($this->parentObject->pi_getLL('templateNotFound'), $templateFile), 'error');
        }
    }

    /**
 * Fetches subparts inside template
 *
 * @param	$markers		string subpart marker
 * @param	string		$errorClass string class name to use
 * @return	[type]		...
 */
    private function getSubpart($markers, $errorClass = 'error') {
        $subpart = $this->cObj->getSubpart($this->content, $markers);
        if ($subpart) {
            $this->setMessage(sprintf($this->parentObject->pi_getLL('templateSubpartFoundTitle'), $markers), $this->parentObject->pi_getLL('templateSubpartFound'), 'ok');
        }
        else {
            $this->setMessage(sprintf($this->parentObject->pi_getLL('templateSubpartFoundTitle'), $markers), $this->parentObject->pi_getLL('templateSubpartNotFound'), $errorClass);
        }
    }

    /**
 * Checks the evaluations of the fields
 *
 * @return	[type]		...
 */
    private function evaluationCheck() {
        foreach ($this->fieldsData as $key => $item) {
            if (isset($item['regexp']) && strlen($item['regexp']) > 0) {
                if (strlen($item['config']['eval']) > 0) {
                    $evalArray = explode(',', $item['config']['eval']);
                }
                else {
                    $evalArray = array();
                }
                $evalArray[] = 'regexp';
                $item['config']['eval'] = implode(',', $evalArray);
            }
            if (isset($item['config']['internal_type']) && $item['config']['internal_type'] === 'file') {
                if (strlen($item['config']['eval']) > 0) {
                    $evalArray = explode(',', $item['config']['eval']);
                }
                else {
                    $evalArray = array();
                }
                $evalArray[] = 'file';
                $item['config']['eval'] = implode(',', $evalArray);
            }
            $evaluation = array();
            if (isset($item['config']['eval'])) {
                $evaluation = explode(',', $item['config']['eval']);
            }
            //evaluation from flexform
            if (isset($item['evaluation']) && strlen(trim($item['evaluation']))) {
                $additionalEvaluationArray = array_diff(explode(',', $item['evaluation']), $evaluation);
                $evaluation = array_merge($evaluation, $additionalEvaluationArray);
            }
            //evaluation from typoscript add function
            if (isset($item['config']['additionalEval'])) {
                $additionalEvaluationArray = array_diff(explode(',', $item['config']['additionalEval']), $evaluation);
                $evaluation = array_merge($evaluation, $additionalEvaluationArray);
            }
            $text[] = sprintf($this->parentObject->pi_getLL('fieldsEvaluation'), $key, implode(',', $evaluation));
        }
        $this->setMessage($this->parentObject->pi_getLL('fieldsEvaluationTitle'), sprintf($this->parentObject->pi_getLL('fieldsEvaluationBody'), implode('<br />', $text)), 'info');
    }

    /**
 * Tests features of plugin interface
 *
 * @return	[type]		...
 */
    private function getHTMLData() {
        $text[] = sprintf($this->parentObject->pi_getLL('preUserGroup'), ($this->configurationArray['preUsergroup']) ? $this->configurationArray['preUsergroup'] : $this->parentObject->pi_getLL('noGroup'));
        $text[] = sprintf($this->parentObject->pi_getLL('postUserGroup'), ($this->configurationArray['postUsergroup']) ? $this->configurationArray['postUsergroup'] : $this->parentObject->pi_getLL('noGroup'));
        $text[] = sprintf($this->parentObject->pi_getLL('autoLoginAfterConfirmation'), $this->configurationArray['autoLoginAfterConfirmation']);
        $authMethod = explode(',', $this->configurationArray['approvalProcess']);
        foreach ($authMethod as $item) {
            $authMethodArray[] = $this->parentObject->pi_getLL($item);
        }
        $text[] = sprintf($this->parentObject->pi_getLL('authMethod'), implode(',', $authMethodArray));
        if ($this->configurationArray['useAnotherTemplateInChangeProfileMode']) {
            $text[] = $this->parentObject->pi_getLL('changeProfile');
        }
        $text[] = sprintf($this->parentObject->pi_getLL('userFolder'), $this->configurationArray['userFolder']);
        $this->setMessage($this->parentObject->pi_getLL('generalConfigurationTitle'), sprintf($this->parentObject->pi_getLL('generalConfigurationBody'), implode('<br />', $text)), 'info');
    }

    /**
 * Function to create the eroor/notice/warning box
 *
 * @param	$title		string title of box
 * @param	$message		string test to show inside of box
 * @param	$status		string possible values are: info, warning, error, notice
 * @return	[type]		...
 */
    private function setMessage($title, $message, $status) {
        switch ($status) {
            case 'info':
                $class = 'message-information';
                break;
            case 'warning':
                $class = 'message-warning';
                break;
            case 'error':
                $class = 'message-error';
                break;
            case 'ok':
                $class = 'message-ok';
                break;
            case 'notice':
                $class = 'message-notice';
                break;
        }
        $this->messages[] = '<div class="typo3-message ' . $class . '"><div class="message-header">' . $title . '</div><div class="message-body">' . $message . '</div></div>';
    }

    /**
 * Function to create the HTML code to show on page (it could be called without initialize class).
 *
 * @param	$title		string title of box
 * @param	$message		string test to show inside of box
 * @param	$status		string possible values are: info, warning, error, notice
 * @return	string		HTML code
 * @static
 */
    public static function getMessage($title, $message, $status) {
        if(!isset($GLOBALS['TSFE']->additionalHeaderData['t3registrationMessage'])){
            $GLOBALS['TSFE']->additionalHeaderData['t3registrationMessage'] = '<link href="' . t3lib_extMgm::siteRelPath('t3registration') . 'res/message.css" rel="stylesheet" type="text/css"/>';
        }
        switch ($status) {
            case 'info':
                $class = 'message-information';
                break;
            case 'warning':
                $class = 'message-warning';
                break;
            case 'error':
                $class = 'message-error';
                break;
            case 'ok':
                $class = 'message-ok';
                break;
            case 'notice':
                $class = 'message-notice';
                break;
        }
        return '<div class="typo3-message ' . $class . '"><div class="message-header">' . $title . '</div><div class="message-body">' . $message . '</div></div>';
    }

}

?>