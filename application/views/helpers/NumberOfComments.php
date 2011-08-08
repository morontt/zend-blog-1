<?php

class Zend_View_Helper_NumberOfComments extends Zend_View_Helper_Abstract
{
    public function numberOfComments($number)
	{
	    $number = (int)$number;

        if ($number == 0 || $number > 4) {
            $result = $number . ' комментариев';
        }
        if ($number == 1) {
            $result = $number . ' комментарий';
        }
        if ($number >= 2 && $number <= 4) {
            $result = $number . ' комментария';
        }

		return $result;
	}
}
