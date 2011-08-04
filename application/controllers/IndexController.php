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
        //Zend_Loader::loadClass('My_Acl');
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
        $id = $this->_getParam('id');
		$page = $this->_getParam('page');
        $fetch = $this->_getParam('fetch');
		
	    $topics = new Application_Model_DbTable_Topics();

        //extract of all records
        if ($fetch == 'index') {
            $paginator = $topics->getAllTopic($this->_showHideTopic);
            $this->view->browsertitle = ' - Cтраница ' . $page;
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

}
