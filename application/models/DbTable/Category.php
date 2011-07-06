<?php

class Application_Model_DbTable_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'category';
	
	public function getNameCategory()
	{
	    $data = $this->fetchAll();
		
		foreach($data as $value_cat)
		{
			$key = $value_cat->category_id;
			$arrayName[$key] = $value_cat->name;
		}
		
		return $arrayName;
	}
    
    public function getNotEmpty()
	{
	    $select = $this->select()->where('count <> 0');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
		foreach($data as $value_cat)
		{
			$key = $value_cat['category_id'];
			$arrayName[$key] = array('name' => $value_cat['name'],
                                     'parent_id' => $value_cat['parent_id']);
		}
		
		return $arrayName;
	}

    public function getById($id)
    {	
		$row = $this->fetchRow('category_id = ' . $id);
        if (!$row)
        {
            throw new Exception("Категория с заданным id = $id не обнаружена");
        }
        
        return $row;
    }
	
    public function getAllCategory($page)
	{
		$select = $this->select()->order('name ASC');
        
        $paginator = Zend_Paginator::factory($select);
		
		$config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
		$itemPerPage = $config->category->per->page;
		$paginator->setItemCountPerPage($itemPerPage);
		
		$paginator->SetCurrentPageNumber($page);
        
		return $paginator;
	}
    
    public function createNewCategory($name, $parent)
    {
        if (!$parent)
            unset($parent);
        
        $data = array(
               'name' => $name,
               'count' => 0,
               'parent_id' => $parent);
        $this->insert($data);
    }
    
    public function editCategory($id, $name, $parent)
    {
        if ($id == $parent)
            $parent = 0;
        
        if (!$parent)
            unset($parent);
        
        $data = array(
               'name' => $name,
          'parent_id' => $parent
        );
        $this->update($data, 'category_id = ' . $id);
    }
    
    public function deleteCategory($id)
    {
        $row = $this->fetchRow('category_id = ' . $id);

        if (($id == 1)||($row->count != 0))
        {
            throw new Exception("Данную категорию удалить нельзя");
        }
        
        if (($id != 1)&&($row->count == 0))
        {
            $del = $this->delete('category_id = ' . $id);
        }
    }
    
    public function countPlus($id)
    {
        $row = $this->fetchRow('category_id = '. $id);
        $count = $row->count;
        $parentId = $row->parent_id;
        
        $count += 1;
        $data = array('count' => $count);
        $this->update($data, 'category_id = ' . $id);
        
        if ($parentId)
        {
            $this->countPlus($parentId);
        }
        
    }
    
    public function countMinus($id)
    {
        $row = $this->fetchRow('category_id = '. $id);
        $count = $row->count;
        $parentId = $row->parent_id;
        
        $count -= 1;
        $data = array('count' => $count);
        $this->update($data, 'category_id = ' . $id);
        
        if ($parentId)
        {
            $this->countMinus($parentId);
        }
        
    }
    
}
