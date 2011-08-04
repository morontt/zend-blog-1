<?php

class Zend_View_Helper_ListCategory extends Zend_View_Helper_Abstract
{
    public function listCategory()
    {
	    $arrayLinks = array();

        foreach ($this->view->nameCategory as $key => $value) {
            $link = '<a href="' . $this->view->url(array('id' => $key), 'category')
                                . '">' . $value['name'] . '</a>';

            $arrayLinks[] = array('id' => $key,
                                  'parent_id' => $value['parent_id'],
                                  'link' => $link);
        }

        //Zend_Debug::dump($arrayLinks); die;

        $result = '<ul class="My_Navigation">' . PHP_EOL;

        foreach ($arrayLinks as $value) {
            $result .= '<li>' . $value['link'] . '</li>' . PHP_EOL;
        }

        $result .= '</ul>' . PHP_EOL;
		
        return $result;
    }
}
