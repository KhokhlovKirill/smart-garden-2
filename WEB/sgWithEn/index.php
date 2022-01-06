<?php
session_start();

if (empty($_SESSION['login']) or empty($_SESSION['id'])) {
} else {
    header('Location: /control-center.php');
}

switch ($_GET['errorCode']){
    case 0:
        $notification = '';
        $notificationDisplay = 'none';
        break;
    case 1:
        $notification = 'Неверный ID или пароль';
        $notificationDisplay = 'block';
        break;
    case 2:
        $notification = 'Заполните все поля';
        $notificationDisplay = 'block';
}
?>

<!doctype html>
<html lang="ru">
<head class="head">
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../settings/style.css"/>
    <link rel="icon" href="../favicon.ico">
    <link rel="icon" href="../img/favicons/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="../img/favicons/apple.png">
    <link rel="manifest" href="../manifest.webmanifest">
    <title>Smart Garden</title>

    <script>
        function login() {
            const id = document.getElementById('id').value;
            const password = document.getElementById('password').value;

            window.location.href = '/control-center.php?id=' + id;
        }

    </script>
</head>
<body>
<div class="container">
    <div class="down-part">
        <div class="main">
            <div class="main-content">
                <div class="header-logo">
                    <img
                            src="../img/leaf.svg"
                            alt="Smart Garden"
                            class="header-logo-img"
                    />
                    <span class="header-logo-text">Smart Garden</span>
                </div>
                <form action="testreg.php" method="post">
                    <h1 class="settings-success-title">Вход в центр управления</h1>
                    <div class="error-notification">
                        <span class="error-notification-text"><?= $notification ?></span>
                    </div>
                    <div class="settings-success-subtitle">ID устройства</div>
                    <input type="text" name="login" maxlength="21" class="id" id="id">
                    <div class="settings-success-subtitle">Пароль</div>
                    <input type="password" name="password" maxlength="21" class="password" id="password">
            <input type="submit" name="submit" class="settings-success-button-to-home" id="button" value="Войти">
            </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<style>
    .main {
        display: block;
        min-width: 480px;
        width: 30%;
        padding-bottom: 45px;
        background-color: white;
        border-radius: 30px;

        margin-top: 17vh;
        margin-left: auto;
        margin-right: auto;
    }

    @media screen and (max-width: 781px) {
        .container {
            margin: 0;
        }
    }

    .header-logo {
        text-align: center;

        margin-bottom: 40px;
    }

    .header-logo * {
        vertical-align: middle;
    }

    .main-content {
        margin-right: 50px;
    }

    .settings-success-title {
        text-align: center;

        margin-bottom: 35px;
        font-family: 'PTSansCaption-Regular';
        font-size: 30px;
        color: #008000;
    }

    .error-notification{
        display: <?= $notificationDisplay ?>;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 30px;

        padding: 10px;
        padding-left: 27px;
        padding-right: 27px;

        width: 260px;
        min-height: 31px;

        border-radius: 30px;

        background-color: #E75C5C;

        font-family: 'PTSansCaption-Regular';
        font-size: 18px;
        color: white;
    }

    .error-notification-text{
        display: block;

        padding-top: 3px;

        text-align: center;
    }

    .settings-success-subtitle {
        text-align: center;

        margin-bottom: 25px;

        font-family: 'PTSansCaption-Regular';
        font-size: 24px;
        color: #008000;
    }

    .id {
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 30px;

        padding-left: 27px;
        padding-right: 27px;
        width: 260px;
        height: 45px;
        border-radius: 30px;
        border: #BFBFBF solid 2px;
        font-family: 'PTSansCaption-Regular';
        font-size: 20px;
    }

    .password {
        display: block;
        margin-left: auto;
        margin-right: auto;

        padding-left: 27px;
        padding-right: 27px;
        width: 260px;
        height: 45px;
        border-radius: 30px;
        border: #BFBFBF solid 2px;
        font-family: 'PTSansCaption-Regular';
        font-size: 20px;
    }

    .settings-success-button-to-home {
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
</style>
<?php
// Проверяем, пусты ли переменные логина и id пользователя
if (empty($_SESSION['login']) or empty($_SESSION['id'])) {
} else {
    echo '<meta http-equiv="refresh" content="0;URL=control-center.php">';
}
?>