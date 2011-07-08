<?php

class Application_Form_Category extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $name = new Zend_Form_Element_Text('name');
        $parent = new Zend_Form_Element_Select('parent_id');
        $oldparent = new Zend_Form_Element_Hidden('old_parent');
        $submit = new Zend_Form_Element_Submit('submit');
        
        $name->setLabel('Категория:')
             ->setRequired(true)
             ->addFilter('StripTags')
             ->addFilter('StringTrim')
             ->addValidator('NotEmpty');
             
        $category = new Application_Model_DbTable_Category();
        $nameCategory = $category->getNameCategory();
        $parent->setLabel('Родительская категория:')
               ->addMultiOption(0, '...')
               ->addMultiOptions($nameCategory);
        
        $this->addElements(array($name, $parent, $submit, $oldparent));
    }

}

