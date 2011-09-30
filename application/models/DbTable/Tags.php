<?php

class Application_Model_DbTable_Tags extends Zend_Db_Table_Abstract
{
    protected $_name = 'tags';
	
	public function getNameTags()
	{
	    $data = $this->fetchAll();
		
		foreach($data as $value) {
			$key = $value->tag_id;
			$arrayName[$key] = array('name' => $value->name,
                                    'count' => $value->count);
		}
		
		return $arrayName;
	}

    public function getById($id)
    {
        $row = $this->fetchRow('tag_id = ' . $id);
        
        return $row;
    }

    public function getByName($name)
    {
        $row = $this->fetchRow($this->select()->where('name = ?', $name));

        if ($row) {
            $result = $row->tag_id;
        } else {
            $result = FALSE;
        }

        return $result;
    }
    
    public function getAllTags()
	{
		$select = $this->select()->order('name ASC');
        
        $paginator = Zend_Paginator::factory($select);
        
		return $paginator;
	}
    
    public function createNewTag($name)
    {
        $data = array('name'  => $name,
                      'count' => 0);

        $id = $this->insert($data);

        return $id;
    }
    
    public function editTag($id, $name)
    {
        $data = array('name' => $name);
        
        $this->update($data, 'tag_id = ' . (int)$id);
    }
    
    public function deleteTag($id)
    {
        $row = $this->fetchRow('tag_id = ' . $id);

        if ($row->count == 0) {
            $del = $this->delete('tag_id = ' . $id);
            $result = TRUE;
        } else {
            $result = FALSE;
        }
        
        return $result;
    }

    public function setCountTags($dataArray, $delta)
    {
        foreach($dataArray as $key => $value) {
            $count = $this->fetchRow('tag_id = '. $value)->count;
            $count += $delta;
            $data = array('count' => $count);
            $this->update($data, 'tag_id = ' . $value);
        }
        $this->clearCacheTag(FALSE);
    }
    
}
