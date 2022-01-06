<?php
header('Content-type: text/html; charset=utf-8');

$airTempJson = file_get_contents('json/airTemp.json');
$airTemp = json_decode($airTempJson, true);

$airHumidityJson = file_get_contents('json/airHumidity.json');
$airHumidity = json_decode($airHumidityJson, true);

$groundTempJson = file_get_contents('json/groundTemp.json');
$groundTemp = json_decode($groundTempJson, true);

$groundHumidityJson = file_get_contents('json/groundHumidity.json');
$groundHumidity = json_decode($groundHumidityJson, true);

$idJson = file_get_contents('json/id.json');
$id = json_decode($idJson, true);

$deviceNameJson = file_get_contents('json/deviceName.json');
$deviceName = json_decode($deviceNameJson, true);

$notificationJson = file_get_contents('json/notification.json');
$notification = json_decode($notificationJson, true);

$onlineStatusJson = file_get_contents('json/onlineStatus.json');
$onlineStatus = json_decode($onlineStatusJson, true);

$updateDateJson = file_get_contents('json/updateDate.json');
$updateDate = json_decode($updateDateJson, true);
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

$wifiJson = file_get_contents('json/wifi.json');
$wifi = json_decode($wifiJson, true);
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
  </head>

  <body class="body">
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
              src="img/settings.svg"
              alt="Настройки"
          /></a>
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
</script>
