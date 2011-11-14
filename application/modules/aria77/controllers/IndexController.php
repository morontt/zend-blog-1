<?php

class Aria77_IndexController extends Zend_Controller_Action
{
    private $_accessControl;
    
    public function init()
    {
        $acl = new Application_Model_Acl();
        $role = Application_Model_Acl::getUserType();
        
        $this->_accessControl = $acl->isAllowed($role,'controlPage','view');
        
        $this->view->editUser = $acl->isAllowed($role,'controlPage','editUser');
        $this->view->edit = $acl->isAllowed($role,'controlPage','edit');
    }

    public function indexAction()
    {
        if (!$this->_accessControl) {
            $this->_redirect('aria77/index/denied');
        }
    }

    public function deniedAction()
    {
        // action body
    }

    public function clearcacheAction()
    {
        if (!$this->_accessControl) {
            $this->_redirect('aria77/index/denied');
        }
        
        $cache = Zend_Cache::factory('Core', 'File', array(), array('cache_dir' => '../cache/'));
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }
}



