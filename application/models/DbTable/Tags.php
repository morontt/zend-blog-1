<?php

class Application_Model_DbTable_Tags extends Zend_Db_Table_Abstract
{
    protected $_name = 'tags';
	
	public function getNameTags()
	{
	    $data = $this->fetchAll();
		
		foreach($data as $value)
		{
			$key = $value->tag_id;
			$arrayName[$key] = array('name' => $value->name,
                                    'count' => $value->count);
		}
		
		return $arrayName;
	}

    public function getById($id)
    {
        $row = $this->fetchRow('tag_id = '.$id);
        //if (!$row) {
        //    throw new Exception("Count not find row $id");
        //}
        return $row;
    }
    
    public function getAllTags($page)
	{
		$select = $this->select()->order('name ASC');
        
        $paginator = Zend_Paginator::factory($select);
		
		$config = new Zend_Config_Ini('../application/configs/application.ini','production');
		$itemPerPage = $config->tags->per->page;
		$paginator->setItemCountPerPage($itemPerPage);
		
		$paginator->SetCurrentPageNumber($page);
        
		return $paginator;
	}
    
    public function createNewTag($name)
    {
        $data = array(
               'name' => $name,
              'count' => 0,
        );
        $this->insert($data);
    }
    
    public function editTag($id, $name)
    {
        $data = array(
               'name' => $name,
        );
        $this->update($data, 'tag_id = ' . (int)$id);
    }
    
    public function deleteTag($id)
    {
        $row = $this->fetchRow('tag_id = ' . $id);
        
        if ($row->count == 0)
        {
            $del = $this->delete('tag_id = ' . $id);
            //if (!$del)
            //{
            //    throw new Exception("Count not find row $id");
            //}
        }
    }

    public function setCountTags($dataArray, $delta)
    {
        foreach($dataArray as $key => $value)
        {
            $count = $this->fetchRow('tag_id = '. $value)->count;
            $count += $delta;
            $data = array('count' => $count);
            $this->update($data, 'tag_id = ' . $value);
        }
    }
}
