<?php

class Aria77_CategoryController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    public function init()
    {
        $acl = new Application_Model_Acl();
        $role = Application_Model_Acl::getUserType();
        
        if (!$acl->isAllowed($role,'controlPage','edit')) {
            $this->_redirect('aria77/index/denied');
        }

        $this->_flashMessenger = $this->_helper->FlashMessenger;
    }

    public function indexAction()
    {
        $page = $this->_getParam('page');
        
        $category = new Application_Model_DbTable_Category();
		
        $this->view->nameCategory = $category->getNameCategory();

        $paginator = $category->getAllCategory();
        
        $config = $this->getInvokeArg('bootstrap')->getOptions();
		$itemPerPage = $config['category']['per']['page'];
		$paginator->setItemCountPerPage($itemPerPage);
        $paginator->SetCurrentPageNumber($page);

        if (count($paginator) < $page || $page < 1) {
            $this->_redirect('/error/404');
        }

        $this->view->messages = $this->_flashMessenger->getMessages();

		$this->view->paginator = $paginator;
    }

    public function addAction()
    {
        $form = new Application_Form_Category;
		$form->submit->setLabel('Создать');
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $formData = $form->getValues();
            
                $category = new Application_Model_DbTable_Category();
                $category->createNewCategory($formData['name'], $formData['parent_id']);

                $this->_flashMessenger->addMessage('Категория создана');
            
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
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $formData = $form->getValues();
                
                $result = $category->editCategory($id, $formData['name'],
                                                       $formData['parent_id'],
                                                       $formData['old_parent']);
                if ($result) {
                    $this->_flashMessenger->addMessage('Категория отредактирована');
                } else {
                    $this->_flashMessenger->addMessage('Категория не может быть отредактирована');
                }

                $this->_redirect('aria77/category');
            }
        } else {
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
        
        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да') {
                if ($category->deleteCategory($id)) {
                    $this->_flashMessenger->addMessage('Категория удалена');
                } else {
                    $this->_flashMessenger->addMessage('Категория не можеть быть удалена');
                }

                $this->_redirect('/aria77/category');
            } else {
                $this->_redirect('/aria77/category');
            }
            
        } else {
            $data = $category->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
                }
            $this->view->category = $data;
        }
    }

}







