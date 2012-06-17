<?php

class Aria77_TagController extends Zend_Controller_Action
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
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $formData = $form->getValues();
            
                $tag = new Application_Model_DbTable_Tags();
                $tag->createNewTag($formData['name']);
                $this->clearCacheTag(false);

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
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $formData = $form->getValues();
                
                $tag->editTag($id, $formData['name']);
                $this->clearCacheTag($id);

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
        
        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да') {
                $result = $tag->deleteTag($id);

                if ($result) {
                    $this->_flashMessenger->addMessage('Тег удалён');
                    $this->clearCacheTag($id);
                } else {
                    $this->_flashMessenger->addMessage('Тег не может быть удалён');
                }

                $this->_redirect('/aria77/tag');
            } else {
                $this->_redirect('/aria77/tag');
            }
            
        } else {
            $data = $tag->getById($id);
            if (!$data) {
                $this->_redirect('error/404');
                }
            $this->view->tag = $data;
        }
    }
    
    protected function clearCacheTag($id)
    {
        $cache = Zend_Cache::factory('Core', 'File', array(), array('cache_dir' => '../cache/'));
        $cache->remove('nameTags');
        
        if ($id) {
            $tag = 'tag_id_' . $id;
            $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                            array($tag));
        }
        
    }
    
    public function updateurlAction()
    {
        $topic = new Application_Model_DbTable_Tags();
        
        $select = $topic->select()
                        ->from('tags', array('tag_id', 'name'));
        $topics = $topic->fetchAll($select);
        
        $arrayUrl = array();
        
        foreach ($topics as $item) {
            
            $url = Zml_Transform::ruTransform($item->name);
            $data = array(
                'url' => $url,
            );
            
            $arrayUrl[] = array(
                'id'  => $item->tag_id,
                'url' => $url,
            );
            
            $topic->update($data, 'tag_id = ' . $item->tag_id);
        }
        
        $this->view->urlArray = $arrayUrl;
    }
}
