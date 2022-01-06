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

$host = 'localhost';
$user = 'root';
$password = 'kirillKhokhlov69Kvantorium';
$db_name = 'smart-garden';
$table = 'data';

$link = mysqli_connect($host, $user, $password, $db_name);

$id = $_SESSION['id'];
$airTempSet = ($_GET['airTemp']);
$airHumiditySet = ($_GET['airHumidity']);
$groundTempSet = ($_GET['groundTemp']);
$groundHumiditySet = ($_GET['groundHumidity']);
$deviceNameSet = ($_GET['deviceName']);
$wifiSSIDSet = ($_GET['wifiSSID']);
$wifiPassSet = ($_GET['wifiPass']);
$regularUpdateSet = ($_GET['regularUpdate']);


    if ($airTempSet != ''){
        mysql_updateElement($link, $table, $id, 'airTempSet', $airTempSet);
    }
    if ($airHumiditySet != ''){
        mysql_updateElement($link, $table, $id, 'airHumiditySet', $airHumiditySet);
    }
    if ($groundTempSet != ''){
        mysql_updateElement($link, $table, $id, 'groundTempSet', $groundTempSet);
    }
    if ($groundHumiditySet != ''){
        mysql_updateElement($link, $table, $id, 'groundHumiditySet', $groundHumiditySet);
    }
    if ($deviceNameSet != ''){
        mysql_updateElement($link, $table, $id, 'deviceNameSet', $deviceNameSet);
    }
    if ($wifiSSIDSet != ''){
        mysql_updateElement($link, $table, $id, 'wifiSSIDSet', $wifiSSIDSet);
    }
    if ($wifiPassSet != ''){
        mysql_updateElement($link, $table, $id, 'wifiPassSet', $wifiPassSet);
    }
    if ($regularUpdateSet != ''){
        mysql_updateElement($link, $table, $id, 'regularUpdateSet', $regularUpdateSet);
    }


header("Location: settings-success.php");
?>