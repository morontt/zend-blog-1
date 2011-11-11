<?php

class Application_Model_DbTable_TopicsCount extends Zend_Db_Table_Abstract
{
    protected $_name = 'blog_posts_counts';
    
    public function setViewCount($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('post_id = ' . $id);
        
        $countViews = $row->views;
        
        $data = array('views' => $countViews + 1);
        $upd = $this->update($data, 'post_id = ' . $id);
        
		return $row;
    }
    
    public function setCommentCount($id, $delta)
    {
        $row = $this->fetchRow('post_id = '. $id);
        $count = $row->comments;

        $count += $delta;
        $data = array('comments' => $count);
        $this->update($data, 'post_id = ' . $id);
    }
    
    public function addNewRow($id)
    {
        $this->insert(array('post_id' => $id));
    }
    
    public function getCounts($id)
    {
        $row = $this->fetchRow('post_id = ' . $id);
        
        return $row->toArray();
    }
    
    public function getArrayCounts($arrayId)
    {
        $select = $this->select()
                       ->from($this->_name, array('post_id',
                                                  'comments',
                                                  'views'));
        foreach ($arrayId as $id) {
            $select->orWhere('post_id = ?', $id);
        }
        
        $rez = $this->fetchAll($select)->toArray();
        $result = array();
        foreach ($rez as $item) {
            $result[$item['post_id']] = array(
                'comments' => $item['comments'],
                'views'    => $item['views']
            );
        }
        
        return $result;
    }
}