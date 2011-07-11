<?php

class Aria77_UsersController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Loader::loadClass('My_Acl');
        $acl = new My_Acl();
        $role = My_Acl::getUserType();
        
        if (!$acl->isAllowed($role,'controlPage','editUser'))
            $this->_redirect('aria77/index/denied');
    }

    public function indexAction()
    {
        $page = $this->_getParam('page');
        
        $users = new Application_Model_DbTable_Users();
		
        $this->view->nameUsers = $users->getNameUsers();

        $paginator = $users->getAllUsers($page);
        if (count($paginator) < $page || $page < 1)
            $this->_redirect('/error/404');

		$this->view->paginator = $paginator;
    }

    public function editAction()
    {
        $id = $this->_getParam('id', 0);
        
        $form = new Application_Form_UserType;
        $this->view->form = $form;
        
        $users = new Application_Model_DbTable_Users();
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST))
            {
                $formData = $form->getValues();
                
                $users->editUser($id, $formData['user_type']);
            
                $this->_redirect('aria77/users');
            }
        } else
        {
            $data = $users->getById($id)->toArray();
            
            $this->view->username = $data['username'];
            $form->populate($data);
        }
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $users = new Application_Model_DbTable_Users();
        $this->view->statusAction = 0;
        
        if ($this->getRequest()->isPost())
        {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да')
            {
                $users->deleteUser($id);
                $this->view->statusAction = 1;
            } else
            {
                $this->_redirect('/aria77/users');
            }
            
        } else
        {
            $this->view->user = $users->getById($id);
        }
    }


}





