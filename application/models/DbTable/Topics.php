<?php

class Application_Model_DbTable_Topics extends Zend_Db_Table_Abstract
{
    protected $_name = 'blog_posts';
    
    
    public function getTopicById($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('post_id = ' . $id);
        
        $countViews = $row->views;
        
        $data = array('views' => $countViews + 1);
        $upd = $this->update($data, 'post_id = ' . $id);
        
		return $row;
    }

    public function getAllTopic($showHidden)
    {
        $select = $this->select();

        if (!$showHidden) {
            $select = $select->where('hide <> 1');
        }

        $select = $select->order('time_created DESC');

		return Zend_Paginator::factory($select);
    }
    
    public function getSitemapTopic()
    {
        $select = $this->select()
                       ->from($this->_name, array('post_id', 'last_update'))
                       ->where('hide <> 1')
                       ->order('time_created DESC');
        
        $result = $this->fetchAll($select)->toArray();

		return $result;
    }

	public function getTopicByCategoryId($id, $showHidden)
    {	
        $tree = new Application_Model_TreeCategory;
        
        $select = $this->select();
        
        if (!$showHidden) {
            $select = $select->where('hide <> 1');
        }

        $select = $select->where('category_id = ?', $id);
        
        $childCategory = $tree->allChild($id);
        if (!empty($childCategory)) {
            $childArray = $childCategory;
            foreach ($childArray as $key => $value) {
                $select = $select->orWhere('category_id = ?', $value);
            }
        }
        
        $select = $select->order('time_created DESC');
		
		$paginator = Zend_Paginator::factory($select);
		
		return $paginator;
    }
	
	public function getTopicByUserId($id, $showHidden)
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
        
		return $paginator;
    }
    
    public function getTopicByTagId($id, $showHidden)
    {
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
		
		return $paginator;
    }
    
    public function getTopicForControl()
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
		
		return $paginator;
    }
    
    public function getFeedData($feedType)
    {	
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . '/';
        $host = $request->getHttpHost();
        
        $result = array('title'       => $host,
                        'link'        => $baseUrl,
                        'description' => $host . ' - последние записи',
                        'language'    => 'ru-ru',
                        'charset'     => 'utf-8',
                        'generator'   => 'Zend Framework Generator',
                );
        
        $select = $this->select()
                       ->where('hide <> 1')
                       ->order('time_created DESC')
                       ->limit(25);
        
        $topics = $this->fetchAll($select);
        
        $lastDate = '';
        
        $entries = array();
        foreach ($topics as $topic) {
            //time format
            list($year, $month, $day, $hour, $min, $sec) = sscanf($topic->time_created, "%d-%d-%d %d:%d:%d");
            $timestamp = mktime($hour, $min, $sec, $month, $day, $year);
            
            if (empty($lastDate)) $lastDate = $timestamp;
            
            $item = array(
                'title'       => $topic->title,
                'link'        => $baseUrl . 'topic/' . $topic->post_id,
                'description' => $topic->text_post,
                'lastUpdate'  => $timestamp,
                'comments'    => $baseUrl . 'topic/' . $topic->post_id,
                'guid'        => 'topic_' . $topic->post_id
            );
            $entries[] = $item;
        }
        
        $result['entries'] = $entries;
        $result['lastUpdate'] = $lastDate;
		
		return $result;
    }
    
    public function getDistinctUser()
    {
        $data = $this->fetchAll($this->select()
                                     ->distinct()
                                     ->from($this->_name, 'user_id')
                                );
        
        foreach ($data as $value) {
            $result[] = $value['user_id'];
        }
        $result = array_flip($result);
        
        return $result;
    }
    
    public function htmlFilter($text)
    {
        $text = str_replace('<!-- cut -->', '30fefd0cd99b5', $text);
        
        $filter = new Application_Model_HtmlFilterClass();
        
        $text = $filter->htmlFilter($text);
        
        $text = str_replace('30fefd0cd99b5', '<!-- cut -->', $text);
        
        return $text;
    }
    
    public function createNewTopic($formData)
    {
        $auth = Zend_Auth::getInstance();
        $userId = $auth->getIdentity()->user_id;
        
        $text = $this->htmlFilter($formData['text_post']);
        
        $dateTime = date('Y-m-d H:i:s');
        
        $data = array('category_id'  => $formData['category_id'],
                      'hide'         => $formData['hide'],
                      'title'        => $formData['title'],
                      'text_post'    => $text,
                      'user_id'      => $userId,
                      'time_created' => $dateTime,
                      'last_update'  => $dateTime,
                      //'syntax'       => $formData['syntax']
                );
        
        $topicId = $this->insert($data);
        
        if (!empty($formData['tags'])) {
            $relation = new Application_Model_DbTable_RelationTopicTag();
            $relation->addRelation($formData['tags'], $topicId);
        }
        
        $category = new Application_Model_DbTable_Category;
        $category->setCount($formData['category_id'], 1);
        
        $this->clearCacheFeed();
        
        return $topicId;
    }
    
    public function editTopic($formData, $id)
    {   
        $text = $this->htmlFilter($formData['text_post']);
        
        $data = array('category_id' => $formData['category_id'],
                      'hide'        => $formData['hide'],
                      'title'       => $formData['title'],
                      'text_post'   => $text,
                      'last_update' => date('Y-m-d H:i:s'),
                      //'syntax'      => $formData['syntax']
                );
        
        $relation = new Application_Model_DbTable_RelationTopicTag();
        $relation->deleteRelation($id);
                
        if (!empty($formData['tags'])) {
            $relation->addRelation($formData['tags'], $id);
        }
        
        $row = $this->fetchRow('post_id = ' . $id);
        $categoryId = $row->category_id;
        
        $category = new Application_Model_DbTable_Category;
        $category->setCount($categoryId, -1);
        $category->setCount($formData['category_id'], 1);
        
        $this->clearCacheTagByTopic($id);
        $this->clearCacheFeed();
        
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
        
        $this->clearCacheTagByTopic($id);
        $this->clearCacheFeed();
    }

    public function setCount($id, $delta)
    {
        $row = $this->fetchRow('post_id = '. $id);
        $count = $row->count_comments;

        $count += $delta;
        $data = array('count_comments' => $count);
        $this->update($data, 'post_id = ' . $id);
    }
    
    public function clearCacheTagByTopic($id)
    {
        $cache = Zend_Cache::factory('Core', 'File', array(), array('cache_dir' => '../cache/'));
        $cache->remove('tagsByTopic_' . $id);
    }
    
    public function clearCacheFeed()
    {
        $cache = Zend_Cache::factory('Core', 'File', array(), array('cache_dir' => '../cache/'));
        $cache->remove('Feed_rss');
        $cache->remove('Feed_atom');
    }
    
}
