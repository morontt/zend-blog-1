<?php

class Application_Model_DbTable_RelationTopicTag extends Zend_Db_Table_Abstract
{
    protected $_name = 'relation_topictag';
	
    public function addRelation($tagString, $topicId)
    {
        $tags = new Application_Model_DbTable_Tags;
        
        $tagsArray = explode(',', $tagString);

        function trimStringTag(&$item)
        {
            $item = trim($item);
            $item = mb_strtolower($item, 'UTF-8');
        }

        array_walk($tagsArray, 'trimStringTag');
        
        $data = array();

        foreach ($tagsArray as $value) {
            $id = $tags->getByName($value);
            if ($id) {
                $data[] = $id;
            } else {
                $newId = $tags->createNewTag($value);
                $data[] = $newId;
            }
        }

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
    
    public function getArrayTags($topicId)
    {
        $row = $this->fetchAll('post_id = ' . $topicId);
        
        return $row->toArray();
    }

    public function getStringTags($topicId)
    {
        $tags = new Application_Model_DbTable_Tags();
        
        $row = $this->fetchAll('post_id = ' . $topicId)->toArray();

        $temp = array();
        foreach ($row as $value) {
            $temp[] = $tags->getById($value['tag_id'])->name;
        }

        $stringTags = implode(', ', $temp);

        return $stringTags;
    }
    
}
