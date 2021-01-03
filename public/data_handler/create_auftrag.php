<?php
require("../../includes/funktionen.php");
require("../../auth/current_user.php");

$conn = db_connect();
//der User wird wieder zurückgeleitet.
$redirect_url = "../";

$titel = htmlspecialchars($_POST["titel"]);
$preis = $_POST["preis"];
//das Datum wird so für die SQL-Datenbank gespeichert
$erhalten_str = date("Y-m-d", strtotime($_POST["erhalten"]));
$status = 0;

//Falls ein Error auftritt wird diese Variable auf true geschaltet
$error=false;

//Auftrag wird erstellt mit Informationen aus POST-Request
$stmt = $conn->prepare("INSERT INTO auftraege (`kassier_id`, `titel`, `preis`, `erhalten`, `status`) VALUES (?,?,?,?,?)");
if ($stmt === false){
    $error=true;
}
else if (($stmt->bind_param("isdsi", $current_user_id,$titel, $preis, $erhalten_str,$status)) === false){
    $error=true;
}
else if (($stmt->execute()) === false){
    $error=true;
}
if ($error==true){
    $redirect_url.= '&error';
}
header('Location: '. $redirect_url); //Rückleitung
$stmt->close();
$conn->close();

?>
