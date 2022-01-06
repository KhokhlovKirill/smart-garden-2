function save(){
    var deviceNameSet = document.getElementById('deviceName').value;
    var groundHumiditySet = document.getElementById('groundHumidity').value;
    var groundTempSet = document.getElementById('groundTemp').value;
    var airHumiditySet = document.getElementById('airHumidity').value;
    var airTempSet = document.getElementById('airTemp').value;
    var wifiSSIDSet = document.getElementById('wifiSSID').value;
    var wifiPassSet = document.getElementById('wifiPass').value;
    var regularUpdateSet = document.getElementById('regularUpdate').value;
    
    var getRequest = '/data-send.php?id='
}

function cancel(){
    window.location.href = '/';
}