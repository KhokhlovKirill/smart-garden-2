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
session_start();

$host = 'localhost';
$user = 'root';
$password = 'kirillKhokhlov69Kvantorium';
$db_name = 'smart-garden';
$table = 'data';

$link = mysqli_connect($host, $user, $password, $db_name);

$id = $_SESSION['id'];
$oldPass = ($_POST['oldPass']);
$newPass = ($_POST['newPass']);

if ($id == ""){
    header("Location: /");
  }


if ($oldPass != "" && $newPass != ""){
    $currentPass = mysql_outputElement($link, $table, $id, "password");
    if ($currentPass == $oldPass){
        mysql_updateElement($link, $table, $id, "password", $newPass);
        header("Location: /settings/user-settings-success.php");
    } else {
        header("Location: /settings/user-settings.php?errorCode=1");
    }
} else {
    header("Location: /settings/user-settings.php?errorCode=2");
}
?>