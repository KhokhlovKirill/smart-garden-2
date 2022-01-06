<?php
$mySQLConnect = mysqli_connect("localhost:3306","sg","kirillKhokhlov69Kvantorium", "smart-garden");
if ($mySQLConnect == false){
    echo("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else {
    echo("Соединение установлено успешно");

    $idIsRegistered = mysqli_query( $mySQLConnect, "SELECT id FROM data WHERE id = 1;" );
    echo $idIsRegistered;
}
?>