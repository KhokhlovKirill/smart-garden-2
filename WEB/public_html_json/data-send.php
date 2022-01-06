<?php
$id = ($_GET['id']);
$airTemp = ($_GET['airTemp']);
$airHumidity = ($_GET['airHumidity']);
$groundTemp = ($_GET['groundTemp']);
$groundHumidity = ($_GET['groundHumidity']);
$deviceName = ($_GET['deviceName']);
$notificationCode = ($_GET['notificationCode']);
$wifi = ($_GET['wifi']);
if ($id == '000001') {
    $updateDate = new \DateTime('now');
    file_put_contents('json/updateDate.json', json_encode($updateDate->getTimestamp(), JSON_FORCE_OBJECT));
    if ($airTemp != ''){
        file_put_contents('json/airTemp.json', json_encode($airTemp, JSON_FORCE_OBJECT));
        echo '<p>success</p>';
    }
    if ($airHumidity != ''){
        file_put_contents('json/airHumidity.json', json_encode($airHumidity, JSON_FORCE_OBJECT));
        echo '<p>success</p>';
    }
    if ($groundTemp != ''){
        file_put_contents('json/groundTemp.json', json_encode($groundTemp, JSON_FORCE_OBJECT));
        echo '<p>success</p>';
    }
    if ($groundHumidity != ''){
        file_put_contents('json/groundHumidity.json', json_encode($groundHumidity, JSON_FORCE_OBJECT));
        echo '<p>success</p>';
    }
    if ($id != ''){
        file_put_contents('json/id.json', json_encode($id, JSON_FORCE_OBJECT));
        echo '<p>success</p>';
    }
    if ($deviceName != ''){
        file_put_contents('json/deviceName.json', json_encode($deviceName, JSON_FORCE_OBJECT));
        echo '<p>success</p>';
    }
    if ($notificationCode != ''){
        switch ($notificationCode){
            case 0000:
                file_put_contents('json/notification.json', json_encode('Уведомления отсутствуют', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 1000:
                file_put_contents('json/notification.json', json_encode('Сухая почва, необходим полив!', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 0100:
                file_put_contents('json/notification.json', json_encode('Низкая температура почвы!', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 0200:
                file_put_contents('json/notification.json', json_encode('Высокая температура почвы!', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;    
            case 0010:
                file_put_contents('json/notification.json', json_encode('Сухой воздух!', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 0001:
                file_put_contents('json/notification.json', json_encode('Низкая температура воздуха!', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 0002:
                file_put_contents('json/notification.json', json_encode('Высокая температура воздуха!', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 1100:
                file_put_contents('json/notification.json', json_encode('Сухая почва, необходим полив! Низкая температура почвы!', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 0110:
                file_put_contents('json/notification.json', json_encode('', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
            case 0011:
                file_put_contents('json/notification.json', json_encode('', JSON_FORCE_OBJECT));
                echo '<p>success</p>';
                break;
        }
    }
    if ($wifi != ''){
        file_put_contents('json/wifi.json', json_encode($wifi, JSON_FORCE_OBJECT));
        echo '<p>success</p>';
    }
} else {
    echo 'ERROR: False ID';
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
