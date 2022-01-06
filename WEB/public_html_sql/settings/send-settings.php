<?php
$id = ($_GET['id']);
$airTempSet = ($_GET['airTemp']);
$airHumiditySet = ($_GET['airHumidity']);
$groundTempSet = ($_GET['groundTemp']);
$groundHumiditySet = ($_GET['groundHumidity']);
$deviceNameSet = ($_GET['deviceName']);
$wifiSSIDSet = ($_GET['wifiSSID']);
$wifiPassSet = ($_GET['wifiPass']);
$regularUpdateSet = ($_GET['regularUpdate']);

if ($id == '000001') {
    if ($airTempSet != ''){
        file_put_contents('../json/settings/airTemp.json', json_encode($airTempSet, JSON_FORCE_OBJECT));
    }
    if ($airHumiditySet != ''){
        file_put_contents('../json/settings/airHumidity.json', json_encode($airHumiditySet, JSON_FORCE_OBJECT));
    }
    if ($groundTempSet != ''){
        file_put_contents('../json/settings/groundTemp.json', json_encode($groundTempSet, JSON_FORCE_OBJECT));
    }
    if ($groundHumiditySet != ''){
        file_put_contents('../json/settings/groundHumidity.json', json_encode($groundHumiditySet, JSON_FORCE_OBJECT));
    }
    if ($deviceNameSet != ''){
        file_put_contents('../json/settings/deviceName.json', json_encode($deviceNameSet, JSON_FORCE_OBJECT));
    }
    if ($wifiSSIDSet != ''){
        file_put_contents('../json/settings/wifiSSID.json', json_encode($wifiSSIDSet, JSON_FORCE_OBJECT));
    }
    if ($wifiPassSet != ''){
        file_put_contents('../json/settings/wifiPass.json', json_encode($wifiPassSet, JSON_FORCE_OBJECT));
    }
    if ($regularUpdateSet != ''){
        file_put_contents('../json/settings/regularUpdate.json', json_encode($regularUpdateSet, JSON_FORCE_OBJECT));
    }
}

echo '<meta http-equiv="refresh" content="0;URL=settings-success.php">';
?>