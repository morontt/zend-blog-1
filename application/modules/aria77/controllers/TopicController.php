<?php

class Aria77_TopicController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    
    public function init()
    {
        $acl = new Application_Model_Acl();
        $role = Application_Model_Acl::getUserType();
        
        if (!$acl->isAllowed($role,'controlPage','view')) {
            $this->_redirect('aria77/index/denied');
        }

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

        if ((count($paginator) < $page && count($paginator) != 0) ||
                $page < 1 ||
                (count($paginator) == 0 && $page > 1)) {
            $this->getResponse()->setHttpResponseCode(404);
            $this->view->message = 'Страница не найдена';
            $this->view->error404 = true;

            return $this->_request->setModuleName('default')
                                  ->setControllerName('error')
                                  ->setActionName('error');
        }
        
        $this->view->messages = $this->_flashMessenger->getMessages();

		$this->view->paginator = $paginator;
    }

    public function addAction()
    {
        $form = new Application_Form_Topic;
		$form->submit->setLabel('Создать запись');
        $this->view->form = $form;
        
        $this->clearCacheCategory();
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $formData = $form->getValues();
                if (empty($formData['title'])) {
                    $formData['title'] = 'no subject';
                }
                
                $topic = new Application_Model_DbTable_Topics();
                $topicId = $topic->createNewTopic($formData);
                
                Application_Model_SitemapClass::createSitemap();

                $this->_flashMessenger->addMessage('Запись создана');

                $this->_redirect('aria77/topic');
            }
        }
    }

    public function editAction()
    {
        $id = $this->_getParam('id', 0);
        
        $topic = new Application_Model_DbTable_Topics();
        $tags = new Application_Model_DbTable_RelationTopicTag();
        $form = new Application_Form_Topic;
		$form->submit->setLabel('Изменить запись');
        $this->view->form = $form;
        
        
        $this->clearCacheCategory();
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $formData = $form->getValues();
                if (empty($formData['title'])) {
                    $formData['title'] = 'no subject';
                }

                $topic->editTopic($formData, $id);
                
                Application_Model_SitemapClass::createSitemap();

                $this->_flashMessenger->addMessage('Запись отредактирована');
                
                $this->_redirect('aria77/topic');
            }
        } else {
            $data = $topic->getTopicById($id)->toArray();
            $data['tags'] = $tags->getStringTags($id);
            
            $form->populate($data);
        }
        
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $topic = new Application_Model_DbTable_Topics();
        
        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Да') {
                $topic->deleteTopic($id);
                
                Application_Model_SitemapClass::createSitemap();
                
                $this->_flashMessenger->addMessage('Запись удалена');
                
                $this->_redirect('/aria77/topic');
            } else {
                $this->_redirect('/aria77/topic');
            }
            
        } else {
            $this->view->topic = $topic->getTopicById($id);
        }
        
    }
    
    protected function clearCacheCategory()
    {
        $cache = Zend_Cache::factory('Core', 'File', array(), array('cache_dir' => realpath(APPLICATION_PATH . '/../cache')));
        $cache->remove('nameCategory');
    }

    public function updateurlAction()
    {
        $topic = new Application_Model_DbTable_Topics();
        
        $select = $topic->select()
                        ->from('blog_posts', array('post_id', 'title'));
        $topics = $topic->fetchAll($select);
        
        $arrayUrl = array();
        
        foreach ($topics as $item) {
            
            $url = Zml_Transform::ruTransform($item->title);
            $data = array(
                'url' => $url,
            );
            
            $arrayUrl[] = array(
                'id'  => $item->post_id,
                'url' => $url,
            );
            
            $topic->update($data, 'post_id = ' . $item->post_id);
        }
        
        $this->view->urlArray = $arrayUrl;
    }
}







