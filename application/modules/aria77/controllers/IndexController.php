<?php

class Aria77_IndexController extends Zend_Controller_Action
{
    private $_accessControl;
    
    public function init()
    {
        //Zend_Loader::loadClass('My_Acl');
        $acl = new My_Acl();
        $role = My_Acl::getUserType();
        
        $this->_accessControl = $acl->isAllowed($role,'controlPage','view');
        
        $this->view->editUser = $acl->isAllowed($role,'controlPage','editUser');
    }

    public function indexAction()
    {
        if (!$this->_accessControl)
            $this->_redirect('aria77/index/denied');
    }

    public function deniedAction()
    {
        // action body
    }


}



