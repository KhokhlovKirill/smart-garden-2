<?php

$id = ($_GET['id']);

if ($id == '000001') {
    $deviceNameSetJson = file_get_contents('json/settings/deviceName.json');
    $deviceNameSet = json_decode($deviceNameSetJson, true);

    echo '<div>';
    echo $deviceNameSet;
    echo '</div>';

    $airTempSetJson = file_get_contents('json/settings/airTemp.json');
    $airTempSet = json_decode($airTempSetJson, true);

    echo '<div>';
    echo $airTempSet;
    echo '</div>';

    $airHumiditySetJson = file_get_contents('json/settings/airHumidity.json');
    $airHumiditySet = json_decode($airHumiditySetJson, true);

    echo '<div>';
    echo $airHumiditySet;
    echo '</div>';

    $groundTempSetJson = file_get_contents('json/settings/groundTemp.json');
    $groundTempSet = json_decode($groundTempSetJson, true);

    echo '<div>';
    echo $groundTempSet;
    echo '</div>';

    $groundHumiditySetJson = file_get_contents('json/settings/groundHumidity.json');
    $groundHumiditySet = json_decode($groundHumiditySetJson, true);

    echo '<div>';
    echo $groundHumiditySet;
    echo '</div>';

    $wifiSSIDSetJson = file_get_contents('json/settings/wifiSSID.json');
    $wifiSSIDSet = json_decode($wifiSSIDSetJson, true);

    echo '<div>';
    echo $wifiSSIDSet;
    echo '</div>';

    $wifiPassSetJson = file_get_contents('json/settings/wifiPass.json');
    $wifiPassSet = json_decode($wifiPassSetJson, true);

    echo '<div>';
    echo $wifiPassSet;
    echo '</div>';

    $regularUpdateSetJson = file_get_contents('json/settings/regularUpdate.json');
    $regularUpdateSet = json_decode($regularUpdateSetJson, true);

    echo '<div>';
    echo $regularUpdateSet;
    echo '</div>';
} else {
    echo 'error1';
}
?>