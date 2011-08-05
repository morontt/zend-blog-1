<?php

class Zend_View_Helper_ListCategory extends Zend_View_Helper_Abstract
{
    protected $_arrayCategory;
    protected $_child;

    protected function subCategory($arrayChild, $i)
    {
        $subResult = '';

        foreach ($arrayChild as $value) {
            foreach ($this->_arrayCategory as $value2) {
                if ($value2['id'] == $value) {
                    $subLink = $value2['link'];
                    $subResult .= '<li style="padding-left: ' . $i*10 . 'px;">' . $subLink . '</li>' . PHP_EOL;
                    if (isset($this->_child[$value2['id']])) {
                        $subResult .= $this->subCategory($this->_child[$value2['id']], $i + 1);
                    }
                }
            }
        }

        return $subResult;
    }

    public function listCategory()
    {
	    $tree = new Application_Model_TreeCategory;
        $child = $tree->child;
        $this->_child = $child;
        
        $arrayLinks = array();

        foreach ($this->view->nameCategory as $key => $value) {
            $link = '<a href="' . $this->view->url(array('id' => $key), 'category')
                                . '">' . $value['name'] . '</a>';

            $arrayLinks[] = array('id' => $key,
                                  'parent_id' => $value['parent_id'],
                                  'link' => $link);
        }

        $this->_arrayCategory = $arrayLinks;
        //Zend_Debug::dump($this->_child); die;

        $result = '<ul class="My_Navigation">' . PHP_EOL;

        foreach ($arrayLinks as $value) {
            if ($value['parent_id'] == NULL) {
                $result .= '<li>' . $value['link'] . '</li>' . PHP_EOL;
                if (isset($child[$value['id']])) {
                    $result .= $this->subCategory($child[$value['id']], 1);
                }
            }
        }

        $result .= '</ul>' . PHP_EOL;
		
        return $result;
    }
}
