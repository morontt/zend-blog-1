<?php

class Application_Model_DbTable_Comments extends Zend_Db_Table_Abstract
{
    protected $_name = 'comments';

    public function htmlFilter($text)
    {
        $allowTags = array('a', 'b', 'i', 'u',
                           's', 'p', 'img', 'br',
                           'table', 'tr', 'td', 'th',
                           'pre', 'center', 'ul',
                           'ol', 'li', 'dl',
                           'dt', 'dd', 'div');
        $allowAttribs = array('src', 'href', 'width',
                              'height', 'title', 'target',
                              'alt', 'align', 'border', 'style');
        $filter = new Zend_Filter_StripTags(array('allowTags'    => $allowTags,
                                                  'allowAttribs' => $allowAttribs));
        $text = $filter->filter($text);

        return $text;
    }

    public function getByTopicId($id)
    {
        $select = $this->select()
                       ->where('post_id = ?', $id)
                       ->order('time_created ASC');

        $paginator = Zend_Paginator::factory($select);
        
		return $paginator;
    }

    public function saveComment($formData)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userId = $auth->getIdentity()->user_id;
        } else {
            $userId = NULL;
        }

        $text = $this->htmlFilter($formData['comment_text']);

        if (empty($formData['name'])) {
            $formData['name'] = NULL;
        }
        if (empty($formData['mail'])) {
            $formData['mail'] = NULL;
        }
        if (empty($formData['website'])) {
            $formData['website'] = NULL;
        }

        $data = array('name'         => $formData['name'],
                      'post_id'      => (int)$formData['topicId'],
                      'mail'         => $formData['mail'],
                      'website'      => $formData['website'] ,
                      'text'         => $text,
                      'user_id'      => $userId,
                      'ip_addr'      => $_SERVER['REMOTE_ADDR'],
                      'time_created' => date('Y-m-d H:i:s'));

        $commentId = $this->insert($data);

        if ($commentId) {
            $topic = new Application_Model_DbTable_Topics();
            $topic->setCount((int)$formData['topicId'], 1);
        }

        return $commentId;
    }
}