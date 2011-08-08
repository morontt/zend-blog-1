<?php

class IndexController extends Zend_Controller_Action
{

    private $_showHideTopic = null;
    private $_config;

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
        $acl = new Application_Model_Acl();
        $role = Application_Model_Acl::getUserType();
        
        $this->_showHideTopic = $acl->isAllowed($role,'showHideTopic','view');
		
		$category = new Application_Model_DbTable_Category();
		$tags = new Application_Model_DbTable_Tags();
		$users = new Application_Model_DbTable_Users();
		
		$this->view->nameCategory = $category->getNotEmpty();
		$this->view->nameTags = $tags->getNameTags();
		$this->view->nameUser = $users->getNameUsers();

        $this->_config = $this->getInvokeArg('bootstrap')->getOptions();
		//$this->_itemPerPage = $config['items']['per']['page'];
    }

    public function indexAction()
    {
        $id = $this->_getParam('id');
		$page = $this->_getParam('page');
        $fetch = $this->_getParam('fetch');
		
	    $topics = new Application_Model_DbTable_Topics();

        //extract of all records
        if ($fetch == 'index') {
            $paginator = $topics->getAllTopic($this->_showHideTopic);
            //$this->view->browsertitle = ' - Cтраница ' . $page;
        }
        //extract of records by category
        if ($fetch == 'category') {
            $paginator = $topics->getTopicByCategoryId($id, $this->_showHideTopic);
            $this->view->browsertitle = ' - Категория: ' . $this->view->nameCategory[$id]['name'];
        }
        //extract of records by tag
        if ($fetch == 'tag') {
            $paginator = $topics->getTopicByTagId($id, $this->_showHideTopic);
            $this->view->browsertitle = ' - Тег: ' . $this->view->nameTags[$id]['name'];
        }
        //extract of records by author
        if ($fetch == 'author') {
            $paginator = $topics->getTopicByUserId($id, $this->_showHideTopic);
            $this->view->browsertitle = ' - Автор: ' . $this->view->nameUser[$id];
        }
        
        $paginator->setItemCountPerPage($this->_config['items']['per']['page']);
        $paginator->SetCurrentPageNumber($page);
        
        if (count($paginator) < $page || $page < 1) {
            $this->gotoError404();
        }
		
		$this->view->paginator = $paginator;
    }

    public function topicAction()
    {
		$id = $this->_getParam('id');
        $page = $this->_getParam('page');
		
        $topic = new Application_Model_DbTable_Topics();
        $comments = new Application_Model_DbTable_Comments();
        $form = new Application_Form_CommentForm;
		
		$form->topicId->setValue($id);
        
        $topicRow = $topic->getTopicById($id);
		
        if ($topicRow) {
            if (!$topicRow->hide || ($topicRow->hide && $this->_showHideTopic)) {
                $this->view->topic = $topicRow;

                $paginator = $comments->getByTopicId($id);
                $paginator->setItemCountPerPage($this->_config['comments']['per']['page']);
                $paginator->SetCurrentPageNumber($page);
                $this->view->comments = $paginator;

                $this->view->form = $form;
            } else {
                $this->gotoError404(); //сообщить, что доступ к записи закрыт
            }
		} else {
		    $this->gotoError404();
		}
    }

    public function addcommentAction()
    {
        $comments = new Application_Model_DbTable_Comments();
        $form = new Application_Form_CommentForm;

		if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                $this->view->data = $data;
                $comments->saveComment($data);
            }
        }

        $topicId = $this->_getParam('topicId');
        $link = $this->view->url(array('id' => $topicId), 'topic');
        $this->_redirect($link);
    }

}
