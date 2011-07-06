<?php

class Zend_View_Helper_ListCategory extends Zend_View_Helper_Abstract
{
    public function listCategory()
	{
	    $result = '<div class="title">Категории:</div><p>';
		
		foreach ($this->view->nameCategory as $key => $value)
		{
		    $result .= '<a href="'.$this->view->url(array('module' => 'default',
                                                      'controller' => 'index',
                                                          'action' => 'index',
                                                            'page' => 1,
                                                              'id' => $key), 'category') . '">' . $value . '</a><br />';
        }
		$result .= '</p>';
		
		return $result;
	}
}
