<?php

class Zend_View_Helper_ViewTags extends Zend_View_Helper_Abstract
{
    public function viewTags($topicId)
	{
	    $frontendOptions = array('lifetime' => 86400,
                                 'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => '../cache/');
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        
        $result = $cache->load('tagsByTopic_' . $topicId);
        if (!$result) {
            $result = 'empty';
            $tags = new Application_Model_DbTable_RelationTopicTag();
            $rows = $tags->getArrayTags($topicId);
        
            if(!empty($rows))
            {
                $linkTag = array();
                $arrayTagCache = array();
                foreach($rows as $key => $value)
                {
                    $linkTag[] = '<a href="' . $this->view->url(array('id' => $value['tag_id']), 'tag')
                               . '">' . $this->view->nameTags[$value['tag_id']]['name'] . '</a>';
                    $arrayTagCache[] = 'tag_id_' . $value['tag_id'];
                }
                $result = 'Теги: ' . implode(', ', $linkTag);
            }
            if ($result == 'empty') {
                $cache->save($result, 'tagsByTopic_' . $topicId);
            } else {
                $cache->save($result, 'tagsByTopic_' . $topicId, $arrayTagCache);
            }
        }
        
        if ($result == 'empty') $result = NULL;
        
        return $result;
	}
}
