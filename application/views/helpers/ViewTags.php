<?php

class Zend_View_Helper_ViewTags extends Zend_View_Helper_Abstract
{
    public function viewTags($topicId)
	{
	    $result = NULL;
        $tags = new Application_Model_DbTable_RelationTopicTag();
        $rows = $tags->getArrayTags($topicId);
        
        if(!empty($rows))
        {
            $result = 'Теги: ';
            foreach($rows as $key => $value)
            {
                $result .= '<a href="' . $this->view->url(array(
                                        'module' => 'default',
                                        'controller' => 'index',
                                        'action' => 'tag',
                                        'id' => $value['tag_id'],
                                        'page' => 1), 'tag')
                        . '">' . $this->view->nameTags[$value['tag_id']]['name'] . '</a>, ';
            }
            
        }
        
        $length = strlen($result);
        $result = substr($result, 0, $length - 2);
        
        return $result;
	}
}
