<?php

class IndexController extends Zend_Controller_Action
{

    private $showHideTopic = null;

    public function init()
    {
        Zend_Loader::loadClass('My_Acl');
        $acl = new My_Acl();
        $role = My_Acl::getUserType();
        
        $this->showHideTopic = $acl->isAllowed($role,'showHideTopic','view');
		
		$category = new Application_Model_DbTable_Category();
		$tags = new Application_Model_DbTable_Tags();
		$users = new Application_Model_DbTable_Users();
		
		$this->view->nameCategory = $category->getNotEmpty();
		$this->view->nameTags = $tags->getNameTags();
		$this->view->nameUser = $users->getNameUsers();
    }

    public function indexAction()
    {
        $id = $this->_getParam('id', 0);
		$page = $this->_getParam('page', 1);
		
	    $topics = new Application_Model_DbTable_Topics();
		
		$this->view->paginator = $topics->getTopicByCategoryId($id, $page, $this->showHideTopic);
    }

    public function topicAction()
    {
		$id = $this->_getParam('id', 0);
		
        $topic = new Application_Model_DbTable_Topics();
		
		$topicRow = $topic->getTopicById($id);
		
        if ($topicRow):
            if (!$topicRow->hide || ($topicRow->hide && $this->showHideTopic)) :
                $this->view->topic = $topicRow;
            else :
                $this->_redirect('404');
            endif;
		else:
		    $this->_redirect('404');
		endif;

    }

    public function authorAction()
    {
        $id = $this->_getParam('id', 0);
		$page = $this->_getParam('page', 1);
		
	    $topics = new Application_Model_DbTable_Topics();
		
        $this->view->userId = $id;
		$this->view->paginator = $topics->getTopicByUserId($id, $page, $this->showHideTopic);
        
    }

    public function tagAction()
    {
        $id = $this->_getParam('id', 0);
		$page = $this->_getParam('page', 1);
		
	    $topics = new Application_Model_DbTable_Topics();
		
        $this->view->tagId = $id;
		$this->view->paginator = $topics->getTopicByTagId($id, $page, $this->showHideTopic);
    }

}









