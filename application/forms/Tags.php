<?php

class Application_Form_Tags extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $name = new Zend_Form_Element_Text('name');
        
        $submit = new Zend_Form_Element_Submit('submit');
        
        $name->setLabel('Тег:')
             ->setRequired(true)
             ->addFilter('StripTags')
             ->addFilter('StringTrim')
             ->addValidator('NotEmpty');
        
        $this->addElements(array($name, $submit));
    }

}

