<?php

class Application_Model_MailClass
{
    protected $_host;
    protected $_mailAddress;
    protected $_sender;

    function __construct()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->_host = $request->getScheme() . '://' . $request->getHttpHost();
        $this->_mailAddress = 'support@' . $request->getHttpHost();
        $this->_sender = $request->getHttpHost();
    }

    public function forgotPasswordMail($login, $url)
    {
        $host = $this->_host;
        $mailAddress = $this->_mailAddress;
        $sender = $this->_sender;

$bodyText = <<<TEXT
Здравствуйте. На ваш адрес отправлена ссылка для восстановления пароля на сайте $host
Если письмо попало к вам случайно, то не предпринимайте ничего.

Ссылка для восстановления пароля - $url
TEXT;
   
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($bodyText);
        $mail->setFrom($mailAddress, $sender);
        $mail->addTo($login, $login);
        $mail->setSubject('Восстановление пароля');
        $mail->send();

        return 'test';
    }

    public function registrationMail($login, $password)
    {
        $host = $this->_host;
        $mailAddress = $this->_mailAddress;
        $sender = $this->_sender;

$bodyText = <<<REG
Добро пожаловать на сайт $host

Ваши регистрационные данные для входа.
Логин: $login
Пароль: $password

Пожалуйста, запомните данную информацию для будущих визитов на наш web-сайт.
REG;

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($bodyText);
        $mail->setFrom($mailAddress, $sender);
        $mail->addTo($login, $login);
        $mail->setSubject('Регистрация на сайте');
        $mail->send();

        return 'test';
    }
}
