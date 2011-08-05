<?php

class Application_Model_Acl extends Zend_Acl {
    
    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('member'), 'guest');
        $this->addRole(new Zend_Acl_Role('author'), 'member');
        $this->addRole(new Zend_Acl_Role('admin'), 'author');

        $this->add(new Zend_Acl_Resource('page'));
        $this->add(new Zend_Acl_Resource('controlPage'), 'page');
        $this->add(new Zend_Acl_Resource('showHideTopic'));

        $this->deny('guest', 'controlPage', 'view');
        $this->allow('member', 'controlPage', 'view');
        $this->deny('guest', 'controlPage', 'editUser');
        $this->allow('admin', 'controlPage', 'editUser');
        $this->deny('guest', 'showHideTopic', 'view');
        $this->allow('author', 'showHideTopic', 'view');
    }

    public static function getUserType()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $role = $auth->getIdentity()->user_type;

            $user = new Application_Model_DbTable_Users;
            $user->latestActivity($auth->getIdentity()->user_id);
        } else {
            $role = 'guest';
        }

        return $role;
    }
}
