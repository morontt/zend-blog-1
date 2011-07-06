<?php

class Application_Form_UserType extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $type = new Zend_Form_Element_Select('user_type');
        $submit = new Zend_Form_Element_Submit('submit');

        $type->setLabel('Тип пользователя:')
             ->addMultiOptions(array('admin' => 'admin',
                                    'member' => 'member',
                                     'guest' => 'guest'));
                                
        $submit->setLabel('Отправить');

        $this->addElements(array($type, $submit));
    }

}
