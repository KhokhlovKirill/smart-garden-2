<?php
$id = ($_GET['id']);
$airTemp = ($_GET['airTemp']);
$airHumidity = ($_GET['airHumidity']);
$groundTemp = ($_GET['groundTemp']);
$groundHumidity = ($_GET['groundHumidity']);
$deviceName = ($_GET['deviceName']);
$notificationCode = ($_GET['notificationCode']);
$wifi = ($_GET['wifi']);


$mySQLConnect = mysqli_connect("84.252.74.159","sg","kirillKhokhlov69Kvantorium", "smart-garden");
if ($mySQLConnect == false){
    echo("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else {
    echo("Соединение установлено успешно");
}

$amountDataJson = file_get_contents('json/chart/amountData.json');
$amountData = json_decode($amountDataJson, true);
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
file_put_contents('json/chart/amountData.json', json_encode($amountData, JSON_FORCE_OBJECT));
echo $amountData;
?>
