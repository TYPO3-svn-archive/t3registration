<?php

/**
 * @test
 * @author federico
 *
 */
class T3RegistrationTest extends tx_phpunit_testcase {
    /**
     * @test
     */
    public function newArrayIsEmpty() {
        // Create the Array fixture.
        $fixture = array();

        // Assert that the size of the Array fixture is 0.
        $this->assertEquals(0, sizeof($fixture));
    }
}
?>