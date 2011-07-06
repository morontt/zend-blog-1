<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRouters()
    {
        $controller = Zend_Controller_Front::getInstance();
        $router = $controller->getRouter();
        
        
        $router->addRoute('home', new Zend_Controller_Router_Route('/:page',
                                                array('module' => 'default',
                                                      'controller' => 'index', 
                                                      'action' => 'index',
                                                      'page' => 1)));
        
        $router->addRoute('topic', new Zend_Controller_Router_Route('topic/:id',
                                                array('module' => 'default',
                                                      'controller' => 'index', 
                                                      'action' => 'topic')));
        
        $router->addRoute('category', new Zend_Controller_Router_Route('category/:id/page/:page',
                                                array('module' => 'default',       
                                                      'controller' => 'index', 
                                                      'action' => 'index')));
        
        $router->addRoute('tag', new Zend_Controller_Router_Route('tag/:id/page/:page',
                                                array('module' => 'default',       
                                                      'controller' => 'index', 
                                                      'action' => 'tag')));
        
        $router->addRoute('author', new Zend_Controller_Router_Route('author/:id/page/:page',
                                                array('module' => 'default',
                                                      'controller' => 'index', 
                                                      'action' => 'author')));
        
        $router->addRoute('login', new Zend_Controller_Router_Route('login/',
                                                array('module' => 'default',
                                                      'controller' => 'auth', 
                                                      'action' => 'login')));
                                                      
        $router->addRoute('logout', new Zend_Controller_Router_Route('logout/',
                                                array('module' => 'default',
                                                      'controller' => 'auth', 
                                                      'action' => 'logout')));
                                                      
        $router->addRoute('signin', new Zend_Controller_Router_Route('registration/',
                                                array('module' => 'default',
                                                      'controller' => 'auth', 
                                                      'action' => 'signin')));
        
        $router->addRoute('forgot', new Zend_Controller_Router_Route('forgot/',
                                                array('module' => 'default',
                                                      'controller' => 'auth', 
                                                      'action' => 'forgot')));
                                                      
        //ARIA77
        
        $router->addRoute('aria77', new Zend_Controller_Router_Route('aria77/',
                                                array('module' => 'aria77',
                                                      'controller' => 'index', 
                                                      'action' => 'index')));

        //ARIA77/TOPIC
        $router->addRoute('topicControl', new Zend_Controller_Router_Route('aria77/topic/:page',
                                                array('module' => 'aria77',
                                                      'controller' => 'topic', 
                                                      'action' => 'index',
                                                      'page' => 1)));

        $router->addRoute('addTopic', new Zend_Controller_Router_Route('aria77/addTopic',
                                                array('module' => 'aria77',
                                                      'controller' => 'topic',
                                                      'action' => 'add')));

        $router->addRoute('editTopic', new Zend_Controller_Router_Route('aria77/editTopic/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'topic',
                                                      'action' => 'edit')));

        $router->addRoute('deleteTopic', new Zend_Controller_Router_Route('aria77/deleteTopic/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'topic',
                                                      'action' => 'delete')));

        //ARIA77/CATEGORY
        $router->addRoute('categoryControl', new Zend_Controller_Router_Route('aria77/category/:page',
                                                array('module' => 'aria77',
                                                      'controller' => 'category', 
                                                      'action' => 'index',
                                                      'page' => 1)));
        
        $router->addRoute('addCategory', new Zend_Controller_Router_Route('aria77/addCategory',
                                                array('module' => 'aria77',
                                                      'controller' => 'category',
                                                      'action' => 'add')));

        $router->addRoute('editCategory', new Zend_Controller_Router_Route('aria77/editCategory/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'category',
                                                      'action' => 'edit')));

        $router->addRoute('deleteCategory', new Zend_Controller_Router_Route('aria77/deleteCategory/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'category',
                                                      'action' => 'delete')));

        //ARIA77/TAGS
        $router->addRoute('tagsControl', new Zend_Controller_Router_Route('aria77/tag/:page',
                                                array('module' => 'aria77',
                                                      'controller' => 'tag', 
                                                      'action' => 'index',
                                                      'page' => 1)));

        $router->addRoute('addTag', new Zend_Controller_Router_Route('aria77/addTag',
                                                array('module' => 'aria77',
                                                      'controller' => 'tag',
                                                      'action' => 'add')));

        $router->addRoute('editTag', new Zend_Controller_Router_Route('aria77/editTag/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'tag',
                                                      'action' => 'edit')));

        $router->addRoute('deleteTag', new Zend_Controller_Router_Route('aria77/deleteTag/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'tag',
                                                      'action' => 'delete')));

        //ARIA77/USER
        $router->addRoute('usersControl', new Zend_Controller_Router_Route('aria77/users/:page',
                                                array('module' => 'aria77',
                                                      'controller' => 'users', 
                                                      'action' => 'index',
                                                      'page' => 1)));

        $router->addRoute('editUser', new Zend_Controller_Router_Route('aria77/editUser/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'users',
                                                      'action' => 'edit')));

        $router->addRoute('deleteUser', new Zend_Controller_Router_Route('aria77/deleteUser/:id',
                                                array('module' => 'aria77',
                                                      'controller' => 'users',
                                                      'action' => 'delete')));
        
        $controller->setRouter($router);
        
    }
    
}
