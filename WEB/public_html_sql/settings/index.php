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

$deviceName = mysql_outputElement($link, $table, $id, 'deviceName');

// Получение настроек
$deviceNameSet = mysql_outputElement($link, $table, $id, 'deviceNameSet');

$airTempSet = mysql_outputElement($link, $table, $id, 'airTempSet');

$airHumiditySet = mysql_outputElement($link, $table, $id, 'airHumiditySet');

$groundTempSet = mysql_outputElement($link, $table, $id, 'groundTempSet');

$groundHumiditySet = mysql_outputElement($link, $table, $id, 'groundHumiditySet');

$wifiSSIDSet = mysql_outputElement($link, $table, $id, 'wifiSSIDSet');

$wifiPassSet = mysql_outputElement($link, $table, $id, 'wifiPassSet');

$regularUpdateSet = mysql_outputElement($link, $table, $id, 'regularUpdateSet');
//________________

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

<!DOCTYPE html>
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
        function save(){
            const deviceNameSet = document.getElementById('deviceName').value;
            const groundHumiditySet = document.getElementById('groundHumidity').value;
            const groundTempSet = document.getElementById('groundTemp').value;
            const airHumiditySet = document.getElementById('airHumidity').value;
            const airTempSet = document.getElementById('airTemp').value;
            const wifiSSIDSet = document.getElementById('wifiSSID').value;
            const wifiPassSet = document.getElementById('wifiPass').value;
            const regularUpdateSet = document.getElementById('regularUpdate').value;
            const deviceID = '<?= $id ?>';

            if (regularUpdateSet >= 1 && regularUpdateSet <= 30) {
            } else {
                alert('Регулярность обновлений может иметь значение от 1 до 30')
                return;
            }

            if (groundHumiditySet >= 0 && groundHumiditySet <= 100) {
            } else {
                alert('Недопустимое значение влажности почвы')
                return;
            }

            if (groundTempSet >= 0 && groundTempSet <= 50) {
            } else {
                alert('Недопустимое значение температуры почвы')
                return;
            }

            if (airHumiditySet >= 0 && airHumiditySet <= 100) {
            } else {
                alert('Недопустимое значение влажности воздуха')
                return;
            }

            if (airTempSet >= 1 && airTempSet <= 50) {
            } else {
                alert('Недопустимое значение температуры воздуха')
                return;
            }
            const getRequest = '/settings/send-settings.php?id=' + deviceID + '&deviceName=' + deviceNameSet + '&groundHumidity=' + groundHumiditySet + '&groundTemp='
                + groundTempSet + '&airHumidity=' + airHumiditySet + '&airTemp=' + airTempSet + '&wifiSSID=' + wifiSSIDSet
                + '&wifiPass=' + wifiPassSet + '&regularUpdate=' + regularUpdateSet;
            window.location.href = getRequest;
        }

        function cancel(){
          window.location.href = '/control-center.php';
        }

        function userSettings(){
          window.location.href = '/settings/user-settings.php';
        }
    </script>
  </head>

  <body class="body">
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
          <a onclick="cancel();" style="cursor: pointer" class="header-settings-link"
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
            <div class="main-header">
            <span class="main-title">Настройки</span>
            <button class="main-button-user-settings" onclick="userSettings();">Настройки аккаунта</button>
            </div>
            <span class="main-separator"></span>
            <div class="main-down">
            <div class="main-device-settings">
              <span class="main-subtitle-text">Устройство</span>
              <span class="main-separator"></span>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Имя устройства</span>
                <span class="setting-input-subtitle">(до 21 символа)</span>
                </div>
                <input type="text" size="21" maxlength="21" class="setting-input-input" id="deviceName" value="<?= $deviceNameSet ?>">
              </div>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Влажность почвы</span>
                <span class="setting-input-subtitle">%</span>
                </div>
                <input type="text" size="21" maxlength="21" class="setting-input-input" id="groundHumidity" value="<?= $groundHumiditySet ?>">
              </div>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Температура почвы</span>
                <span class="setting-input-subtitle">°С</span>
                </div>
                <input type="text" size="21" maxlength="21" class="setting-input-input" id="groundTemp" value="<?= $groundTempSet ?>">
              </div>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Влажность воздуха</span>
                <span class="setting-input-subtitle">%</span>
                </div>
                <input type="text" size="21" maxlength="21" class="setting-input-input" id="airHumidity" value="<?= $airHumiditySet ?>">
              </div>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Температура воздуха</span>
                <span class="setting-input-subtitle">°С</span>
                </div>
                <input type="text" size="21" maxlength="21" class="setting-input-input" id="airTemp" value="<?= $airTempSet ?>">
              </div>

            </div>
            <div class="main-data-send-settings">
              <span class="main-subtitle-text">Передача данных</span>
              <span class="main-separator"></span>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Wi-Fi</span>
                <span class="setting-input-subtitle">SSID</span>
                </div>
                <input type="text" size="21" maxlength="21" class="setting-input-input" id="wifiSSID" value="<?= $wifiSSIDSet ?>">
              </div>

              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Wi-Fi</span>
                <span class="setting-input-subtitle">Пароль</span>
                </div>
                <input type="password" size="21" maxlength="21" class="setting-input-input" id="wifiPass" value="<?= $wifiPassSet ?>">
              </div>
 
              <div class="setting-input-block">
                <div class="setting-input-title-block">
                <span class="setting-input-title">Регулярность обновлений</span>
                <span class="setting-input-subtitle">минут (до 30 мин)</span>
                </div>
                <input type="text" size="21" maxlength="2" class="setting-input-input" id="regularUpdate" value="<?= $regularUpdateSet ?>">
              </div>
              <span class="main-information">Если Smart Garden не сможет подключиться к Wi-Fi, то будет открыта точка доступа для настройки</span>
            </div>
            </div>
            <div class="main-buttons">
              <div class="main-buttons-information">
              <span class="main-information-big">Настройки будут изменены при следующем обновлении</span>
              <span class="main-information">(для принудительного обновления выберите соответствующий пункт в меню Smart Garden)</span>
              </div>
              <button class="button-cancel" onclick="cancel();">Отмена</button>
              <button class="button-save" onclick="save();">Сохранить</button>
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

  function exit(){
      window.location.href = '/exit.php';
  }
</script>