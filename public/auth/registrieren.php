<?php
require("../../includes/funktionen.php");
$conn = db_connect();
$error = false;

$name = $_POST["name"];
$username = $_POST["username"];
$password = $_POST["password"];
$password_hashed = password_hash($password,PASSWORD_DEFAULT,["cost"=> 12]);

if (strlen($password) < 6){
    $error = true;
}

//sql string um das Passwort des Nutzers zu bekommen
$stmt = $conn->prepare("INSERT INTO `benutzer`(`username`, `password`, `name`, `type`) VALUES (?,?,?,1)");
if ($error || $stmt === false){
    $error=true;
}
else if (($stmt->bind_param("sss", $username,$password_hashed,$name)) === false){
    $error=true;
}
else if (($stmt->execute()) === false){
    //z.B wenn Benutzername schon existiert
    $error=true;
}

//Der Kassier selbst wird zu seiner Klasse hinzugefügt
$sql="INSERT INTO `schulklassen`(`kassier_id`, `schueler_id`) VALUES (LAST_INSERT_ID(),LAST_INSERT_ID())";

if ($error || $conn->query($sql)=== false){
    header('Location: ../registrieren.php?error');
    die();
}
else{
    header('Location: ../login.php?new_user');
}

?>