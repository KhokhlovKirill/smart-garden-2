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
session_start();
$id = $_SESSION['id'];
$errorCode = $_GET['errorCode'];

if ($id == ""){
    header("Location: /");
  }

header('Content-type: text/html; charset=utf-8');

if ($_GET['errorCode'] == " "){
    $errorCode = 0;
}

switch ($errorCode){
    case 0:
        $errorMessage = "";
        $errorMessageStyle = "none";
        break;
    case 1:
        $errorMessage = "Неверный старый пароль";
        $errorMessageStyle = "block";
        break;
    case 2:
        $errorMessage = "Заполните все поля";
        $errorMessageStyle = "block";
        break;
}

$host = 'localhost';
$user = 'root';
$password = 'kirillKhokhlov69Kvantorium';
$db_name = 'smart-garden';
$table = 'data';

$link = mysqli_connect($host, $user, $password, $db_name);

$deviceName = mysql_outputElement($link, $table, $id, 'deviceName');

$updateDate = mysql_outputElement($link, $table, $id, 'updateTime');
$currentTime = new \DateTime('now');
$updateTimeSeconds = ($currentTime->getTimestamp() - $updateDate);

$regularUpdate = mysql_outputElement($link, $table, $id, 'regularUpdateSet');

if ($updateTimeSeconds < $regularUpdate + 2){
    $onlineStatus = 'Онлайн';
} else {
    $onlineStatus = 'Офлайн';
}

if ($updateTimeSeconds < 60) {
    $updateTime = $updateTimeSeconds;
    $unitTime = "секунд";
} if ($updateTimeSeconds > 60) {
    $updateTime = intval($updateTimeSeconds / 60);
    $unitTime = "минут";
} if ($updateTimeSeconds > 3600) {
    $updateTime = intval($updateTimeSeconds / 3600);
    $unitTime = "часов";
} if ($updateTimeSeconds > 86400) {
    $updateTime = intval($updateTimeSeconds / 86400);
    $unitTime = "дней";
} if ($updateTimeSeconds > 604800) {
    $updateTime = intval($updateTimeSeconds / 604800);
    $unitTime = "недели";
} if ($updateTimeSeconds > 2592000) {
    $updateTime = intval($updateTimeSeconds / 2592000);
    $unitTime = "месяцев";
} if ($updateTimeSeconds > 31536000) {
    $updateTime = intval($updateTimeSeconds / 31536000);
    $unitTime = "лет";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки — Smart Garden</title>
</head>
<body>
<!doctype html>
<html lang="ru">
<head class="head">
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="../favicon.ico">
    <link rel="icon" href="../img/favicons/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="../img/favicons/apple.png">
    <link rel="manifest" href="../manifest.webmanifest">
    <title>Настройки — Smart Garden</title>

    <script>
        function toSettings(){
            window.location.href = '/settings';
        }

        function exit(){
            window.location.href = '/exit.php';
        }
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-content">
            <div class="header-logo">
                <img
                    src="../img/leaf.svg"
                    alt="Smart Garden"
                    class="header-logo-img"
                />
                <span class="header-logo-text">Smart Garden</span>
            </div>
            <span class="header-separator"></span>
            <div class="header-device">
                <span class="header-device-name"><?= $deviceName ?></span>
                <span class="header-device-id" id="id">(id: <?= $id ?>)</span>
            </div>
            <span class="header-separator"></span>

            <div class="header-online">
                <svg
                    class="header-online-indicator"
                    id="onlineIndicator"
                    width="9"
                    height="9"
                    viewBox="0 0 9 9"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle cx="4.5" cy="4.5" r="4.5"/>
                </svg>

                <span class="header-online-status" id="onlineStatusText"><?= $onlineStatus ?></span>
                <span class="header-online-status-text" id="updateTime">(Обновление: <?= $updateTime ?> <?= $unitTime ?> назад)</span>
            </div>
            <span class="header-separator"></span>
            <a href="/settings" class="header-settings-link"
            ><img
                    class="header-settings-icon"
                    src="../img/back.svg"
                    alt="Настройки"
                /></a>
            <span class="header-separator"></span>
            <img src="../img/exit.svg" class="header-settings-icon" onclick="exit();" style="width: 32px; height: 32px; cursor: pointer">
        </div>
    </div>

    <div class="down-part">
        <div class="main">
            <div class="main-content">
                <span class="main-title">Настройки аккаунта</span>
                <button class="main-button-device-settings" onclick="toSettings();">Настройки устройства</button>
                <span class="main-separator"></span>

            <div class="setting-input">
                <form action="send-user-settings.php" method="post">
                <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Старый пароль</span>
                </div>
                <input type="password" name="oldPass" size="21" maxlength="21" class="setting-input-input" id="deviceName">
              </div>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Новый пароль</span>
                </div>
                <input type="password" name="newPass" size="21" maxlength="21" class="setting-input-input" id="deviceName"">
              </div>

             </div>

                <div class="error-message-block">
                <div class="error-message">
                    <span class="error-message-text"><?= $errorMessage ?></span>
                </div>
                </div>

              <div class="buttons">
              <input type="button" class="button button-cancel" value="Отмена" onclick="toSettings();">
              <input type="submit" name="submit" value="Сохранить" class="button button-save">
              </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<style>

    .error-message-block{
        display: flex;

        margin-right: 50px;

        justify-content: center;
    }

    .error-message{
        display: <?= $errorMessageStyle ?>;

        margin-top: 20px;
        margin-bottom: 30px;

        padding: 10px;
        padding-left: 27px;
        padding-right: 27px;

        width: 260px;
        min-height: 31px;

        border-radius: 30px;

        background-color: #E75C5C;
        text-align: center;

        font-family: 'PTSansCaption-Regular';
        font-size: 18px;
        color: white;
    }

    .error-message-text{
        display: block;
        margin-top: 3px;
    }

    .setting-input{
        margin-bottom: 40px;
    }

    .setting-input-title{
        display: inline-block;

        margin-right: 20px;
    }

    .main-button-device-settings{
        display: inline-block;

        width: 250px;
        height: 46px;

        margin-bottom: 20px;
        margin-right: 20px;

        border: #008000 solid 2px;
        border-radius: 30px;
        
        background-color: white;
        font-size: 18px;
        font-family: 'PTSansCaption-Regular';
        
        color: #008000;
        cursor: pointer;
    }

    .setting-input-input{
        margin-left: 0px !important;
    }

    .main{
        max-width: 700px;
    }

    .buttons{
        padding-top: 20px;
        margin-right: 50px;
        
        text-align: center;
    }

    .button-save{
        text-align: center;
    }
</style>

<script>
    var onlineStatus = window.document.querySelector(".header-online-status").innerHTML;

    if (onlineStatus == "Онлайн"){
        onlineIndicator.style.fill = "#008000";
        onlineStatusText.style.color = "#008000";
        updateTime.style.color = "#008000";
    } else {
        onlineIndicator.style.fill = "red";
        onlineStatusText.style.color = "red";
        updateTime.style.color = "red";
    }
</script>