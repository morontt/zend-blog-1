<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Настройка базовой конфигурации</title>	<link href="/zf-tutorial/public/css/main_s.css" media="screen" rel="stylesheet" type="text/css" /></head>
<body>
	<div id="container">
		<div id="header">
			<div id="sitename">
			<a href="/zf-tutorial/public">Zend.blog</a>
			</div>
		</div>

<div id="strlist">
    <div class="strecord">
    <div class="theme">Инсталляция блога</div>
        <?php
        if (isset($_POST['send']))
        {
            $blogTitle = $_POST['blogTitle'];
            $blogAdmin = $_POST['blogAdmin'];
            $blogNick = $_POST['blogNick'];
            $blogPass = $_POST['blogPass'];
            $blogVerifyPass = $_POST['blogVerifyPass'];
            
            $dbHost = $_POST['dbHost'];
            $dbName = $_POST['dbName'];
            $dbUser = $_POST['dbUser'];
            $dbPass = $_POST['dbPass'];
            
            $condition = TRUE;
            if (empty($blogAdmin)||empty($blogPass)||empty($blogVerifyPass)||empty($blogNick))
            {
                $condition = FALSE;
                $errorMessage = 'Заполенены не все поля';
            }
            if ($blogPass != $blogVerifyPass)
            {
                $condition = FALSE;
                $errorMessage = 'Пароли не совпадают';
            }
            
            $email = filter_var($blogAdmin, FILTER_VALIDATE_EMAIL);
            if (!$email)
            {
                $condition = FALSE;
                $errorMessage = 'email введён неправильно';
            }
            
            if ($condition)
            {
                //$dbHost = "zadachni.mysql.ukraine.com.ua";
                //$dbName = "zadachni_db";
                //$dbUser = "zadachni_db";
                //$dbPass = "xxx";
                
                @ $db = mysql_pconnect($dbHost, $dbUser, $dbPass);
                $dbUse = mysql_select_db($dbName);
            
                if (!$db || !$dbUse) :
                    echo '<br>Ошибка соединения с базой данных<br>';
                else :
                    include_once('import_sql.php');
                    
                    /* $temp = mysql_query($createTopic);
                    $temp = mysql_query($createCategory);
                    $temp = mysql_query($createRelation);
                    $temp = mysql_query($createTags);
                    $temp = mysql_query($createUsers); */
                    
                    $dynamicSalt = chr(rand(48, 122));
                    for ($i = 0; $i < 10; $i++)
                        $dynamicSalt .= chr(rand(48, 122));
		
                    $dynamicSalt = md5($dynamicSalt);
                    $password = md5(md5($blogPass) . $dynamicSalt);
                    
                    $query = "INSERT INTO `users2` (`user_id`, `username`, `login`, `password`, `password_salt`";
                    $query .= ", `user_type`, `time_created`) ";
                    //$query .= "VALUES ( '1', '".$blogNick."', '".$blogAdmin."', '".$password;
                    $query .= "VALUES ( NULL, '".$blogNick."', '".$blogAdmin."', '".$password;
                    $query .= "', '".$dynamicSalt."', 'admin', '" . date('Y-m-d H:i:s') ."');";
                    
                    $temp = mysql_query($query);
                    
                    $ini_array = parse_ini_file("../application/configs/application.ini", TRUE);
                    //echo '<pre>';
                    //print_r($ini_array);
                    //echo '</pre>';
                endif;
            }
            else
            {
                echo '<p>' . $errorMessage . '</p>';
            }
        }
        ?>
        <div class="strsigninform">
        <form method="post" action="">
            <fieldset><legend>Настройка соединения с базой данных</legend>
            Хост:<br><input type="text" name="dbHost" value="" required /><br>
            Название базы:<br><input type="text" name="dbName" value="" required /><br>
            Пользователь БД:<br><input type="text" name="dbUser" value="" required /><br>
            Пароль:<br><input type="password" name="dbPass" value="" required /><br>
            </fieldset>
            <fieldset><legend>Базовая настройка блога</legend>
            Заголовок блога:<br><input type="text" name="blogTitle" value="" /><br>
            email администратора:<br><input type="text" name="blogAdmin" value="" required /><br>
            nickname администратора:<br><input type="text" name="blogNick" value="" required /><br>
            Пароль:<br><input type="password" name="blogPass" value="" required /><br>
            Ещё раз пароль:<br><input type="password" name="blogVerifyPass" value="" required /><br>
            <br><input type='submit' name='send' value='Отправить' />
            </fieldset>
        </form>
        </div>
    </div>
</div>
		
		<div id="footer">
		&copy; 2011 <a href="http://www.zadachnik.info/">Zadachnik.info</a>: Footer
		</div>
	</div>
</body>
</html>
