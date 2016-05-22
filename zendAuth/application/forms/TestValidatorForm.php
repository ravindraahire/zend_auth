<?php
class Application_Form_TestValidatorForm extends Zend_Form
{
    public function __construct($options = null) {
        //$this->addElementPrefixPath('CustomValidator_DuplicateRecord', 'CustomValidator/DuplicateRecord/');
        parent::__construct($options);
    }
    public function init()
    {
        $text1 = new Zend_Form_Element_Text('text1');
        $text1->setRequired(true);
        $text1->addValidators(array(
            array (
                'NotEmpty',
                true,
                array('messages' => 'should not be empty')
            ),
            /*array (
                'Db_NoRecordExists', 
                true, 
                array(
                    'table' => 'user',
                    'field' => 'uname',
                    'messages' => array( "recordFound" => "This record already exists in our DB") ,

                )
            )*/
            /*array (
                new Application_Form_DuplicateRecord(),
                true,
                array(
                    'table' => 'user',
                    'field' => 'uname',
                    'messages' => array( "recordFound" => "This record already exists in our DB") ,
                )
            )*/
        ));
        
        $submit1 = new Zend_Form_Element_Submit('submit1');
        
        $this->addElements(array($text1, $submit1));
    }
    
    public function isValid($data) {
        $select = Zend_Db_Table::getDefaultAdapter()->select()->from('user')->where('uname = ?',$data['text1']);echo $select;
        $validator =  new Zend_Validate_Db_NoRecordExists($select);
        $this->getElement('text1')->addValidator($validator);
        
        parent::isValid($data);
    }
}

