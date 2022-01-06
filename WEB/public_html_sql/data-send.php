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

function registerID($link, $table, $id){
    mysqli_query($link, "SET NAMES 'utf8'");

    $query = "INSERT INTO ".$table." (id) VALUES ($id);";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));

    echo 'Registration ID...';
    return $result;
}

function shift_in_left (&$arr) {
    $item = array_shift($arr);
    array_push ($arr,$item);
   }


$id = ($_GET['id']);
$airTemp = ($_GET['airTemp']);
$airHumidity = ($_GET['airHumidity']);
$groundTemp = ($_GET['groundTemp']);
$groundHumidity = ($_GET['groundHumidity']);
$deviceName = ($_GET['deviceName']);
$notificationCode = ($_GET['notificationCode']);
$wifi = ($_GET['wifi']);


$host = 'localhost';
$user = 'root';
$password = 'kirillKhokhlov69Kvantorium';
$db_name = 'smart-garden';
$table = 'data';

echo ('<p>Connecting to MySQL...</p>');
$link = mysqli_connect($host, $user, $password, $db_name);

$oldUpdateTime = mysql_outputElement($link, $table, $id, "updateTime");

if (mysql_outputElement($link, $table, $id, 'id') == ''){
    registerID($link, $table, $id);
} else {
    echo ('<p>ID is registered</p>');
}
if ($groundHumidity != '') {
    mysql_updateElement($link, $table, $id, 'currentGroundHumidity', $groundHumidity);
}
if ($groundTemp != '') {
    mysql_updateElement($link, $table, $id, 'currentGroundTemp', $groundTemp);
}
if ($airHumidity != '') {
    mysql_updateElement($link, $table, $id, 'currentAirHumidity', $airHumidity);
}
if ($airTemp != '') {
    mysql_updateElement($link, $table, $id, 'currentAirTemp', $airTemp);
}
if ($deviceName != '') {
    mysql_updateElement($link, $table, $id, 'deviceName', $deviceName);
}
if ($notificationCode != '') {
    mysql_updateElement($link, $table, $id, 'notificationCode', $notificationCode);
}
if ($wifi != '') {
    mysql_updateElement($link, $table, $id, 'wifiSSID', $wifi);
}
$updateDate = new \DateTime('now');
mysql_updateElement($link, $table, $id, 'updateTime', $updateDate->getTimestamp());



if ($updateDate->getTimestamp() - $oldUpdateTime > 3600){
    if ($groundHumidity != ''){
        $groundHumidityHistory = json_decode(mysql_outputElement($link, $table, $id, "groundHumidityHistory"), TRUE);
        //_______
        if ($groundHumidity != ''){
            if (count($groundHumidityHistory) >= 24){
                shift_in_left($groundHumidityHistory);
                $groundHumidityHistory[23] = (float)$groundHumidity;
            } else {
                if ($groundHumidityHistory[0] != NULL){
                    array_push($groundHumidityHistory,(float)$groundHumidity);
                } else {
                    $groundHumidityHistory[0] = (float)$groundHumidity;
                }
            }
        }
        // End
        var_dump($groundHumidityHistory);
        mysql_updateElement($link, $table, $id, 'groundHumidityHistory', json_encode($groundHumidityHistory, JSON_FORCE_OBJECT));
    }

    if ($groundTemp != ''){
        $groundTempHistory = json_decode(mysql_outputElement($link, $table, $id, "groundTempHistory"), TRUE);
        //_______
        if ($groundTemp != ''){
            if (count($groundTempHistory) >= 24){
                shift_in_left($groundTempHistory);
                $groundTempHistory[23] = (float)$groundTemp;
            } else {
                if ($groundTempHistory[0] != NULL){
                    array_push($groundTempHistory,(float)$groundTemp);
                } else {
                    $groundTempHistory[0] = (float)$groundTemp;
                }
            }
        }
        // End
        var_dump($groundTempHistory);
        mysql_updateElement($link, $table, $id, 'groundTempHistory', json_encode($groundTempHistory, JSON_FORCE_OBJECT));
    }

    if ($airHumidity != ''){
        $airHumidityHistory = json_decode(mysql_outputElement($link, $table, $id, "airHumidityHistory"), TRUE);
        //_______
        if ($airHumidity != ''){
            if (count($airHumidityHistory) >= 24){
                shift_in_left($airHumidityHistory);
                $airHumidityHistory[23] = (float)$airHumidity;
            } else {
                if ($airHumidityHistory[0] != NULL){
                    array_push($airHumidityHistory,(float)$airHumidity);
                } else {
                    $airHumidityHistory[0] = (float)$airHumidity;
                }
            }
        }
        // End
        var_dump($airHumidityHistory);
        mysql_updateElement($link, $table, $id, 'airHumidityHistory', json_encode($airHumidityHistory, JSON_FORCE_OBJECT));
    }

    if ($airTemp != ''){
        $airTempHistory = json_decode(mysql_outputElement($link, $table, $id, "airTempHistory"), TRUE);
        //_______
        if ($airTemp != ''){
            if (count($airTempHistory) >= 24){
                shift_in_left($airTempHistory);
                $airTempHistory[23] = (float)$airTemp;
            } else {
                if ($airTempHistory[0] != NULL){
                    array_push($airTempHistory,(float)$airTemp);
                } else {
                    $airTempHistory[0] = (float)$airTemp;
                }
            }
        }
        // End
        var_dump($airTempHistory);
        mysql_updateElement($link, $table, $id, 'airTempHistory', json_encode($airTempHistory, JSON_FORCE_OBJECT));
    }
}
?>