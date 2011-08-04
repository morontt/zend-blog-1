<?php

class Zend_View_Helper_TopicPreview extends Zend_View_Helper_Abstract
{
    public function LinksView($text)
    {
        $text = preg_replace("#(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$text);
        
        return $text;
    }

    public function TopicPreview($topic, $topicId)
	{
		//$topic = $this->LinksView($topic);

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
