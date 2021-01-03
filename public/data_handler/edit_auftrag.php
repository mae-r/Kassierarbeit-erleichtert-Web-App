<?php
require("../../includes/funktionen.php");
require("../../auth/current_user.php");
$conn = db_connect();

//der User wird wieder zurückgeleitet.
$redirect_url = (isset($_POST["url"])) ? $_POST["url"] : "../";;

$a_id = $_POST["a_id"];
$action = $_POST["action"];

//Falls ein Error auftritt wird diese Variable auf true geschaltet
$error=false;

//Es wird geschaut ob ein Auftrag gelöscht werden soll
if ($action == "delete"){
    //Der Auftrag wird gelöscht
    $stmt = $conn->prepare("DELETE a 
                            FROM auftraege a
                            WHERE a.id = ? AND a.kassier_id = ". $current_user_id);
    
    //Alle zugehörigen Zahlungsaufträge werden gelöscht
    $stmt1 = $conn->prepare("DELETE za 
                            FROM zahlungsauftraege za
                            INNER JOIN auftraege a 
                            ON za.auftrag_id = a.id
                            WHERE a.id = ? AND a.kassier_id = ". $current_user_id);
    if ($stmt === false || $stmt1 === false){
        $error=true;
    }
    else if (($stmt->bind_param("i", $a_id)) === false ||($stmt1->bind_param("i", $a_id)) === false){
        $error=true;
    }
    //zuerst werden die Zahlungsaufträge gelöscht, da sonst JOIN nicht möglich ist
    else if($stmt1->execute() === false){
        $error=true;
    }
    $stmt1->close();
}
else if ($action == ("abgeben" || "reset")){
    
    if($action == "abgeben"){
        $status = 1;
        //das Datum wird so für die SQL-Datenbank gespeichert
        $abgegeben_str = date("Y-m-d", strtotime($_POST["abgegeben"]));
    }
    else{
        //Zurücksetzen
        $status = 0;
        $abgegeben_str = NULL;
    }
    
    $stmt = $conn->prepare("UPDATE auftraege a
                            SET a.abgegeben = ?, a.status = ?
                            WHERE a.id = ? AND a.kassier_id =". $current_user_id);
    if ($stmt === false){
        $error=true;
    }
    else if (($stmt->bind_param("sii", $abgegeben_str, $status, $a_id)) === false){
        $error=true;
    }
}
// Es erfolgt eine Änderung der Einträge
else {
    $titel = htmlspecialchars($_POST["titel"]);
    $preis = $_POST["preis"];
    //das Datum wird so für die SQL-Datenbank gespeichert
    $erhalten_str = date("Y-m-d", strtotime($_POST["erhalten"]));
    $abgegeben_str = (isset($_POST["abgegeben"])) ? date("Y-m-d", strtotime($_POST["abgegeben"])) : NULL;

    $stmt = $conn->prepare("UPDATE auftraege a
                            SET a.titel = ?, a.preis = ?, a.erhalten = ?, a.abgegeben = ?
                            WHERE a.id = ? AND a.kassier_id =". $current_user_id);
    if ($stmt === false){
        $error=true;
        echo ($stmt->error());
    }
    else if (($stmt->bind_param("sdssi", $titel, $preis, $erhalten_str, $abgegeben_str, $a_id)) === false){
        $error=true;
    }
}


if (!$error && ($stmt->execute()) === false){
    $error=true;
}
if ($error==true){
    $redirect_url.= '?error';
}
header('Location: '. $redirect_url);
$stmt->close();
$conn->close();

?>
