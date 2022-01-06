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
$password = 'kirillKhokhlov69Kvantorium';
$db_name = 'smart-garden';
$table = 'data';

$link = mysqli_connect($host, $user, $password, $db_name);

    $deviceNameSet = mysql_outputElement($link, $table, $id, 'deviceNameSet');

    echo '<div>';
    echo $deviceNameSet;
    echo '</div>';

    $airTempSet = mysql_outputElement($link, $table, $id, 'airTempSet');

    echo '<div>';
    echo $airTempSet;
    echo '</div>';

    $airHumiditySet = mysql_outputElement($link, $table, $id, 'airHumiditySet');

    echo '<div>';
    echo $airHumiditySet;
    echo '</div>';

    $groundTempSet = mysql_outputElement($link, $table, $id, 'groundTempSet');

    echo '<div>';
    echo $groundTempSet;
    echo '</div>';

    $groundHumiditySet = mysql_outputElement($link, $table, $id, 'groundHumiditySet');

    echo '<div>';
    echo $groundHumiditySet;
    echo '</div>';

    $wifiSSIDSet = mysql_outputElement($link, $table, $id, 'wifiSSIDSet');

    echo '<div>';
    echo $wifiSSIDSet;
    echo '</div>';

    $wifiPassSet = mysql_outputElement($link, $table, $id, 'wifiPassSet');

    echo '<div>';
    echo $wifiPassSet;
    echo '</div>';

    $regularUpdateSet = mysql_outputElement($link, $table, $id, 'regularUpdateSet');

    echo '<div>';
    echo $regularUpdateSet;
    echo '</div>';
?>