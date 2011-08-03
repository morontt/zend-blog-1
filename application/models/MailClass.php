<?php

class Application_Model_MailClass
{
    public function forgotPasswordMail($host, $url)
    {

$bodyText = <<<TEXT
Здравствуйте. На ваш адрес отправлена ссылка для восстановления пароля на сайте http://$host
Если письмо попало к вам случайно, то не предпринимайте ничего.

Ссылка для восстановления пароля - $url
TEXT;
   
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($bodyText);
        $mail->setFrom('support@' . $host, 'support@' . $host);
        $mail->addTo('morontt@list.ru', 'morontt@list.ru');
        $mail->setSubject($host . ' - Forgot Password');
        $mail->send();

        return 'test';
    }
}
