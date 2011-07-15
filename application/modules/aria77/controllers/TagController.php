<?php

class Aria77_TagController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    
    public function init()
    {
        //Zend_Loader::loadClass('My_Acl');
        $acl = new My_Acl();
        $role = My_Acl::getUserType();
        
        if (!$acl->isAllowed($role,'controlPage','view')) {
            $this->_redirect('aria77/index/denied');
        }

        $this->_flashMessenger = $this->_helper->FlashMessenger;
    }

    public function indexAction()
    {
        $page = $this->_getParam('page');
        
        $tags = new Application_Model_DbTable_Tags();
		
        $this->view->nameTags = $tags->getNameTags();

        $paginator = $tags->getAllTags();

        $config = $this->getInvokeArg('bootstrap')->getOptions();
		$itemPerPage = $config['tags']['per']['page'];
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
        $form = new Application_Form_Tags;
		$form->submit->setLabel('Создать');
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST))
            {
                $formData = $form->getValues();
            
                $tag = new Application_Model_DbTable_Tags();
                $tag->createNewTag($formData['name']);

                $this->_flashMessenger->addMessage('Тег создан');
            
                $this->_redirect('aria77/tag');
            }
        }
    }

    public function editAction()
    {
        $id = $this->_getParam('id', 0);
        
        $form = new Application_Form_Tags;
		$form->submit->setLabel('Отправить');
        $this->view->form = $form;
        
        $tag = new Application_Model_DbTable_Tags();
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST))
            {
                $formData = $form->getValues();
                
                $tag->editTag($id, $formData['name']);

                $this->_flashMessenger->addMessage('Тег отредактирован');
            
                $this->_redirect('aria77/tag');
            }
        } else {
            $data = $tag->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
            }
            
            $data = $data->toArray();
            
            $form->populate($data);
        }
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $tag = new Application_Model_DbTable_Tags();
        
        if ($this->getRequest()->isPost())
        {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да')
            {
                $result = $tag->deleteTag($id);

                if ($result) {
                    $this->_flashMessenger->addMessage('Тег удалён');
                } else {
                    $this->_flashMessenger->addMessage('Тег не может быть удалён');
                }

                $this->_redirect('/aria77/tag');
            } else
            {
                $this->_redirect('/aria77/tag');
            }
            
        } else
        {
            $data = $tag->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
                }
            $this->view->tag = $data;
        }
    }
    
}







