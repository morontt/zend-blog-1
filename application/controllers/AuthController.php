<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
    
    }

    public function indexAction()
    {
        $this->_redirect('auth/login');
    }

    public function loginAction()
    {
		$users = new Application_Model_DbTable_Users;
        $form = new Application_Form_LoginForm;
		
        $this->view->form = $form;
		
        if ($this->getRequest()->isPost())
		{
            if ($form->isValid($_POST))
			{
                $data = $form->getValues();
                $result = $users->loginUser($data);
                if ($result)
				{
                    $this->_redirect('/');
                } else
				{
                    $this->view->errorMessage = 'Неверный логин или пароль';
                }
            }
        }
		
    }

    public function signinAction()
    {
		$users = new Application_Model_DbTable_Users;
        $form = new Application_Form_RegistrationForm;
		
        $this->view->form = $form;
		
		if ($this->getRequest()->isPost())
		{
            if ($form->isValid($_POST))
			{
			    $data = $form->getValues();
				
				if ($data['password'] != $data['verifypassword'])
				{
                    $this->view->errorMessage = 'Введённые пароли не совпадают';
                    return;
                }
				
				$users->signinNewUser($data);
				
				$this->_redirect('auth/login');
			}
		}
    }

    public function logoutAction()
    {
        //$storage = new Zend_Auth_Storage_Session();
        //$storage->clear();
		
		Zend_Auth::getInstance()->clearIdentity();
		
        $this->_redirect('auth/login');
    }

    public function forgotAction()
    {
        $users = new Application_Model_DbTable_Users;
        $form = new Application_Form_ForgotPassword;
		
        $this->view->form = $form;
		
		if ($this->getRequest()->isPost())
		{
            if ($form->isValid($_POST))
			{
			    $data = $form->getValues();
                //$pass = $users->generateNewPassword($data['username']);
                $pass = TRUE;
				
				if ($pass) :
                    $this->view->message = "Новый пароль выслан на указанный email</br>
                    Функция пока бутафорская, приносим свои извинения";
				else :
                    $this->view->message = 'Указанный email в базе данных отсутствует';
                endif;
			}
		}
        
        //$mail = new Zend_Mail();
        //$mail->setBodyText('This is the text of the mail.');
        //$mail->setFrom('support@zadachnik.info', 'Forgot');
        //$mail->addTo('morontt@list.ru', 'Some Recipient');
        //$mail->setSubject('TestSubject');
        //$mail->send();
    }

    public function recoveryPasswordAction()
    {
        // action body
    }


}











