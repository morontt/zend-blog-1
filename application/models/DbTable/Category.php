<?php

class Application_Model_DbTable_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'category';
	
	public function getNameCategory()
	{
	    $data = $this->fetchAll();
		
		foreach($data as $value_cat) {
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
        
		foreach($data as $value_cat) {
			$key = $value_cat['category_id'];
			$arrayName[$key] = array('name'      => $value_cat['name'],
                                     'parent_id' => $value_cat['parent_id']);
		}
		
		return $arrayName;
	}

    public function getById($id)
    {	
		$row = $this->fetchRow('category_id = ' . $id);
        
        return $row;
    }
	
    public function getAllCategory()
	{
		$select = $this->select()->order('name ASC');
        
        $paginator = Zend_Paginator::factory($select);
		
		return $paginator;
	}
    
    public function createNewCategory($name, $parent)
    {
        if (!$parent) {
            unset($parent);
        }
        
        $data = array(
               'name' => $name,
               'count' => 0,
               'parent_id' => $parent);
        $this->insert($data);
    }
    
    public function editCategory($id, $name, $parent, $oldparent)
    {
        if ($id == $parent) {
            $parent = 0;
        }
        
        if (!$parent) {
            unset($parent);
        }
        
        $data = array(
               'name'      => $name,
               'parent_id' => $parent);

        $this->update($data, 'category_id = ' . $id);

        if ($parent != $oldparent) {
            $delta = $this->fetchRow('category_id = ' . $id)->count;
            $this->setCount($oldparent, - $delta);
            $this->setCount($parent, $delta);
        }
    }
    
    public function deleteCategory($id)
    {
        $row = $this->fetchRow('category_id = ' . $id);

        if (($id == 1)||($row->count != 0)) {
            throw new Exception("Данную категорию удалить нельзя");
        }
        
        if (($id != 1)&&($row->count == 0)) {
            $del = $this->delete('category_id = ' . $id);
        }
    }

    public function setCount($id, $delta)
    {
        $row = $this->fetchRow('category_id = '. $id);
        $count = $row->count;
        $parentId = $row->parent_id;

        $count += $delta;
        $data = array('count' => $count);
        $this->update($data, 'category_id = ' . $id);

        if ($parentId) {
            $this->setCount($parentId, $delta);
        }

    }
    
}
