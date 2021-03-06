<?php

class IndexController extends Zend_Controller_Action
{

    private $_showHideTopic = null;
    private $_config;
    private $_cache;

    public function init()
    {
        $acl = new Application_Model_Acl();
        $role = Application_Model_Acl::getUserType();
        
        $this->_showHideTopic = $acl->isAllowed($role,'showHideTopic','view');
        
        $frontendOptions = array('lifetime' => 345600,
                                 'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => realpath(APPLICATION_PATH . '/../cache'));
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        
        $this->_cache = $cache;
        
        $cacheNameTags = $cache->load('nameTags');
        if (!$cacheNameTags) {
            $tags = new Application_Model_DbTable_Tags();
            $cacheNameTags = $tags->getNameTags();
            $cache->save($cacheNameTags, 'nameTags');
        }
        $cacheNameCategory = $cache->load('nameCategory');
        if (!$cacheNameCategory) {
            $category = new Application_Model_DbTable_Category();
            $cacheNameCategory = $category->getNotEmpty();
            $cache->save($cacheNameCategory, 'nameCategory');
        }
        $cacheNameUsers = $cache->load('nameUsers');
        if (!$cacheNameUsers) {
            $users = new Application_Model_DbTable_Users();
            $cacheNameUsers = $users->getNameUsers();
            $cache->save($cacheNameUsers, 'nameUsers');
        }

        $this->view->nameCategory = $cacheNameCategory;
        $this->view->nameTags = $cacheNameTags;
        $this->view->nameUser = $cacheNameUsers;
        
        $this->view->showIpAddres = $acl->isAllowed($role, 'page', 'showIpAddres');

        $this->_config = $this->getInvokeArg('bootstrap')->getOptions();
    }
    
    protected function gotoError404()
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = '???????????????? ???? ??????????????';
        $this->view->error404 = true;

        return $this->_request->setControllerName('error')
                              ->setActionName('error');
    }

    public function indexAction()
    {
        $id = $this->_getParam('id');
		$page = $this->_getParam('page');
        $fetch = $this->_getParam('fetch');
        
        $this->view->showGoogleAnalytic = TRUE;
        $this->view->syntaxHighlight = TRUE;
        $this->view->robots = FALSE;
		
	    $topics = new Application_Model_DbTable_Topics();
        
        //extract of all records
        if ($fetch == 'index') {
            $paginator = $topics->getAllTopic($this->_showHideTopic);
            //$this->view->browsertitle = ' - C?????????????? ' . $page;
            $this->view->robots = TRUE;
        }
        //extract of records by category
        if ($fetch == 'category') {
            $paginator = $topics->getTopicByCategoryId($id, $this->_showHideTopic);
            $this->view->browsertitle = ' - ??????????????????: ' . $this->view->nameCategory[$id]['name'];
        }
        //extract of records by tag
        if ($fetch == 'tag') {
            $paginator = $topics->getTopicByTagId($id, $this->_showHideTopic);
            $this->view->browsertitle = ' - ??????: ' . $this->view->nameTags[$id]['name'];
        }
        //extract of records by author
        if ($fetch == 'author') {
            $paginator = $topics->getTopicByUserId($id, $this->_showHideTopic);
            $this->view->browsertitle = ' - ??????????: ' . $this->view->nameUser[$id];
        }
        
        $paginator->setItemCountPerPage($this->_config['items']['per']['page']);
        $paginator->SetCurrentPageNumber($page);
        
        if (count($paginator) < $page || $page < 1) {
            $this->gotoError404();
        }
        
        $topicCount = new Application_Model_DbTable_TopicsCount();
        
        $arrayQuery = array();
        foreach ($paginator as $pagin) {
            $arrayQuery[] = $pagin->post_id;
        }
        
        $this->view->arrayCount = $topicCount->getArrayCounts($arrayQuery);
		$this->view->paginator = $paginator;
    }

    public function topicAction()
    {
		$id = $this->_getParam('id');
        $page = $this->_getParam('page');
        $hide = $this->_getParam('hide');
        $formData = $this->_getParam('formData');
        
        $request = $this->getRequest()->getRequestUri();
        $this->view->robots = strpos($request, 'reply') ? FALSE : TRUE;
        
        $this->view->showGoogleAnalytic = TRUE;
        $this->view->syntaxHighlight = TRUE;
		
        $topic = new Application_Model_DbTable_Topics();
        $comments = new Application_Model_DbTable_Comments();
        $form = new Application_Form_CommentForm;
		
		$form->topicId->setValue($id);
        
        $topicRow = $topic->getTopicById($id);

        if (!empty($formData)) {
            $form->isValid($formData);
            $this->view->formHide = FALSE;
        } else {
            $this->view->formHide = ($hide == 'hide') ? TRUE : FALSE;
        }

        if ($topicRow) {
            $viewcount = new Application_Model_DbTable_TopicsCount();
            if (!$topicRow->hide) {
                $viewcount->setViewCount($id);
            }
            if (!$topicRow->hide || $this->_showHideTopic) {
                $paginator = $comments->getByTopicId($id);
                $paginator->setItemCountPerPage($this->_config['comments']['per']['page']);
                $paginator->SetCurrentPageNumber($page);
                $this->view->topic = $topicRow;
                $this->view->comments = $paginator;
                $this->view->form = $form;
                $this->view->counts = $viewcount->getCounts($id);
            } else {
                $this->gotoError404(); //????????????????, ?????? ???????????? ?? ???????????? ????????????
            }
		} else {
		    $this->gotoError404();
		}
    }

    public function addcommentAction()
    {
        $topicId = $this->_getParam('topicId');

        $comments = new Application_Model_DbTable_Comments();
        $form = new Application_Form_CommentForm;

		if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                $comments->saveComment($data, $this->_config['comments']['sendmail']);
                
                $this->_redirect($this->view->url(array('id' => $topicId), 'topic'));
            } else {
                $data = $form->getValues();
                Zend_Controller_Action::_forward('topic', 'index', 'default', array('id'       => $topicId,
                                                                                    'formData' => $data));
            }
        }
    }
    
    public function ajaxcommentAction()
    {
        $topicId = $this->_getParam('topicId');
        
        $this->_helper->layout->disableLayout();

        $comments = new Application_Model_DbTable_Comments();
        $form = new Application_Form_CommentForm;

		if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $this->view->formValid = true;
                $data = $form->getValues();
                $resultDb = $comments->saveComment($data, $this->_config['comments']['sendmail']);
                
                $commentData = $comments->getById($resultDb);
                
                $this->view->comment = $commentData;
                
                //$this->_redirect($this->view->url(array('id' => $topicId), 'topic'));
            } else {
                $this->view->formValid = false;
                //$data = $form->getValues();
                $this->view->form = $form;
                //Zend_Controller_Action::_forward('topic', 'index', 'default', array('id'       => $topicId,
                //                                                                    'formData' => $data));
            }
        }
    }
    
    public function feedAction()
    {
        $feedType = $this->_getParam('feed');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $nameCacheFeed = 'Feed_' . $feedType;
        
        $cacheFeed = $this->_cache->load($nameCacheFeed);
        if (!$cacheFeed) {
            $topics = new Application_Model_DbTable_Topics();
            $cacheFeed = $topics->getFeedData($feedType);
            $this->_cache->save($cacheFeed, $nameCacheFeed);
        }
        
        $feedArray = $cacheFeed;
        
        $feed = Zend_Feed::importArray($feedArray, $feedType);
        $feed->send();
    }
    
}
