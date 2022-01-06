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

header('Content-type: text/html; charset=utf-8');

$host = 'localhost';
$user = 'root';
$password = 'kirillKhokhlov69Kvantorium';
$db_name = 'smart-garden';
$table = 'data';

$link = mysqli_connect($host, $user, $password, $db_name);

if($id == ''){
    header('Location: /');
}

if ((mysql_outputElement($link, $table, $id, 'id') == '')){
        header('Location: /');
}


$airTemp = mysql_outputElement($link,$table,$id,'currentAirTemp');

$airHumidity = mysql_outputElement($link, $table, $id, 'currentAirHumidity');

$groundTemp = mysql_outputElement($link, $table, $id, 'currentGroundTemp');

$groundHumidity = mysql_outputElement($link, $table, $id, 'currentGroundHumidity');

$deviceName = mysql_outputElement($link, $table, $id, 'deviceName');

$notificationCode = mysql_outputElement($link, $table, $id, 'notificationCode');

$wifi = mysql_outputElement($link, $table, $id, 'wifiSSID');

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

$notification = '';
if ($notificationCode == '0000') $notification = 'Уведомления отсутствуют';
if ($notificationCode[0] == '1'){
    $notification = $notification . '<p>Необходим полив почвы.</p>';
}
if ($notificationCode[1] == '1'){
    $notification = $notification . '<p>Низкая температура почвы.</p>';
}
if ($notificationCode[1] == '2'){
    $notification = $notification . '<p>Слишком высокая температура почвы.</p>';
}
if ($notificationCode[2] == '1'){
    $notification = $notification . '<p>Необходимо увлажнение воздуха.</p>';
}
if ($notificationCode[2] == '2'){
    $notification = $notification . '<p>Слишком влажный воздух.</p>';
}
if ($notificationCode[3] == '1'){
    $notification = $notification . '<p>Низкая температура воздуха.</p>';
}
if ($notificationCode[3] == '2'){
    $notification = $notification . '<p>Слишком высокая температура воздуха.</p>';
}
?>

<!DOCTYPE html>
<html lang="ru">
  <head class="head">
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="favicon.ico">
    <link rel="icon" href="img/favicons/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="img/favicons/apple.png">
    <link rel="manifest" href="manifest.webmanifest">
    <title>Smart Garden</title>

      <script>
          function toSettings(){
              window.location.href = '/settings';
          }
      </script>
  </head>

  <body class="body">
  <div class="language-selector">
      <div class="language-selector-content">

          <span class="language-selector-link current-lang">RU</span>
          <span class="language-separator"></span>
          <a href=""><span class="language-selector-link">EN</span></a>

      </div>
  </div>
    <div class="container">
      <div class="header">
        <div class="header-content">
          <div class="header-logo">
            <img
              src="img/leaf.svg"
              alt="Smart Garden"
              class="header-logo-img"
            />
            <span class="header-logo-text">Smart Garden</span>
          </div>
          <span class="header-separator"></span>
          <div class="header-device">
            <span class="header-device-name"><?= $deviceName ?></span>
            <span class="header-device-id">(id: <?= $id ?>)</span>
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
            >
              <circle cx="4.5" cy="4.5" r="4.5"/>
            </svg>

            <span class="header-online-status" id="onlineStatusText"><?= $onlineStatus ?></span>
            <span class="header-online-status-text" id="updateTime">(Обновление: <?= $updateTime ?> <?= $unitTime ?> назад)</span>
          </div>
          <span class="header-separator"></span>
          <a onclick="toSettings();" style="cursor: pointer" class="header-settings-link"
            ><img
              class="header-settings-icon"
              src="img/settings.svg"
              alt="Настройки"
          /></a>
            <span class="header-separator"></span>
            <img src="img/exit.svg" class="header-settings-icon" onclick="exit();" style="width: 32px; height: 32px; cursor: pointer">
        </div>
      </div>

      <div class="down-part">
        <div class="main">
            <div class="main-content">
                <div class="main-ground">
                    <span class="main-title">Почва</span>
                    <span class="main-separator"></span>

                    <div class="main-ground-data">
                        <div class="main-data">
                            <img class="main-icon" src="img/drop.svg" alt="Влажность">
                            <span class="main-value"><?= $groundHumidity ?> %</span>
                            <img src="" alt="" class="main-charts">
                        </div>
                        <div class="main-data">
                            <img class="main-icon" src="img/temperature.svg" alt="Температура">
                            <span class="main-value"><?= $groundTemp ?> °C</span>
                            <img src="" alt="" class="main-charts">
                        </div>
                    </div>
                </div>
                <div class="main-air">
                    <span class="main-title">Воздух</span>
                    <span class="main-separator"></span>

                    <div class="main-air-data">
                        <div class="main-data">
                            <img class="main-icon" src="img/drop.svg" alt="Влажность">
                            <span class="main-value"><?= $airHumidity ?> %</span>
                            <img src="" alt="" class="main-charts">
                        </div>
                        <div class="main-data">
                            <img class="main-icon" src="img/temperature.svg" alt="Температура">
                            <span class="main-value"><?= $airTemp ?> °C</span>
                            <img src="" alt="" class="main-charts">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="notification">
          <div class="notification-content">
            <div class="notification-notification">
              <span class="notification-title">Уведомления</span>
              <div class="notification-message-box" id="errorMessageBox">
              <span class="notification-message" id="errorMessage"><?= $notification ?></span>
              </div>
            </div>
            <span class="notification-separator"></span>
            <div class="notification-wifi">
              <span class="notification-title">Wi-Fi</span>
              <div class="notification-wifi-content">
              <img src="img/wifi.svg" alt="" class="notification-wifi-icon">
              <span class="notification-wifi-ssid"><?= $wifi ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>

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

  var notification = window.document.querySelector(".notification-message").innerHTML;
  
  if (notification == "Уведомления отсутствуют"){
    errorMessageBox.style.backgroundColor = "#008000";
  } else {
    errorMessageBox.style.backgroundColor = "#E75C5C";
  }
  
    var notification = window.document.querySelector(".notification-message").innerHTML;
  
  if (notification == "Уведомления отсутствуют"){
    errorMessageBox.style.backgroundColor = "#008000";
  } else {
    errorMessageBox.style.backgroundColor = "#E75C5C";
  }

  function exit(){
      window.location.href = '/exit.php';
  }
</script>
