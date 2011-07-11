<?php

class Aria77_TagController extends Zend_Controller_Action
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
        
        $tags = new Application_Model_DbTable_Tags();
		
        $this->view->nameTags = $tags->getNameTags();

        $paginator = $tags->getAllTags($page);
        if (count($paginator) < $page || $page < 1)
            $this->_redirect('/error/404');

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
            
                $this->_redirect('aria77/tag');
            }
        } else
        {
            $data = $tag->getById($id)->toArray();
            
            $form->populate($data);
        }
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $tag = new Application_Model_DbTable_Tags();
        $this->view->statusAction = 0;
        
        if ($this->getRequest()->isPost())
        {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да')
            {
                $tag->deleteTag($id);
                $this->view->statusAction = 1;
            } else
            {
                $this->_redirect('/aria77/tag');
            }
            
        } else
        {
            $this->view->tag = $tag->getById($id);
        }
    }
    
}







