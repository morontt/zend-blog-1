<?php

class Application_Model_SitemapClass
{
    public static function createSitemap()
    {
        $result = false;
        
        $topic = new Application_Model_DbTable_Topics();
        $arrayTopic = $topic->getSitemapTopic();
        
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        foreach ($arrayTopic as $value) {
            $url = $baseUrl . $router->assemble(array('id' => $value['post_id']), 'topic', false, true);
            
            //time format
            list($year, $month, $day, $hour, $min, $sec) = sscanf($value['last_update'], "%d-%d-%d %d:%d:%d");
            $timestamp = mktime($hour, $min, $sec, $month, $day, $year);
            $time = date('c', $timestamp);
            
            $xml .= '<url>' . PHP_EOL;
            $xml .= '  <loc>' . $url . '</loc>' . PHP_EOL;
            $xml .= '  <lastmod>' . $time . '</lastmod>' . PHP_EOL;
            $xml .= '  <changefreq>monthly</changefreq>' . PHP_EOL;
            $xml .= '  <priority>0.80</priority>' . PHP_EOL;
            $xml .= '</url>' . PHP_EOL;
        }
        
        //static page
        $staticPage = <<<STATICPAGE
<url>
  <loc>$baseUrl/login</loc>
  <lastmod>2011-08-01T21:25:47+00:00</lastmod>
  <changefreq>yearly</changefreq>
  <priority>0.50</priority>
</url>
<url>
  <loc>$baseUrl/registration</loc>
  <lastmod>2011-08-01T21:25:47+00:00</lastmod>
  <changefreq>yearly</changefreq>
  <priority>0.50</priority>
</url>
<url>
  <loc>$baseUrl/forgot</loc>
  <lastmod>2011-08-01T21:25:47+00:00</lastmod>
  <changefreq>yearly</changefreq>
  <priority>0.50</priority>
</url>
STATICPAGE;
        
        $xml .= $staticPage . PHP_EOL;
        
        $xml .= '</urlset>' . PHP_EOL;
        
        $fh = fopen('sitemap.xml', 'w');
        fwrite($fh, $xml);
        fclose($fh);
        
        return $result;
    }
}
