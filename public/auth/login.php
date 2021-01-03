<?php
require("../../includes/funktionen.php");

$username = $_POST["username"];
$password = $_POST["password"];

//Wen kein URL angebenen wurde, wird index.php genommen
$redirect_url = (isset($_POST["url"])) ? $_POST["url"] : "../";

$conn = db_connect();

//sql string um das Passwort des Nutzers zu bekommen
$stmt = $conn->prepare("SELECT id,password FROM benutzer where username = ?");
if ($stmt === false){
    $error=true;
}
else if (($stmt->bind_param("s", $username)) === false){
    $error=true;
}
else if (($stmt->execute()) === false){
    $error=true;
}
$result = $stmt->get_result();
if ($result->num_rows == 0){
    //Dieser Benutzername ist nicht vorhanden
    header('Location: ../login.php?url='.$redirect_url.'&wrong_username'); //GET-Parameter damit Fehlermeldung angezeigt wird.
    die();
}
$current_user = $result->fetch_assoc();
$pwd_hashed = $current_user["password"];
$current_user_id = $current_user["id"];
if(!password_verify($password,$pwd_hashed)){
    //Passwort stimmt nicht
    header('Location: ../login.php?url='.$redirect_url.'&wrong_password');
    die();
}

//Benutzer ID wird in der Session gespeichert.
session_start();
$_SESSION["user_id"]=$current_user_id;

//Weiterleitung auf URL
header('Location: '. $redirect_url);
?>