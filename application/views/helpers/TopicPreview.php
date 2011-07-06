<?php

class Zend_View_Helper_TopicPreview extends Zend_View_Helper_Abstract
{
    public function TopicPreview($topic, $topicId)
	{
		$preview = explode('<!-- cut -->',$topic);
		$newTopic = $preview[0];
		
		if (isset($preview[1]))
		{
		    $addUrl = '(<a href="' . $this->view->url(array('module' => 'default',
                                                        'controller' => 'index',
                                                            'action' => 'topic',
                                                                'id' => $topicId), 'topic') . '">Читать далее...</a>)';
		    $newTopic .= '<br />';
			$newTopic .= $addUrl;
		}
		return $newTopic;
	}
}
