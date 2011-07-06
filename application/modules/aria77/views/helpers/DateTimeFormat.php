<?php

class Zend_View_Helper_DateTimeFormat extends Zend_View_Helper_Abstract
{
    public function dateTimeFormat($dateTime)
	{
	    if(isset($dateTime)) :
            list($year, $month, $day, $hour, $min, $sec) = sscanf($dateTime, "%d-%d-%d %d:%d:%d");
            $timestamp = mktime($hour, $min, $sec, $month, $day, $year);
            $newDateTime = date('d M Y, H:i', $timestamp);
        else :
            $newDateTime = 'N/A';
        endif;
		
		return $newDateTime;
	}
}
