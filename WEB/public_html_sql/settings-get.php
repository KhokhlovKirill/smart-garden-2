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

$id = ($_GET['id']);

$host = 'localhost';
$user = 'root';
$password = 'password'; // Секретный пароль
$db_name = 'smart-garden';
$table = 'data';

$link = mysqli_connect($host, $user, $password, $db_name);

    $deviceNameSet = mysql_outputElement($link, $table, $id, 'deviceNameSet');

    $airTempSet = mysql_outputElement($link, $table, $id, 'airTempSet');

    $airHumiditySet = mysql_outputElement($link, $table, $id, 'airHumiditySet');

    $groundTempSet = mysql_outputElement($link, $table, $id, 'groundTempSet');

    $groundHumiditySet = mysql_outputElement($link, $table, $id, 'groundHumiditySet');

    $wifiSSIDSet = mysql_outputElement($link, $table, $id, 'wifiSSIDSet');

    $wifiPassSet = mysql_outputElement($link, $table, $id, 'wifiPassSet');

    $regularUpdateSet = mysql_outputElement($link, $table, $id, 'regularUpdateSet');

    $settings = array($deviceNameSet, $groundHumiditySet, $groundTempSet, $airHumiditySet, $airTempSet, $wifiSSIDSet, $wifiSSIDSet, $regularUpdateSet);
    echo json_encode($settings, JSON_FORCE_OBJECT);
?>