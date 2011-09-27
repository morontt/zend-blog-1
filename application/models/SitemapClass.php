<?php

class Application_Model_SitemapClass
{
    public static function createSitemap()
    {
        $result = false;
        
        $topic = new Application_Model_DbTable_Topics();
        $arrayTopic = $topic->getSitemapTopic();
        
        Zend_Debug::dump($arrayTopic);
        
        return $result;
    }
}
