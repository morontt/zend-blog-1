<?php

class Application_Model_MailClass
{
    public function forgotPasswordMail($login, $url)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $host = $request->getScheme() . '://' . $request->getHttpHost();
        $mailAddress = 'support@' . $request->getHttpHost();

$bodyText = <<<TEXT
Здравствуйте. На ваш адрес отправлена ссылка для восстановления пароля на сайте $host
Если письмо попало к вам случайно, то не предпринимайте ничего.

Ссылка для восстановления пароля - $url
TEXT;
   
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($bodyText);
        $mail->setFrom($mailAddress, $mailAddress);
        $mail->addTo($login, $login);
        $mail->setSubject('Восстановление пароля');
        $mail->send();

        return 'test';
    }

//    public function RegistrationMail($login, $password)
//    {
//
//$bodyText = <<<REG
//Здравствуйте. На ваш адрес отправлена ссылка для восстановления пароля на сайте $host
//Если письмо попало к вам случайно, то не предпринимайте ничего.
//
//Ссылка для восстановления пароля - $url
//REG;
//
//        $mail = new Zend_Mail('UTF-8');
//        $mail->setBodyText($bodyText);
//        $mail->setFrom('support@' . $host, 'support@' . $host);
//        $mail->addTo($login, $login);
//        $mail->setSubject($host . ' - Forgot Password');
//        $mail->send();
//
//        return 'test';
//    }
}
