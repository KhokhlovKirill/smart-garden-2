<?php

// Future libraries
function mysql_outputElement($link, $table, $id, $element){
    mysqli_query($link, "SET NAMES 'utf8'");

    $query = "SELECT ".$element." FROM ".$table." WHERE id = ".$id;

    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

    return ($data[0])[$element];
}

function mysql_updateElement($link, $table, $id, $element, $value){
    mysqli_query($link, "SET NAMES 'utf8'");

    $query = "UPDATE ".$table." SET ".$element." = '".$value."' WHERE id = ".$id;
    $result = mysqli_query($link, $query) or die(mysqli_error($link));

    return $result;
}
// End libraries

$host = 'localhost';
$user = 'root';
$password = 'password'; // Секретный пароль
$db_name = 'smart-garden';
$table = 'data';

$db = mysqli_connect($host, $user, $password, $db_name);
session_start();//  вся процедура работает на сессиях. Именно в ней хранятся данные  пользователя, пока он находится на сайте. Очень важно запустить их в  самом начале странички!!!
if (isset($_POST['login'])) { $login = $_POST['login']; if ($login == '') { unset($login);} } //заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
if (isset($_POST['password'])) { $password=$_POST['password']; if ($password =='') { unset($password);} }
//заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
if (empty($login) or empty($password)) //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
{
    header("Location: /?errorCode=2");
    exit();
}
//если логин и пароль введены,то обрабатываем их, чтобы теги и скрипты не работали, мало ли что люди могут ввести
$login = stripslashes($login);
$login = htmlspecialchars($login);
$password = stripslashes($password);
$password = htmlspecialchars($password);
//удаляем лишние пробелы
$login = trim($login);
$password = trim($password);
// подключаемся к базе

$query = "SELECT * FROM ".$table." WHERE id = ".(int)$login;
$result = mysqli_query($db, $query) or die(mysqli_error($db));
$myrow = mysqli_fetch_array($result);
if (empty($myrow['password']))
{
    //если пользователя с введенным логином не существует
    header("Location: /?errorCode=1");
}
else {
    //если существует, то сверяем пароли
    if ($myrow['password']==$password) {
        //если пароли совпадают, то запускаем пользователю сессию! Можете его поздравить, он вошел!
        $_SESSION['login']=$myrow['login'];
        $_SESSION['id']=$myrow['id'];//эти данные очень часто используются, вот их и будет "носить с собой" вошедший пользователь
        header('Location: /control-center.php');
    }
    else {
        //если пароли не сошлись

        header("Location: /?errorCode=1");
    }
}
?>