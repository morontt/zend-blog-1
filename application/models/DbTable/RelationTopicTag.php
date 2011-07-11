<?php

class Application_Model_DbTable_RelationTopicTag extends Zend_Db_Table_Abstract
{
    protected $_name = 'relation_topictag';
	
    public function addRelation($data, $topicId)
    {
        $tags = new Application_Model_DbTable_Tags;
        $tags->setCountTags($data, 1);
        
        foreach($data as $key => $value) {
            $row = array('post_id' => $topicId,
                          'tag_id' => $value);
            $this->insert($row);
        }
    }
    
    public function deleteRelation($topicId)
    {
        $temp = $this->fetchAll('post_id = ' . $topicId)->toArray();
        
        if (!empty($temp)) {
            foreach($temp as $key => $value) {
                $data[] = $value['tag_id'];
            }
            $tags = new Application_Model_DbTable_Tags;
            $tags->setCountTags($data, -1);
        }
        
        $this->delete('post_id = ' . $topicId);
    }
    
    public function getTags($topicId)
    {
        $row = $this->fetchAll('post_id = ' . $topicId);
        
        return $row->toArray();
    }
    
}
