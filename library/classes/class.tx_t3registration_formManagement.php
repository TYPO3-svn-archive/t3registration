<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 20102010 Federico Bernardin federico@bernardin.it
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
 * This class manages the operation with form
 *
 * @package TYPO3
 * @subpackage t3registration
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_t3registration_formManagement {

    /**
     * Contains the content object from the caller class
     * @var object
     */
    private $cObj;

    /**
     * Configuration array from TS
     * @var array
     */
    private $conf;

    /**
     * Contains errorList
     * @var array
     */
    private $errors = array();

    /**
     * Array with marker to substitute
     * @var array
     */
    private $markers = array();

    /**
     * reference to caller Object
     * @var object
     */
    private $pluginObject;

    /**
     * Template HTML content
     * @var string
     */
    private $template;

    public function __construct(&$cObj,$conf,$template,&$pluginObject){
        $this->cObj = $cObj;
        $this->conf = $conf;
        $this->template = $template;
        $this->pluginObject = $pluginObject;
    }

    public function getRegistrationForm(){
        $fieldContentArray = array();
        $this->evaluateFields();
        $template = $this->cObj->getSubpart($this->template,'T3R_TEMPLATE_REGISTRATION');
        foreach($this->conf['fields'] as $field){
            $fieldContentArray['SUBTEMPLATE_' . strtoupper($field)] = $this->getFieldContentFromTemplate($field,$template);
        }
        $result = $this->cObj->substituteMarkerArrayCached($template,$this->marker,$fieldContentArray);
        //debug($this->cObj->substituteMarkerArrayCached($template,$this->marker,$fieldContentArray));
        preg_match_all('/(SUBTEMPLATE_[\w]*)/',$result,$matches);
        debug($matches);
        for($i=0;$i<count($matches); $i++){
            $subparts[$matches[$i][0]] = '';
        }
        $result = $this->clearFromSubpart($this->cObj->substituteMarkerArrayCached($result,array(),$subparts));

        debug($result);
        debug($fieldContentArray);
        return $fieldContentArray;
    }

    protected function clearFromSubpart($template){
        preg_match_all('/(SUBTEMPLATE_[\w]*)/',$template,$matches);
        for($i=0;$i<count($matches); $i++){
            $subparts[$matches[$i][0]] = '';
        }
        return $this->cObj->substituteMarkerArrayCached($template,array(),$subparts);
    }

    protected function evaluateFields(){
        $error = false;
        foreach($this->conf['fields'] as $field){
            if(is_array($this->conf['fieldsConfiguration.'][$field . '.'])){
                $error = $this->evaluateSingleField($field);
            }
        }
        debug($this->errors,'errors');
        return $error;
    }

    protected function evaluateSingleField($field){
        $error = false;
        foreach($this->conf['fieldsConfiguration.'][$field . '.'] as $key => $value){
            switch($key){
                case 'alfanum':
                case 'alfa':
                    $this->errors[$field][] = 'errore in alfanum';
                    $error = true;
                    break;
                case 'email':
                    if(!tx_t3registration_evaluate::isEmail($this->conf['fieldsConfiguration.'][$field . '.'][$key . '.'],'pippo.pluto@example.com')){
                        $this->errors[$field][] = 'errore in email';
                        $error = true;
                    }
                    break;
                case 'regexp':
                    if(!tx_t3registration_evaluate::regexp($this->conf['fieldsConfiguration.'][$field . '.'][$key],'a#9')){
                        $this->errors[$field][] = 'errore in regexp';
                        $error = true;
                    }
                    break;
                case 'date':
                    if(!tx_t3registration_evaluate::checkDate($this->conf['fieldsConfiguration.'][$field . '.'][$key],'21-3-1973')){
                        $this->errors[$field][] = 'errore in date';
                        $error = true;
                    }

            }
        }
        return $error;
    }

    protected function getFieldContentFromTemplate($field,$template){
        $fieldUpper = strtoupper($field);
        $fieldContent = $this->cObj->getSubpart($template,'SUBTEMPLATE_' . $fieldUpper);
        $fieldContentError = $this->cObj->getSubpart($fieldContent,'EVALUATE_' . $fieldUpper);
        $fieldContentLabel = $this->pluginObject->pi_getLL($field . 'Label');
        if (is_array($this->errors) && is_array($this->errors[$field]) && count($this->errors[$field])>0){
            $errors = array();
            for($i = 0; $i < count($this->errors[$field]); $i++){
                $errors[] = $this->cObj->substituteMarker($fieldContentError,array('###EVALUATE_ERROR_' . $fieldUpper . '###'),$this->errors[$field][$i]);
            }
            $fieldContentErrorsResult = implode("\n", $errors);
        }
        else{
            $fieldContentErrorsResult = '';
        }
        $fieldContentResult = $this->cObj->substituteSubpart($fieldContent, 'EVALUATE_' . $fieldUpper, $fieldContentErrorsResult);
        $this->markers['###LABEL_' . $fieldUpper . '###'] = $fieldContentLabel;
        return $fieldContentResult;
    }


}

?>