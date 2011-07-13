<?php

class Application_Form_RecoveryPassword extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        $this->addElement('password', 'password', array(
               'label' => 'Новый пароль:',
            'required' => true));

		$this->addElement('password', 'verifypassword', array(
               'label' => 'Ещё раз:',
            'required' => true));

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'SUBMIT'));
    }


}

