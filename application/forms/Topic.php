<?php

class Application_Form_Topic extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $title = new Zend_Form_Element_Text('title');
        $textarea = new Zend_Form_Element_Textarea('text_post');
        $categoryId = new Zend_Form_Element_Select('category_id');
//        $tagSelect = new Zend_Form_Element_Multiselect('tagSelect');
        $tagString = new Zend_Form_Element_Text('tags');
//        $codeSyntax = new Zend_Form_Element_Checkbox('syntax');
        $hide = new Zend_Form_Element_Select('hide');
            
        $submit = new Zend_Form_Element_Submit('submit');
        
        $title->setLabel('Заголовок:')
             //->setRequired(true)
             ->setAttrib('size', 96)
             ->addFilter('StripTags')
             ->addFilter('StringTrim')
             //->addValidator('NotEmpty')
             ->addValidator('stringLength', false, array(1,255));

        $textarea->setLabel('Текст записи:')
                 ->setRequired(true)
                 ->setAttribs(array('cols' => 76,
                                    'rows' => 20))
                 //->addFilter('StripTags')
                 ->addFilter('StringTrim');

        $tagString->setLabel('Теги:')
                  ->setAttrib('size', 96)
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator('stringLength', false, array(1,1000));
        
        $category = new Application_Model_DbTable_Category();
        $nameCategory = $category->getNameCategory();
        $categoryId->setLabel('Категория:')
                   //->setAttrib('size', 16)
                   ->addMultiOptions($nameCategory);
        
        
//        $tags = new Application_Model_DbTable_Tags();
//        $nameTags = $tags->getNameTags();
//        foreach($nameTags as $key => $value)
//        {
//            $dataTags[$key] = $value['name'];
//        }
//        $tagSelect->setLabel('Теги:')
//                  ->setAttrib('size', 8)
//                  ->addMultiOptions($dataTags);
        
//        $codeSyntax->setLabel('Подсветка кода:');
//        
        $hide->setLabel('Скрытие:');
        $hide->addMultiOption('0', 'Видно всем')
             ->addMultiOption('1', 'Скрыто');
        
//        $this->addElements(array($title, $textarea, $categoryId,
//                                 $tagSelect, $hide, $submit));

        $this->addElements(array($title, $textarea, $tagString, $categoryId,
                                 $hide, $submit));
    }


}

