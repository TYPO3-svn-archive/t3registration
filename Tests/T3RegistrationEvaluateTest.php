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


require_once(t3lib_extmgm::extPath('t3registration').'library/classes/class.tx_t3registration_evaluate.php');

/**
 *
 *
 * @package
 * @subpackage
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class T3RegistrationEvaluateTest extends tx_phpunit_testcase {

    protected function setUp() {
        /* $this->setHost('127.0.0.1');
         $this->setPort(4444);
         $this->setBrowser('*firefox');*/
        $this->testEmailOK = 'pippo.pluto@example.com';
        $this->testEmailKO[] = 'pippo.plutoexample.com';
        $this->testEmailKO[] = 'pippo@example';
        $this->testEmailBlackList = 'pippo@google.com';
        $this->conf =  array('blacklist' => 'google.com');
    }
    /**
     * @test
     * @return void
     */
    public function TestIsEmail(){
        $this->assertTrue(tx_t3registration_evaluate::isEmail($this->conf,$this->testEmailOK),'email correct test');
        for($i=0; $i< count($this->testEmailKO); $i++){
            $this->assertFalse(tx_t3registration_evaluate::isEmail($this->conf,$this->testEmailKO[$i]),'email not correct test number' . $i);
        }
        $this->assertFalse(tx_t3registration_evaluate::isEmail($this->conf,$this->testEmailBlackList),'email in blacklist');

    }


    /**
     * @test
     * @return void
     */
    public function TestIsInt(){
        $this->assertTrue(tx_t3registration_evaluate::isInt($this->conf,'1'),'1 is a number');
        $this->assertTrue(tx_t3registration_evaluate::isInt($this->conf,'6.4'),'6.4 is a number');
        $this->assertFalse(tx_t3registration_evaluate::isInt($this->conf,'abcdf'),'abcdf is not a number');
        $this->assertFalse(tx_t3registration_evaluate::isInt($this->conf,'6,87'),'6,87 comma is not a decimal divisor');

    }


    /**
     * @test
     * @return void
     */
    public function Testregexp(){
        $this->assertTrue(tx_t3registration_evaluate::regexp('/[\w0-9]{1,4}\.[12345]*/','yf6.42333'),'the regular expression is ok');
        $this->assertTrue(tx_t3registration_evaluate::regexp('/[\w0-9]{1,4}@#[123450]*##/','yf6@#423330##'),'the regular expression is ok');
        $this->assertFalse(tx_t3registration_evaluate::regexp('notregexp','notregexp'),'is not a regular expression');
        $this->assertFalse(tx_t3registration_evaluate::regexp('/[\w0-9]{1,4}@#[123450]*##/','gr5633@#'),'is not a regular expression');

    }


    /**
     * @test
     * @return void
     */
    public function TestcheckDate(){
        $this->assertGreaterThan(0,tx_t3registration_evaluate::checkDate('d-m-y','29-12-2010'),'the date is ok');
        $this->assertGreaterThan(0,tx_t3registration_evaluate::checkDate('m-d-y','02-29-2000'),'the date is ok');
        $this->assertFalse(tx_t3registration_evaluate::checkDate('d-y-m','29-2010-2'),'date is incorrect');
        $this->assertFalse(tx_t3registration_evaluate::checkDate('m/y/d','1-1-2010'),'date is incorrect');

    }

}