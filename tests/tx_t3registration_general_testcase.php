<?php

require_once (t3lib_extMgm::extPath('t3registration').'pi1/class.tx_t3registration_pi1.php');

class tx_t3registration_pi1_extension extends tx_t3registration_pi1{

    public function evaluateField($value,$evaluationRule,$field = array()){
        return parent::evaluateField($value,$evaluationRule,$field);
    }
}

class tx_t3registration_general_testcase extends tx_phpunit_database_testcase{
    private $fields = array(
    'username' => array(
            'label' => 'LLL:EXT:cms/locallang_tca.xml:fe_users.username',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'max' => '50',
                'eval' => 'nospace,lower,uniqueInPid,required'
                )
                ),
        'password' => array(
            'label' => 'LLL:EXT:cms/locallang_tca.xml:fe_users.password',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'max' => '40',
                'eval' => 'nospace,required,password'
                )
                ),
        'usergroup' => array(
            'label' => 'LLL:EXT:cms/locallang_tca.xml:fe_users.usergroup',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'size' => '6',
                'minitems' => '1',
                'maxitems' => '50'
                )
                ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.name',
            'config' => array(
                'type' => 'input',
                'size' => '40',
                'eval' => 'trim',
                'max' => '80'
                )
                ),
        'first_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.first_name',
            'config' => array(
                'type' => 'input',
                'size' => '25',
                'eval' => 'trim',
                'max' => '50'
                )
                ),
        'middle_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.middle_name',
            'config' => array(
                'type' => 'input',
                'size' => '25',
                'eval' => 'trim',
                'max' => '50'
                )
                ),
        'last_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.last_name',
            'config' => array(
                'type' => 'input',
                'size' => '25',
                'eval' => 'trim',
                'max' => '50'
                )
                ),
        'address' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.address',
            'config' => array(
                'type' => 'text',
                'cols' => '20',
                'rows' => '3'
                )
                ),
        'telephone' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.phone',
            'config' => array(
                'type' => 'input',
                'eval' => 'trim',
                'size' => '20',
                'max' => '20'
                )
                ),
        'fax' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fax',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
                'max' => '20'
                )
                ),
        'email' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.email',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim,email',
                'max' => '80'
                )
                ),
        'title' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.title_person',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
                'max' => '40'
                )
                ),
        'zip' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.zip',
            'config' => array(
                'type' => 'input',
                'eval' => 'trim',
                'size' => '10',
                'max' => '10'
                )
                ),
        'city' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.city',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
                'max' => '50'
                )
                ),
        'country' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.country',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
                'max' => '40'
                )
                ),
        'www' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.www',
            'config' => array(
                'type' => 'input',
                'eval' => 'trim',
                'size' => '20',
                'max' => '80'
                )
                ),
        'company' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.company',
            'config' => array(
                'type' => 'input',
                'eval' => 'trim',
                'size' => '20',
                'max' => '80'
                )
                ),
        'image' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.image',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'jpg,jpeg,png',
                'max_size' => 10000,
                'uploadfolder' => 'uploads/pics',
                'show_thumbs' => '1',
                'size' => '3',
                'maxitems' => '6',
                'minitems' => '0'
                )
           ),
       );

       protected $testedClass;

       public function __construct ($name=null) {
           parent::__construct ($name);
           $this->testedClass = new tx_t3registration_pi1_extension(array());
       }

       public function test_checkEvaluation() {
            //test email
            $value = $this->testedClass->evaluateField('isnotanemail','email');
            self::assertFalse ($value, 'Check control email: test have to be false');
            $value = $this->testedClass->evaluateField('isanemail@t3registration.it','email');
            self::assertTrue ($value, 'Check control email: test have to be true');

            //test int
            $value = $this->testedClass->evaluateField('notint','int');
            self::assertFalse ($value, 'Check control int: test have to be false');
            $value = $this->testedClass->evaluateField('3456','int');
            self::assertTrue ($value, 'Check control int: test have to be true');

            //test string/alpha
            $value = $this->testedClass->evaluateField('jkhjkh444$$$$$','string');
            self::assertEquals(0,$value, 'Check control string: test have to be false');
            $value = $this->testedClass->evaluateField('thisisanalfanumericstring','string');
            self::assertEquals(1,$value, 'Check control string: test have to be true');

            //test regexp
            $field['regexp'] = 'ambra[\d]{4}test';
            $value = $this->testedClass->evaluateField('jdhfyyyyy','regexp',$field);
            self::assertEquals(0,$value, 'Check control regexp: test have to be false');
            $value = $this->testedClass->evaluateField('ambra3456test','regexp',$field);
            self::assertEquals(1,$value, 'Check control regexp: test have to be true');

            //test password length
            $field['config'] = array('maxchars' => 10, 'minchars' => 3);
            $value = $this->testedClass->evaluateField('gg','password',$field);
            self::assertFalse($value, 'Check control password: test have to be false');
            $value = $this->testedClass->evaluateField('gggggggggggggg','password',$field);
            self::assertFalse($value, 'Check control password: test have to be false');
            $value = $this->testedClass->evaluateField('xxxxxxx','password',$field);
            self::assertTrue($value, 'Check control password: test have to be true');

            //test required field
            $field['name'] = 'testVar';
            $this->testedClass->piVars['testVar'] = '';
            $value = $this->testedClass->evaluateField('','required',$field);
            self::assertFalse($value, 'Check control required: test have to be false');
            $this->testedClass->piVars['testVar'] = 'notfalse';
            $value = $this->testedClass->evaluateField('','required',$field);
            self::assertTrue($value, 'Check control required: test have to be true');
       }


}
?>