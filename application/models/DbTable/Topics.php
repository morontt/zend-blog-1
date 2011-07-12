<?php

class Application_Model_DbTable_Topics extends Zend_Db_Table_Abstract
{
    protected $_name = 'blog_posts';
    
    
    public function getTopicById($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('post_id = ' . $id);
        
		return $row;
    }
	
	public function getTopicByCategoryId($id, $page, $showHidden)
    {	
		Zend_Loader::loadClass('My_TreeCategory');
        $tree = new My_TreeCategory;
        
        $select = $this->select();
        
        if (!$showHidden) {
            $select = $select->where('hide <> 1');
        }
            
        if ($id) {
            $select = $select->where('category_id = ?', $id);
        }
        
        $childCategory = $tree->allChild($id);
        if (!empty($childCategory)) {
            $childArray = $childCategory;
            foreach ($childArray as $key => $value) {
                $select = $select->orWhere('category_id = ?', $value);
            }
        }
        
        $select = $select->order('time_created DESC');
		
		$paginator = Zend_Paginator::factory($select);
		
		$config = new Zend_Config_Ini('../application/configs/application.ini','production');
		$itemPerPage = $config->items->per->page;
		$paginator->setItemCountPerPage($itemPerPage);
		
		$paginator->SetCurrentPageNumber($page);
		
		return $paginator;
    }
	
	public function getTopicByUserId($id, $page, $showHidden)
    {	
		$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $user = $auth->getIdentity()->user_id;
        } else {
            $user = null;
        }

        $show = $showHidden || ($user == $id);
        
        if ($show) {
            $select = $this->select()->where('user_id = ?', $id)
                                     ->order('time_created DESC');
        } else {
            $select = $this->select()->where('user_id = ?', $id)
                                     ->where('hide <> 1')
                                     ->order('time_created DESC');
        }
        
		$paginator = Zend_Paginator::factory($select);
		
		$config = new Zend_Config_Ini('../application/configs/application.ini','production');
		$itemPerPage = $config->items->per->page;
		$paginator->setItemCountPerPage($itemPerPage);
		
		$paginator->SetCurrentPageNumber($page);
        
		return $paginator;
    }
    
    public function getTopicByTagId($id, $page, $showHidden)
    {
        $config = new Zend_Config_Ini('../application/configs/application.ini','production');
        
        //$db = Zend_Db::factory($config->resources->db);
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        
        $select = $db->select()->from(array('topics' => 'blog_posts'))
                               ->join(array('relation' => 'relation_topictag'),
                                      'topics.post_id = relation.post_id');
        
        if (!$showHidden) {
            $select = $select->where('topics.hide <> 1');
        }
        
        $select->where('relation.tag_id = ?', $id)
               ->order('time_created DESC');
		
		$paginator = Zend_Paginator::factory($select);
		
		$itemPerPage = $config->items->per->page;
		$paginator->setItemCountPerPage($itemPerPage);
		
		$paginator->SetCurrentPageNumber($page);
		
		return $paginator;
    }
    
    public function getTopicForControl($page)
    {	
		$auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        if ($identity->user_type == 'admin') {
            $select = $this->select()->order('time_created DESC');
        } else {
            $select = $this->select()->where('user_id = ?', $identity->user_id)
                                     ->order('time_created DESC');
        }
        
		$paginator = Zend_Paginator::factory($select);
		
		$config = new Zend_Config_Ini('../application/configs/application.ini','production');
		$itemPerPage = $config->itemsControl->per->page;
		$paginator->setItemCountPerPage($itemPerPage);
		
		$paginator->SetCurrentPageNumber($page);
        
		return $paginator;
    }
    
    public function htmlFilter($text)
    {
        $text = str_replace('<!-- cut -->', '30fefd0cd99b5', $text);
        
        $allowTags = array('a', 'b', 'i', 'u',
                           's', 'p', 'img', 'br',
                           'table', 'tr', 'td', 'th',
                           'pre', 'center', 'ul',
                           'ol', 'li', 'dl',
                           'dt', 'dd');
        $allowAttribs = array('src', 'href', 'width',
                              'height', 'title', 'target',
                              'alt', 'align', 'border');
        $filter = new Zend_Filter_StripTags(array('allowTags' => $allowTags,
                                                  'allowAttribs' => $allowAttribs));
        $text = $filter->filter($text);
        
        $text = str_replace('30fefd0cd99b5', '<!-- cut -->', $text);
        
        return $text;
    }
    
    public function createNewTopic($formData)
    {
        $auth = Zend_Auth::getInstance();
        $userId = $auth->getIdentity()->user_id;
        
        $text = $this->htmlFilter($formData['text_post']);
        
        $data = array('category_id'  => $formData['category_id'],
                      'hide'         => $formData['hide'],
                      'title'        => $formData['title'],
                      'text_post'    => $text,
                      'user_id'      => $userId,
                      'time_created' => date('Y-m-d H:i:s'));
        
        $topicId = $this->insert($data);
        
        if (!empty($formData['tagSelect'])) {
            $relation = new Application_Model_DbTable_RelationTopicTag();
            $relation->addRelation($formData['tagSelect'], $topicId);
        }
        
        $category = new Application_Model_DbTable_Category;
        $category->setCount($formData['category_id'], 1);
        
        return $topicId;
    }
    
    public function editTopic($formData, $id)
    {   
        $text = $this->htmlFilter($formData['text_post']);
        
        $data = array('category_id' => $formData['category_id'],
                      'hide'        => $formData['hide'],
                      'title'       => $formData['title'],
                      'text_post'   => $text);
        
        $relation = new Application_Model_DbTable_RelationTopicTag();
        $relation->deleteRelation($id);
                
        if (!empty($formData['tagSelect'])) {
            $relation->addRelation($formData['tagSelect'], $id);
        }
        
        $row = $this->fetchRow('post_id = ' . $id);
        $categoryId = $row->category_id;
        
        $category = new Application_Model_DbTable_Category;
        $category->setCount($categoryId, -1);
        $category->setCount($formData['category_id'], 1);
        
        return $this->update($data, 'post_id = ' . $id);
    }
    
    public function deleteTopic($id)
    {
        $row = $this->fetchRow('post_id = ' . $id);
        $categoryId = $row->category_id;
        
        $category = new Application_Model_DbTable_Category;
        $category->setCount($categoryId, -1);
        
        $del = $this->delete('post_id = ' . $id);
            
        $relation = new Application_Model_DbTable_RelationTopicTag();
        $relation->deleteRelation($id);
    }
    
}
