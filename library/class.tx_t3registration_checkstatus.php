<?php

class tx_t3registration_checkstatus{

    private $cObj;

    private $configurationArray;

    private $fieldsData;

    private $parentObject;

    public function initialize($parentObject,$fields){
        $this->fieldsData = $fields;
        $this->cObj = $parentObject->cObj;
        $this->configurationArray = $parentObject->conf;
        $this->parentObject = $parentObject;
        $GLOBALS['TSFE']->additionalHeaderData['t3registrationMessage'] = '<link href="' .t3lib_extMgm::siteRelPath('t3registration') . 'res/message.css" rel="stylesheet" type="text/css"/>';
    }

    public function main(){
        $this->getTemplate();
        $this->getSubpart('T3REGISTRATION_FORM');
        $this->getSubpart('T3REGISTRATION_PREVIEW');
        $this->getSubpart('T3REGISTRATION_DELETE');
        $this->getSubpart('T3REGISTRATION_DELETE_SENTEMAIL');
        $this->getSubpart('T3REGISTRATION_FORM');
        $this->getSubpart('T3REGISTRATION_DELETE_CONFIRMATION');
        $this->getSubpart('T3REGISTRATION_ENDREGISTRATION');
        $this->getSubpart('T3REGISTRATION_ENDUPDATEPROFILE');
        $this->getSubpart('T3REGISTRATION_CONFIRMATION_EMAIL_HTML');
        $this->getSubpart('T3REGISTRATION_DELETE_EMAIL_HTML');
        $this->getSubpart('T3REGISTRATION_CONFIRMEDUSER');
        $this->getSubpart('T3REGISTRATION_CONFIRMEDAUTHORIZEDUSER');
        $this->getSubpart('T3REGISTRATION_CONFIRMEDONREDIRECT');
        $this->getMarkerSubPart();
        $this->checkMail();
        $this->evaluationCheck();
        $this->getHTMLData();
        return implode('',$this->messages);
    }

    private function getMarkerSubPart(){
        $markers = array();
        $fields = array_keys($this->fieldsData);
        $subpart = $this->cObj->getSubpart($this->content,'T3REGISTRATION_FORM');
        if($subpart){
            preg_match_all('/<!--[\t]*###([A-Z_]*)_FIELD###/U',$subpart,$matches,PREG_PATTERN_ORDER);
            foreach($matches[1] as $key => $item){
                if(strpos($item,'ERROR')===false){
                    if(!in_array($item,$markers)){
                        $markers[] = $item;
                    }
                }
            }
            foreach($markers as $key => $item){
                $markers[$key] = strtolower($item);
            }
            $this->setMessage($this->parentObject->pi_getLL('fieldsTitle'),sprintf($this->parentObject->pi_getLL('fieldsBody'),implode('<br />',$fields)),'info');
            $this->setMessage($this->parentObject->pi_getLL('markersTitle'),sprintf($this->parentObject->pi_getLL('markersBody'),implode('<br />',$markers)),'info');
            $diffMarkers = array_diff($markers,array_keys($this->fieldsData));
            $diffMarkersText = (count($diffMarkers))?implode('<br />',$diffMarkers):$this->parentObject->pi_getLL('noDiff');
            $type = (count($diffMarkers))?'warning':'ok';
            $this->setMessage($this->parentObject->pi_getLL('markersDiffTitle'),sprintf($this->parentObject->pi_getLL('markersDiffBody'),$diffMarkersText),$type);
            $diffFields = array_diff(array_keys($this->fieldsData),$markers);
            $diffFieldsText = (count($diffFields))?implode('<br />',$diffFields):$this->parentObject->pi_getLL('noDiff');
            $type = (count($diffFields))?'warning':'ok';
            $this->setMessage($this->parentObject->pi_getLL('markersDiffTitle'),sprintf($this->parentObject->pi_getLL('markersDiffBody'),$diffFieldsText),$type);
        }
    }

    private function checkMail(){
        if(t3lib_div::inList($this->configurationArray['approvalProcess'],'adminApproval')){
            if($this->configurationArray['emailAdmin'] ){
                $adminEmailList = explode(',',$this->configurationArray['emailAdmin']);
                foreach($adminEmailList as $email){
                    $emailAdminTemp = explode(':',$email);
                    if(count($emailAdminTemp) == 2){
                        $emailAdmin[$emailAdminTemp[0]] = $emailAdminTemp[1];
                    }

                }
            }
            else{
                if($this->configurationArray['email.']['admin.']['email']){
                    $emailAdmin[] = $this->configurationArray['email.']['admin.']['email'];
                }
            }
        }
        if(is_array($emailAdmin)){
            $this->setMessage($this->parentObject->pi_getLL('emailAddressCheckTitle'),$this->parentObject->pi_getLL('emailAddressCheckBody'),'ok');
        }
        else{
            $this->setMessage($this->parentObject->pi_getLL('emailAddressCheckTitle'),$this->parentObject->pi_getLL('emailAddressCheckErrorBody'),'error');
        }
        if($this->configurationArray['emailFrom'] || $this->configurationArray['email.']['From.']['email']){
            $this->setMessage($this->parentObject->pi_getLL('emailFromPresentTitle'),$this->parentObject->pi_getLL('emailFromPresentBody'),'ok');
        }
        else{
            $this->setMessage($this->parentObject->pi_getLL('emailFromPresentTitle'),$this->parentObject->pi_getLL('emailFromPresentErrorBody'),'error');
        }
        if($this->configurationArray['emailFromName'] || $this->configurationArray['email.']['From.']['name']){
            $this->setMessage($this->parentObject->pi_getLL('emailFromNamePresentTitle'),$this->parentObject->pi_getLL('emailFromNamePresentBody'),'ok');
        }
        else{
            $this->setMessage($this->parentObject->pi_getLL('emailFromNamePresentTitle'),$this->parentObject->pi_getLL('emailFromNamePresentErrorBody'),'error');
        }
    }

    private function getTemplate(){
        $templateFile = $this->cObj->stdWrap($this->configurationArray['templateFile'],$this->configurationArray['templateFile.']);
        $this->content = $this->cObj->fileResource($templateFile);
        if($this->content){
            $this->setMessage($this->parentObject->pi_getLL('templateFoundTitle'),sprintf($this->parentObject->pi_getLL('templateFound'),$templateFile),'ok');
        }
        else{
            $this->setMessage($this->parentObject->pi_getLL('templateFoundTitle'),sprintf($this->parentObject->pi_getLL('templateFound'),$templateFile),'error');
        }
    }

    private function getSubpart($markers){
        $subpart = $this->cObj->getSubpart($this->content,$markers);
        if($subpart){
            $this->setMessage(sprintf($this->parentObject->pi_getLL('templateSubpartFoundTitle'),$markers),$this->parentObject->pi_getLL('templateSubpartFound'),'ok');
        }
        else{
            $this->setMessage(sprintf($this->parentObject->pi_getLL('templateSubpartFoundTitle'),$markers),$this->parentObject->pi_getLL('templateSubpartNotFound'),'error');
        }
    }

    private function evaluationCheck(){
        foreach($this->fieldsData as $key => $item){
            $text[] = sprintf($this->parentObject->pi_getLL('fieldsEvaluation'),$key,$item['config']['eval']);
        }
        $this->setMessage($this->parentObject->pi_getLL('fieldsEvaluationTitle'),sprintf($this->parentObject->pi_getLL('fieldsEvaluationBody'),implode('<br />',$text)),'info');
    }

    private function getHTMLData(){
        $text[] = sprintf($this->parentObject->pi_getLL('preUserGroup'),($this->configurationArray['preUsergroup'])?$this->configurationArray['preUsergroup']:$this->parentObject->pi_getLL('noGroup'));
        $text[] = sprintf($this->parentObject->pi_getLL('postUserGroup'),($this->configurationArray['postUsergroup'])?$this->configurationArray['postUsergroup']:$this->parentObject->pi_getLL('noGroup'));
        $text[] = sprintf($this->parentObject->pi_getLL('autoLoginAfterConfirmation'),$this->configurationArray['autoLoginAfterConfirmation']);
        $authMethod = explode(',',$this->configurationArray['approvalProcess']);
        foreach($authMethod as $item){
            $authMethodArray[] = $this->parentObject->pi_getLL($item);
        }
        $text[] = sprintf($this->parentObject->pi_getLL('authMethod'),implode(',',$authMethodArray));
        if($this->configurationArray['useAnotherTemplateInChangeProfileMode']){
            $text[] = $this->parentObject->pi_getLL('changeProfile');
        }
        $text[] = sprintf($this->parentObject->pi_getLL('siteUrl'),$this->configurationArray['siteUrl']);
        $text[] = sprintf($this->parentObject->pi_getLL('userFolder'),$this->configurationArray['userFolder']);
        $this->setMessage($this->parentObject->pi_getLL('generalConfigurationTitle'),sprintf($this->parentObject->pi_getLL('generalConfigurationBody'),implode('<br />',$text)),'info');
    }

    private function setMessage($title,$message,$status){
        switch($status){
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

    //

}
?>