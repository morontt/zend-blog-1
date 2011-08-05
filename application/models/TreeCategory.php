<?php

class Application_Model_TreeCategory
{
    public $child;

    public function __construct()
    {
        $category = new Application_Model_DbTable_Category();

        $data = $category->fetchAll();

        foreach ($data as $key => $value)
        {
            if (!is_null($value->parent_id))
                $childArray[$value->parent_id][] = $value->category_id;
        }

        $this->child = $childArray;

    }

    public function allChild($id)
    {
        $childArray = $this->child;

        if (isset($childArray[$id])) :
            $result = $childArray[$id];
            foreach ($result as $key => $value)
            {
                $result = array_merge($result, (array)$this->allChild($value));
            }
        else :
            $result = NULL;
        endif;

        return $result;
    }
}
