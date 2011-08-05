<?php

class Aria77_UsersController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    public function init()
    {
        $acl = new Application_Model_Acl();
        $role = Application_Model_Acl::getUserType();
        
        if (!$acl->isAllowed($role,'controlPage','editUser')) {
            $this->_redirect('aria77/index/denied');
        }

        $this->_flashMessenger = $this->_helper->FlashMessenger;
    }

    public function indexAction()
    {
        $page = $this->_getParam('page');
        
        $users = new Application_Model_DbTable_Users();
		
        $this->view->nameUsers = $users->getNameUsers();

        $paginator = $users->getAllUsers();

        $config = $this->getInvokeArg('bootstrap')->getOptions();
		$itemPerPage = $config['users']['per']['page'];
		$paginator->setItemCountPerPage($itemPerPage);
        $paginator->SetCurrentPageNumber($page);

        if (count($paginator) < $page || $page < 1) {
            $this->_redirect('/error/404');
        }

        $this->view->messages = $this->_flashMessenger->getMessages();
        
		$this->view->paginator = $paginator;
    }

    public function editAction()
    {
        $id = $this->_getParam('id', 0);
        
        $form = new Application_Form_UserType;
        $this->view->form = $form;
        
        $users = new Application_Model_DbTable_Users();
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $formData = $form->getValues();
                
                $users->editUser($id, $formData['user_type']);

                $this->_flashMessenger->addMessage('Пользователь отредактирован');
            
                $this->_redirect('aria77/users');
            }
        } else {
            $data = $users->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
                }
            $data = $data->toArray();
            
            $this->view->username = $data['username'];
            $form->populate($data);
        }
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $users = new Application_Model_DbTable_Users();
        
        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да') {
                $users->deleteUser($id);

                $this->_flashMessenger->addMessage('Пользователь удалён');

                $this->_redirect('/aria77/users');
            } else {
                $this->_redirect('/aria77/users');
            }
            
        } else {
            $data = $users->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
                }
            $this->view->user = $data;
        }
    }


}





