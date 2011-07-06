<?php

class Application_Form_ForgotPassword extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
 
        $this->addElement(
            'text', 'username', array(
                'label' => 'Username:',
             'required' => true,
           'validators' => array('EmailAddress'),
              'filters' => array('StripTags', 'StringTrim')));
            
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
             'label' => 'Отправить'));
    }

}

