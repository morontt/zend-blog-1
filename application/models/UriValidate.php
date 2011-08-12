<?php
class Application_Model_UriValidate extends Zend_Validate_Abstract
{
    const MSG_URI = 'msgUri';

    protected $_messageTemplates = array(
        self::MSG_URI => 'Invalid URL',
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        //Validate the URI
        $valid = Zend_Uri::check($value);

        //Return validation result TRUE|FALSE
        if ($valid) {
            $result = TRUE;
        } else {
            $this->_error(self::MSG_URI);
            $result = FALSE;
        }
        
        return $result;
    }
}