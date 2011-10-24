<?php

class AuthController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    public function init()
    {
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->view->headMeta()->appendName('robots', 'noindex, follow');
    }

    public function indexAction()
    {
        $this->_redirect($this->view->url(array(), 'login'));
    }

    public function loginAction()
    {
		$users = new Application_Model_DbTable_Users;
        $form = new Application_Form_LoginForm;
		
        $this->view->form = $form;
        
        $this->view->messages = $this->_flashMessenger->getMessages();
		
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                $result = $users->loginUser($data);
                if ($result) {
                    $this->_redirect('/');
                } else {
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
		
		if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
			    $data = $form->getValues();
				
				if ($data['password'] != $data['verifypassword']) {
                    $this->view->errorMessage = 'Введённые пароли не совпадают';
                    return;
                }
				
				$users->signinNewUser($data);
                $this->_flashMessenger->addMessage('Вы успешно зарегистрировались.<br />
                                    Используйте свой логин и пароль для входа в систему');

                $mail = new Application_Model_MailClass();
                $mail->RegistrationMail($data['username'], $data['password']);
				
				$this->_redirect($this->view->url(array(), 'login'));
			}
		}
    }

    public function logoutAction()
    {
        //$storage = new Zend_Auth_Storage_Session();
        //$storage->clear();
		
		Zend_Auth::getInstance()->clearIdentity();
		
        $this->_redirect($this->view->url(array(), 'login'));
    }

    public function forgotAction()
    {
        $users = new Application_Model_DbTable_Users;
        $form = new Application_Form_ForgotPassword;
		
        $this->view->form = $form;

		if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
			    $data = $form->getValues();
                $hash = $users->getHashByLogin($data['username']);

                if ($hash) {
                    $this->view->message = 'Новый пароль выслан на указанный email';
                    $request = Zend_Controller_Front::getInstance()->getRequest();
                    $url = $request->getScheme() . '://'
                         . $request->getHttpHost()
                         . $this->view->url(array('id' => $hash['id'],
                                                  'hash' => $hash['hash']), 'recovery');

                    $mail = new Application_Model_MailClass();
                    $mail->forgotPasswordMail($data['username'], $url);
                    
				} else {
                    $this->view->message = 'Указанный email в базе данных отсутствует';
                }
                
			}
		}

    }

    public function recoveryAction()
    {
        $id = $this->_getParam('id');
        $hash = $this->_getParam('hash');
        $users = new Application_Model_DbTable_Users;
        $form = new Application_Form_RecoveryPassword;
        
        $rightHash = $users->getHashById($id);
        if ($hash != $rightHash) {
            $this->view->errorMessage = 'Неверная ссылка, изменение пароля невозможно';
            $form->submit->setAttrib('disable', 'disable');
        }
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
			    $data = $form->getValues();
                if ($data['password'] != $data['verifypassword']) {
                    $this->view->errorMessage = 'Введённые пароли не совпадают.<br />Попробуйте ещё раз.';
                    return;
                }
                $newPassword = $users->recoveryPassword($id, $data['password']);
                if ($newPassword) {
                    $this->view->errorMessage = 'Новый пароль установлен';
                }
			}
		}

    }


}











