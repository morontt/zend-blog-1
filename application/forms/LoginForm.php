<?php

class Application_Form_LoginForm extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
 
        $this->addElement(
            'text', 'username', array(
                'label' => 'E-mail:',
             'required' => true,
           'validators' => array('EmailAddress'),
              'filters' => array('StripTags', 'StringTrim')));
 
        $this->addElement('password', 'password', array(
            'label' => 'Пароль:',
         'required' => true));
 
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
             'label' => 'LOG IN'));
    }

}
