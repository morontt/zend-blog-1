<?php

class Zend_View_Helper_siteMenu extends Zend_View_Helper_Abstract
{
    public function siteMenu()
	{
		$rezult = '<div id="menu"><ul>' . PHP_EOL;
        $rezult .= '<li><a href="' . $this->view->url(array('module' => 'default',
                                                            'controller' => 'index',
                                                            'action'   => 'index',
                                                            'page' => 1),
                                            'home') . '">Home</a></li>' . PHP_EOL;
        
        $auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            
            $rezult .= '<li class="user">' . $identity->username . '</li>' . PHP_EOL;

            $rezult .= '<li><a href="' . $this->view->url(array('module' => 'aria77',
                                                                'controller' => 'topic',
                                                                'action'   => 'add'),
                                            'addTopic') . '">Новая запись</a></li>' . PHP_EOL;
            
            $rezult .= '<li><a href="' . $this->view->url(array('module' => 'aria77',
                                                                'controller' => 'index',
                                                                'action'   => 'index'),
                                            'aria77') . '">Control Panel</a></li>' . PHP_EOL;
            
            $rezult .= '<li><a href="' . $this->view->url(array('module' => 'default',
                                                                'controller' => 'auth',
                                                                'action'   => 'logout'),
                                            'logout') . '">Logout</a></li>' . PHP_EOL;
        } else {
            $rezult .= '<li><a href="' . $this->view->url(array('module' => 'default',
                                                                'controller' => 'auth',
                                                                'action'   => 'login'),
                                               'login') . '">Login</a></li>' . PHP_EOL;
            $rezult .= '<li><a href="' . $this->view->url(array('module' => 'default',
                                                                'controller' => 'auth',
                                                                'action'   => 'signin'),
                                        'signin') . '">Registration</a></li>' . PHP_EOL;
        }
        
        $rezult .= '</ul></div>' . PHP_EOL;
        
        return $rezult;
	}
}
