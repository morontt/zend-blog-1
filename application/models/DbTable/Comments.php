<?php

class Application_Model_DbTable_Comments extends Zend_Db_Table_Abstract
{
    protected $_name = 'comments';

    public function getByTopicId($id)
    {
        $select = $this->select()
                       ->where('post_id = ?', $id)
                       ->order('time_created ASC');

        $paginator = Zend_Paginator::factory($select);
        
		return $paginator;
    }

    public function saveComment($formData, $mailSend)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userId = $auth->getIdentity()->user_id;
        } else {
            $userId = NULL;
        }

        $filter = new Application_Model_HtmlFilterClass();
        
        $text = $filter->htmlFilter($formData['comment_text']);

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
        
        if ($mailSend) {
            $mail = new Application_Model_MailClass();
            $mail->commentMail($formData['topicId'], $userId);
        }

        return $commentId;
    }
}