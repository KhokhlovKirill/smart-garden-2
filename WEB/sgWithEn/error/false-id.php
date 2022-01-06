<!doctype html>
<html lang="ru">
<head class="head">
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../settings/style.css" />
    <link rel="icon" href="../favicon.ico">
    <link rel="icon" href="../img/favicons/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="../img/favicons/apple.png">
    <link rel="manifest" href="../manifest.webmanifest">
    <title>Smart Garden</title>

    <script>
        function toHome(){
            window.location.href = '/';
        }
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-content">
            <div class="header-logo">
                <img
                        src="../img/leaf.svg"
                        alt="Smart Garden"
                        class="header-logo-img"
                />
                <span class="header-logo-text">Smart Garden</span>
            </div>

        </div>
    </div>
    <div class="down-part">
        <div class="main">
            <div class="main-content">
                <h1 class="settings-success-title">Этого устройства не существует</h1>
                <p class="settings-success-subtitle">Возможно Smart Garden еще не успела зарегистрироваться в центре управления, подождите около 5-10 мин и повторите попытку</p>

                <button class="settings-success-button-to-home" onclick="toHome();">На главную</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<style>
    .main-content{
        margin-right: 50px;
    }

    .settings-success-title{
        text-align: center;

        font-family: 'PTSansCaption-Regular';
        font-size: 30px;
        color: #008000;
    }

    .settings-success-subtitle{
        text-align: center;

        font-family: 'PTSansCaption-Regular';
        font-size: 24px;
        color: #008000;
    }

    .settings-success-button-to-home{
        display: block;

        min-height: 55px;

        margin-left: auto;
        margin-right: auto;
        margin-top: 60px;

        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 25px;
        padding-right: 25px;

        border: none;
        border-radius: 30px;

        background-color: #008000;

        font-family: 'PTSansCaption-Regular';
        font-size: 24px;
        color: white;

        cursor: pointer;
    }

    .settings-success-button-to-settings{
        display: block;

        margin-left: auto;
        margin-right: auto;
        margin-top: 30px;

        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 25px;
        padding-right: 25px;

        border: 2px solid #008000;
        border-radius: 30px;

        background-color: white;

        font-family: 'PTSansCaption-Regular';
        font-size: 24px;
        color: #008000;

        cursor: pointer;
    }
</style>

<script>
    var onlineStatus = window.document.querySelector(".header-online-status").innerHTML;

    if (onlineStatus == "Онлайн"){
        onlineIndicator.style.fill = "#008000";
        onlineStatusText.style.color = "#008000";
        updateTime.style.color = "#008000";
    } else {
        onlineIndicator.style.fill = "red";
        onlineStatusText.style.color = "red";
        updateTime.style.color = "red";
    }
</script>
