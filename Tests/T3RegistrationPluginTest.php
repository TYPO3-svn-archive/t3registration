<?php
/***************************************************************
*  Copyright notice
*
*  (c) 20102010
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

//require_once(PATH_t3lib.'class.tslib_content.php');
require_once(PATH_tslib.'class.tslib_content.php');
require_once(t3lib_extMgm::extPath('t3registration').'/pi1/class.tx_t3registration_pi1.php');

/**
 *
 *
 * @package
 * @subpackage
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class T3RegistrationPluginTest extends tx_phpunit_testcase {

    /**
     * @test
     * @return void
     */
    public function testPluginPiFlexForm(){
        $this->simulateFrontendEnviroment();
        $cObj = new tslib_cObj();
        //$cObj = $this->getMock('tslib_cObj');
        //$T3RegistrationPlugin = $this->getMock('tx_t3registration_pi1');
        $T3RegistrationPlugin = new tx_t3registration_pi1();

        $T3RegistrationPlugin->cObj = $cObj;
        $T3RegistrationPlugin->cObj->data['pi_flexform'] = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="displayMode">
                    <value index="vDEF">default</value>
                </field>
                <field index="pages">
                    <value index="vDEF">5</value>
                </field>
                <field index="recursive">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="s_template">
            <language index="lDEF">
                <field index="templateFile">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="s_category">
            <language index="lDEF">
                <field index="catImageMode">
                    <value index="vDEF"></value>
                </field>
                <field index="catImageMaxWidth">
                    <value index="vDEF"></value>
                </field>
                <field index="catImageMaxHeight">
                    <value index="vDEF"></value>
                </field>
                <field index="maxCatImages">
                    <value index="vDEF"></value>
                </field>
                <field index="catTextMode">
                    <value index="vDEF"></value>
                </field>
                <field index="maxCatTexts">
                    <value index="vDEF"></value>
                </field>
                <field index="fieldsManager">
                    <value index="vDEF">{&quot;test1&quot;:[&quot;required&quot;,&quot;int&quot;,&quot;alfa&quot;,&quot;regexp;regular&quot;,&quot;hook;eunhook&quot;],&quot;test2&quot;:[&quot;alfanum&quot;,&quot;saveinflex;campo1&quot;,&quot;minimum;12&quot;]}</value>
                </field>
            </language>
        </sheet>
        <sheet index="s_misc">
            <language index="lDEF">
                <field index="PIDitemDisplay">
                    <value index="vDEF"></value>
                </field>
                <field index="backPid">
                    <value index="vDEF"></value>
                </field>
                <field index="firstImageIsPreview">
                    <value index="vDEF">0</value>
                </field>
                <field index="forceFirstImageIsPreview">
                    <value index="vDEF">0</value>
                </field>
                <field index="listStartId">
                    <value index="vDEF"></value>
                </field>
                <field index="listLimit">
                    <value index="vDEF"></value>
                </field>
                <field index="noPageBrowser">
                    <value index="vDEF">0</value>
                </field>
                <field index="maxWordsInSingleView">
                    <value index="vDEF"></value>
                </field>
                <field index="confirmationPage">
                    <value index="vDEF"></value>
                </field>
                <field index="groupAssociationMode">
                    <value index="vDEF"></value>
                </field>
                <field index="emailFormat">
                    <value index="vDEF">1</value>
                </field>
                <field index="confirmationProcessMode">
                    <value index="vDEF">2</value>
                </field>
                <field index="senderEmail">
                    <value index="vDEF">fede@immaginario.com</value>
                </field>
                <field index="senderName">
                    <value index="vDEF">federico</value>
                </field>
                <field index="adminEmail">
                    <value index="vDEF">federico@bernardin.it</value>
                </field>
                <field index="groupOnConfirmation">
                    <value index="vDEF">2,</value>
                </field>
                <field index="groupOnRegistration">
                    <value index="vDEF">1,</value>
                </field>
            </language>
        </sheet>
        <sheet index="s_fields">
            <language index="lDEF">
                <field index="fieldsManager">
                    <value index="vDEF">{&quot;test2&quot;:[&quot;alfanum&quot;,&quot;date;fdgdfg&quot;,&quot;saveinflex;fdgdfs&quot;,&quot;maximum;fdgfd&quot;],&quot;ewqewrqw&quot;:[&quot;required&quot;,&quot;int&quot;,&quot;decimal&quot;,&quot;minimum;erewrweq&quot;]}</value>
                </field>
                <field index="useEmailAsUsername">
                    <value index="vDEF">0</value>
                </field>
            </language>
        </sheet>
        <sheet index="s_configuration">
            <language index="lDEF">
                <field index="confirmationPage">
                    <value index="vDEF"></value>
                </field>
                <field index="groupAssociationMode">
                    <value index="vDEF"></value>
                </field>
                <field index="emailFormat">
                    <value index="vDEF">1</value>
                </field>
                <field index="confirmationProcessMode">
                    <value index="vDEF">0</value>
                </field>
                <field index="senderEmail">
                    <value index="vDEF"></value>
                </field>
                <field index="senderName">
                    <value index="vDEF"></value>
                </field>
                <field index="adminEmail">
                    <value index="vDEF"></value>
                </field>
                <field index="groupOnConfirmation">
                    <value index="vDEF"></value>
                </field>
                <field index="groupOnRegistration">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>';
        $this->assertTrue(is_object($T3RegistrationPlugin),var_dump($T3RegistrationPlugin,true));
        //$observer->expects($this->once())->method('update');
        $T3RegistrationPlugin->main('',array());
        //$T3RegistrationPlugin->cObj->data['pi_flexform'] = t3lib_div::xml2array($T3RegistrationPlugin->cObj->data['pi_flexform']);
        //$this->assertTrue(is_array($T3RegistrationPlugin->cObj->data['pi_flexform']),var_dump($T3RegistrationPlugin->cObj->data['pi_flexform']['data']['s_fields']['lDEF'],true));
        //$this->assertTrue(intval($T3RegistrationPlugin->cObj->data['pi_flexform']),var_dump($T3RegistrationPlugin->pi_getFFvalueFromSheetArray($T3RegistrationPlugin->cObj->data['pi_flexform']['data']['s_fields']['lDEF'],array('fieldsManager'),'vDEF'),true));
        $arrayToReturn = $T3RegistrationPlugin->mergePiFlexFormValue();

        $this->assertEquals($arrayToReturn['emailFormat'],'1','check on EmailFormat into flexform' . var_export($T3RegistrationPlugin->pi_getFFvalue($T3RegistrationPlugin->cObj->data['pi_flexform'],'fieldsManager','s_fields'),true));

    }

}