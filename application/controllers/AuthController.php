<?php

class AuthController extends Zend_Controller_Action
{
    //protected $_flashMessenger = null;

    public function init()
    {
        //$this->_flashMessenger = $this->_helper->FlashMessenger;
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

		if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
			    $data = $form->getValues();
                $hash = $users->getHashByLogin($data['username']);

                if ($hash) {
                    //$this->_flashMessenger->addMessage('Новый пароль выслан на указанный email');
                    $this->view->message = 'Новый пароль выслан на указанный email';
                    $request = Zend_Controller_Front::getInstance()->getRequest();
                    $url = $request->getScheme() . '://'
                         . $request->getHttpHost()
                         . $this->view->url(array('id' => $hash['id'],
                                                  'hash' => $hash['hash']), 'recovery');

                    $mail = new Application_Model_MailClass();
                    echo $mail->forgotPasswordMail();
				} else {
                    //$this->_flashMessenger->addMessage('Указанный email в базе данных отсутствует');
                    $this->view->message = 'Указанный email в базе данных отсутствует';
                }
                echo '<pre>';
                var_dump($url);
                echo '</pre>';
			}
		}

        //$this->view->message = $this->_flashMessenger->getMessages();
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
                    $this->view->errorMessage = 'Введённые пароли не совпадают.</br>Попробуйте ещё раз.';
                    return;
                }
                $newPassword = $users->recoveryPassword($id, $data['password']);
                if ($newPassword) {
                    $this->view->errorMessage = 'Новый пароль установлен';
                }
			}
		}

        echo '<pre>';
        var_dump($rightHash);
        var_dump($hash);
        echo '</pre>';
    }


}











