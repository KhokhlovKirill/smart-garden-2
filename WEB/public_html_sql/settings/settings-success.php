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

if ($id == ""){
    header("Location: /");
  }
  
header('Content-type: text/html; charset=utf-8');

$host = 'localhost';
$user = 'root';
$password = 'kirillKhokhlov69Kvantorium';
$db_name = 'smart-garden';
$table = 'data';

$link = mysqli_connect($host, $user, $password, $db_name);

header('Content-type: text/html; charset=utf-8');

$deviceName = mysql_outputElement($link, $table, $id, 'deviceName');
$updateDate = mysql_outputElement($link, $table, $id, 'updateTime');

$currentTime = new \DateTime('now');
$updateTimeSeconds = ($currentTime->getTimestamp() - $updateDate);

if ($updateTimeSeconds < 300){
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
        function toHome(){
            window.location.href = '/';
        }

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
            <a href="/" class="header-settings-link"
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
                <h1 class="settings-success-title">Настройки сохранены и будут применены при обновлении</h1>
                <p class="settings-success-subtitle">Для принудительного обновления выберите соответствующий пункт в меню Smart Garden</p>

                <button class="settings-success-button-to-home" onclick="toHome();">На главную</button>
                <button class="settings-success-button-to-settings" onclick="toSettings();">Вернуться к настройкам</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<style>
    .main-content{
        margin-right: 50px;
    }

    .settings-success-title{
        text-align: center;

        font-family: 'PTSansCaption-Regular';
        font-size: 30px;
        color: #008000;
    }

    .settings-success-subtitle{
        text-align: center;

        font-family: 'PTSansCaption-Regular';
        font-size: 24px;
        color: #008000;
    }

    .settings-success-button-to-home{
        display: block;

        min-height: 55px;

        margin-left: auto;
        margin-right: auto;
        margin-top: 60px;

        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 25px;
        padding-right: 25px;

        border: none;
        border-radius: 30px;

        background-color: #008000;

        font-family: 'PTSansCaption-Regular';
        font-size: 24px;
        color: white;

        cursor: pointer;
    }

    .settings-success-button-to-settings{
        display: block;

        margin-left: auto;
        margin-right: auto;
        margin-top: 30px;

        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 25px;
        padding-right: 25px;

        border: 2px solid #008000;
        border-radius: 30px;

        background-color: white;

        font-family: 'PTSansCaption-Regular';
        font-size: 24px;
        color: #008000;

        cursor: pointer;
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
