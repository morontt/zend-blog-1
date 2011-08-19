<?php

class Application_Model_HtmlFilterClass
{
    public function htmlFilter($text)
    {
        $allowTags = array('a', 'b', 'i', 'u',
                           's', 'p', 'img', 'br',
                           'table', 'tr', 'td', 'th',
                           'pre', 'center', 'ul',
                           'ol', 'li', 'dl',
                           'dt', 'dd', 'div', 'span');
        $allowAttribs = array('src', 'href', 'width',
                              'height', 'title', 'target',
                              'alt', 'align', 'border',
                              'style', 'class');
        $filter = new Zend_Filter_StripTags(array('allowTags' => $allowTags,
                                                  'allowAttribs' => $allowAttribs));
        $text = $filter->filter($text);
        
        return $text;
    }
}