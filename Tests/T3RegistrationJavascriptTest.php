<?php

/**
 * @test
 * @author federico
 *
 */
class T3RegistrationJavascriptTest extends tx_phpunit_testcase {
    /**
     * @test
     */
    public function testLanguageTranslation() {
        //enable simulation fe
        $this->simulateFrontendEnviroment();

        //calculate path to site
        $url = substr($_SERVER['SCRIPT_NAME'],0,strpos('typo3',$_SERVER['SCRIPT_NAME'])+1);
        $list = file_get_contents('http://' . $_SERVER['SERVER_NAME'] . $url . 'index.php?eID=t3registration&operation=language&language=it');
        $this->assertRegExp('/var[\s]?translateObject[\{\}\:]?/',$list );
    }
}
?>