<?php

class Aria77_CategoryController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Loader::loadClass('My_Acl');
        $acl = new My_Acl();
        $role = My_Acl::getUserType();
        
        if (!$acl->isAllowed($role,'controlPage','view'))
            $this->_redirect('aria77/index/denied');
    }

    public function indexAction()
    {
        $page = $this->_getParam('page');
        
        $category = new Application_Model_DbTable_Category();
		
        $this->view->nameCategory = $category->getNameCategory();

        $paginator = $category->getAllCategory($page);
        if (count($paginator) < $page || $page < 1)
            $this->_redirect('/error/404');

		$this->view->paginator = $paginator;
    }

    public function addAction()
    {
        $form = new Application_Form_Category;
		$form->submit->setLabel('Создать');
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST))
            {
                $formData = $form->getValues();
            
                $category = new Application_Model_DbTable_Category();
                $category->createNewCategory($formData['name'], $formData['parent_id']);
            
                $this->_redirect('aria77/category');
            }
        }
    }

    public function editAction()
    {
        $id = $this->_getParam('id', 0);
        
        $form = new Application_Form_Category;
		$form->submit->setLabel('Отправить');
        $this->view->form = $form;
        
        $category = new Application_Model_DbTable_Category();
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST))
            {
                $formData = $form->getValues();
                
                $category->editCategory($id, $formData['name'],
                                             $formData['parent_id'],
                                             $formData['old_parent']);
            
                $this->_redirect('aria77/category');
            }
        } else
        {
            $data = $category->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
                }
            $data = $data->toArray();

            $oldParent = array('old_parent' => $data['parent_id']);
            $data = array_merge($data, $oldParent);
            
            $form->populate($data);
        }
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $category = new Application_Model_DbTable_Category();
        $this->view->statusAction = 0;
        
        if ($this->getRequest()->isPost())
        {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да')
            {
                $category->deleteCategory($id);
                $this->view->statusAction = 1;
            } else
            {
                $this->_redirect('/aria77/category');
            }
            
        } else
        {
            $data = $category->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
                }
            $this->view->category = $data;
        }
    }

}







