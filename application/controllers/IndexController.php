<?php

class IndexController extends Zend_Controller_Action
{

    private $_showHideTopic = null;
    private $_itemPerPage;

    protected function gotoError404()
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = 'Страница не найдена';
        $this->view->error404 = true;

        return $this->_request->setControllerName('error')
                              ->setActionName('error');
    }

    public function init()
    {
        Zend_Loader::loadClass('My_Acl');
        $acl = new My_Acl();
        $role = My_Acl::getUserType();
        
        $this->_showHideTopic = $acl->isAllowed($role,'showHideTopic','view');
		
		$category = new Application_Model_DbTable_Category();
		$tags = new Application_Model_DbTable_Tags();
		$users = new Application_Model_DbTable_Users();
		
		$this->view->nameCategory = $category->getNotEmpty();
		$this->view->nameTags = $tags->getNameTags();
		$this->view->nameUser = $users->getNameUsers();

        $config = $this->getInvokeArg('bootstrap')->getOptions();
		$this->_itemPerPage = $config['items']['per']['page'];
    }

    public function indexAction()
    {
        $id = $this->_getParam('id', 0);
		$page = $this->_getParam('page');
		
	    $topics = new Application_Model_DbTable_Topics();
        
        $paginator = $topics->getTopicByCategoryId($id, $this->_showHideTopic);
        $paginator->setItemCountPerPage($this->_itemPerPage);
        $paginator->SetCurrentPageNumber($page);
        
        if (count($paginator) < $page || $page < 1) {
            $this->gotoError404();
        }
		
		$this->view->paginator = $paginator;
    }

    public function topicAction()
    {
		$id = $this->_getParam('id');
		
        $topic = new Application_Model_DbTable_Topics();
		
		$topicRow = $topic->getTopicById($id);
		
        if ($topicRow):
            if (!$topicRow->hide || ($topicRow->hide && $this->_showHideTopic)) :
                $this->view->topic = $topicRow;
            else :
                $this->gotoError404(); //сообщить, что доступ к записи закрыт
            endif;
		else:
		    $this->gotoError404();
		endif;

    }

    public function authorAction()
    {
        $id = $this->_getParam('id');
		$page = $this->_getParam('page');

        //if (!is_numeric($page))
        //    $this->_redirect('/error/404');
		
	    $topics = new Application_Model_DbTable_Topics();
		
        $this->view->userId = $id;
		$paginator = $topics->getTopicByUserId($id, $this->_showHideTopic);
        $paginator->setItemCountPerPage($this->_itemPerPage);
        $paginator->SetCurrentPageNumber($page);

        if (count($paginator) < $page || $page < 1)
            $this->gotoError404();

		$this->view->paginator = $paginator;
    }

    public function tagAction()
    {
        $id = $this->_getParam('id');
		$page = $this->_getParam('page');

        //if (!is_numeric($page))
        //    $this->_redirect('/error/404');
		
	    $topics = new Application_Model_DbTable_Topics();
		
        $this->view->tagId = $id;
        $paginator = $topics->getTopicByTagId($id, $this->_showHideTopic);
        $paginator->setItemCountPerPage($this->_itemPerPage);
        $paginator->SetCurrentPageNumber($page);

        if (count($paginator) < $page || $page < 1)
            $this->gotoError404();

		$this->view->paginator = $paginator;
    }

}









