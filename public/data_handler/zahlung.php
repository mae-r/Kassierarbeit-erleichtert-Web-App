<?php
require("../../includes/funktionen.php");
require("../../auth/current_user.php");
$conn = db_connect();
//der User wird wieder zurückgeleitet.
$redirect_url = "../auftrag.php?id=" . $_POST["a_id"];

$za_id = $_POST["za_id"];
$zahlungsmittel = $_POST["zahlungsmittel"];
//das Datum wird so für die SQL-Datenbank gespeichert
$date_str = date("Y-m-d", strtotime($_POST["date"]));
$action = $_POST["action"];
$status = 1;

//Falls ein Error auftritt wird diese Variable auf true geschaltet
$error=false;

//Es wird geschaut ob ein Auftrag gelöscht werden soll
if ($action == "delete"){
    $stmt = $conn->prepare("DELETE za 
                            FROM zahlungsauftraege za
                            INNER JOIN auftraege a ON za.auftrag_id = a.id
                            WHERE za.id = ? AND a.kassier_id = ". $current_user_id);
    if ($stmt === false){
        $error=true;
    }
    else if (($stmt->bind_param("i", $za_id)) === false){
        $error=true;
    }
}

else {
    //wenn der Auftrag zurückgesetzt werden soll, werden lediglich die Variablen und der Status geändert
    if ($action == "reset"){
        //status 0  => unbezahlt
        $status = 0;
        $date_str=NULL;
        $zahlungsmittel="";
    }


    //Änderungen werden durchgeführt

    $stmt = $conn->prepare("UPDATE zahlungsauftraege za
                            INNER JOIN auftraege a 
                            ON za.auftrag_id = a.id
                            SET za.status = ?, za.zahlungsmittel = ? , za.datum = ?
                            WHERE za.id = ? AND a.kassier_id =". $current_user_id);
    if ($stmt === false){
        $error=true;
    }
    else if (($stmt->bind_param("issi", $status, $zahlungsmittel, $date_str, $za_id)) === false){
        $error=true;
    }
}


if (!$error && ($stmt->execute()) === false){
    $error=true;
}
if ($error==true){
    $redirect_url.= '&error';
}
header('Location: '. $redirect_url);
$stmt->close();
$conn->close();

?>
