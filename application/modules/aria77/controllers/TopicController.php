<?php

class Aria77_TopicController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    
    public function init()
    {
        //Zend_Loader::loadClass('My_Acl');
        $acl = new My_Acl();
        $role = My_Acl::getUserType();
        
        if (!$acl->isAllowed($role,'controlPage','view'))
            $this->_redirect('aria77/index/denied');

        $this->_flashMessenger = $this->_helper->FlashMessenger;
    }

    public function indexAction()
    {
        $page = $this->_getParam('page');
        
        $category = new Application_Model_DbTable_Category();
        $topics = new Application_Model_DbTable_Topics();
		
        $this->view->nameCategory = $category->getNameCategory();

        $paginator = $topics->getTopicForControl();

        $config = $this->getInvokeArg('bootstrap')->getOptions();
		$itemPerPage = $config['itemsControl']['per']['page'];
		$paginator->setItemCountPerPage($itemPerPage);
        $paginator->SetCurrentPageNumber($page);

        if (count($paginator) < $page || $page < 1)
            $this->_redirect('/error/404');
        
        $this->view->messages = $this->_flashMessenger->getMessages();

		$this->view->paginator = $paginator;
    }

    public function addAction()
    {
        $form = new Application_Form_Topic;
		$form->submit->setLabel('Создать запись');
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST))
            {
                $formData = $form->getValues();
                if (empty($formData['title'])) {
                    $formData['title'] = 'no subject';
                }
                
                $topic = new Application_Model_DbTable_Topics();
                $topicId = $topic->createNewTopic($formData);

                $this->_flashMessenger->addMessage('Запись создана');

                $this->_redirect('aria77/topic');
            }
        }
    }

    public function editAction()
    {
        $id = $this->_getParam('id', 0);
        
        $topic = new Application_Model_DbTable_Topics();
        $form = new Application_Form_Topic;
		$form->submit->setLabel('Изменить запись');
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST))
            {
                $formData = $form->getValues();
                if (empty($formData['title'])) {
                    $formData['title'] = 'no subject';
                }

                $topic->editTopic($formData, $id);

                $this->_flashMessenger->addMessage('Запись отредактирована');
                
                $this->_redirect('aria77/topic');
            }
        } else
        {
            $data = $topic->getTopicById($id)->toArray();
            
            $form->populate($data);
        }
        
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $topic = new Application_Model_DbTable_Topics();
        
        if ($this->getRequest()->isPost())
        {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да')
            {
                $topic->deleteTopic($id);
                $this->_flashMessenger->addMessage('Запись удалена');
                
                $this->_redirect('aria77/topic');
            } else
            {
                $this->_redirect('/aria77/topic');
            }
            
        } else
        {
            $this->view->topic = $topic->getTopicById($id);
        }
        
    }

}







