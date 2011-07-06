<?php

class Application_Form_RegistrationForm extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
 
        $this->addElement(
            'text', 'username', array(
                     'label' => 'E-mail:',
                'required'   => true,
				'validators' => array('EmailAddress'),
                'filters'    => array('StripTags', 'StringTrim')));
 
        $this->addElement('password', 'password', array(
               'label' => 'Пароль:',
            'required' => true));
			
		$this->addElement('password', 'verifypassword', array(
               'label' => 'Ещё раз пароль:',
            'required' => true));
			
		$this->addElement(
            'text', 'nickname', array(
                   'label' => 'Имя на сайте:',
                'required' => true,
                 'filters' => array('StripTags', 'StringTrim')));
                
        $captcha = new Zend_Form_Element_Captcha('captcha',array(
            'label' => 'Подтвердите, что вы не робот:',
            'captcha' => array(
                'captcha' => 'Figlet',
                'wordLen' => 4,
                'timeout' => 300)
            ));
        
        $this->addElement($captcha);
 
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'CREATE AN ACCOUNT'));  
    }

}
