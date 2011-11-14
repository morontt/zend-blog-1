<?php

class Zend_View_Helper_ListTags extends Zend_View_Helper_Abstract
{
    public function listTags()
    {
        $tagsArray = array();
        foreach ($this->view->nameTags as $key => $value) {
            if ($value['count'] > 0) {
                $tagsArray[] = array('title' => $value['name'],
                                     'weight' => $value['count'],
                              'params' => array('url' => $this->view->url(array(
                                           'module'     => 'default',
                                           'controller' => 'index',
                                           'action'     => 'tag',
                                           'id'         => $key,
                                           'page'       => 1), 'tag')));
            }
        }
        
        $cloud = new Zend_Tag_Cloud(array('tags' => $tagsArray));
        
        return $cloud;
    }    
}
