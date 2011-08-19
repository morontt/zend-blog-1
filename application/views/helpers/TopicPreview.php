<?php

class Zend_View_Helper_TopicPreview extends Zend_View_Helper_Abstract
{
    public function LinksView($text)
    {
        $text = preg_replace("#(^|[\s])((https?|ftp)://\S+[^\s.,>)\];'\"!?])#",'\\1<a href="\\2">\\2</a>',$text);
        
        return $text;
    }

    public function TopicPreview($topic, $topicId, $cut)
	{
		$topic = $this->LinksView($topic);

        if ($cut) {
            $preview = explode('<!-- cut -->',$topic);
            $newTopic = $preview[0];
        } else {
            $newTopic = $topic;
        }
		
		if (isset($preview[1]))
		{
		    $addUrl = '(<a href="' . $this->view->url(array('id' => $topicId),
                                                        'topic') . '">Читать далее...</a>)';
		    $newTopic .= '<br />';
			$newTopic .= $addUrl;
		}
        
        //$newTopic = nl2br($newTopic);
        
		return $newTopic;
	}
}
