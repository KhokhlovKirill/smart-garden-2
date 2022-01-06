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

$amountData = mysql_outputElement($link, $table, $id, 'amountData');
$amountData = $amountData + 1;


if ($amountData >= 19) {
    $airTempArrayJson = file_get_contents('json/chart/airTempArray.json');
    $airTempArray = json_decode($airTempArrayJson,TRUE);
    var_dump($airTempArray);
    unset($airTempArrayJson);
    $airTempArrayCount = count($airTempArray);
    if ($airTempArrayCount >= 25){

        $item = array_shift($airTempArray);
        array_push ($airTempArray,(float)$item);

        unset($airTempArray[25]);
        $airTempArrayCount = count($airTempArray);
    }
    if ($airTempArray[0] != NULL){
        array_push ($airTempArray,(float)$airTemp);
    } else {
        $airTempArray[0] = (float)$airTemp;
    }
    file_put_contents('json/chart/airTempArray.json', json_encode($airTempArray, JSON_FORCE_OBJECT));
    echo '  Array was modified!  ';
    $amountData = 0;
    var_dump($airTempArray);
}
mysql_updateElement($link, $table,$id, 'amountData', $amountData);
echo '<p>Update '.$amountData.' / 19</p>';
?>
