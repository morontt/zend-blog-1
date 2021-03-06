<?php

class Application_Form_CommentForm extends Zend_Form
{

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userReg = TRUE;
        } else {
            $userReg = FALSE;
        }

        $this->setAction('/index/addcomment');
        $this->setMethod('post');
        
        $this->setAttrib('id', 'CommentForm');

        if (!$userReg) {
            $name = new Zend_Form_Element_Text('name');
            $name->setLabel('Имя:')
                 ->setRequired(true)
                 ->setAttrib('placeholder', 'nickname')
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('NotEmpty');

            $this->addElement($name);

            $this->addElement('text', 'mail', array(
                'label'      => 'E-mail:',
                'validators' => array('EmailAddress'),
                'attribs'    => array('placeholder' => 'mail@example.org'),
                'filters'    => array('StripTags', 'StringTrim')));

            $website = new Zend_Form_Element_Text('website');
            $website->setLabel('Website:')
                    ->setRequired(false)
                    ->setAttrib('placeholder', 'http://example.org')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->addValidator(new Application_Model_UriValidate());
            $this->addElement($website);
        }

        $textarea = new Zend_Form_Element_Textarea('comment_text');
        $textarea->setLabel('Текст комментария:')
                 ->setRequired(true)
                 ->setAttribs(array('cols' => 66,
                                    'rows' => 10))
                 ->addFilter('StringTrim');
        $this->addElement($textarea);

        $topicId = new Zend_Form_Element_Hidden('topicId');
        $this->addElement($topicId);

        $captcha = new Zend_Form_Element_Captcha('captcha',array(
            'label' => 'Подтвердите, что вы не робот:',
            'captcha' => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 1200)
            ));

        if (!$userReg) {
            $this->addElement($captcha);
        }

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
             'label' => 'Добавить комментарий'));
    }

}
