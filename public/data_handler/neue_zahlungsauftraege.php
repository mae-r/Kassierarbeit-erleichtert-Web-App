<?php
require("../../includes/funktionen.php");
require("../../auth/current_user.php");

$a_id = $_POST["a_id"];

//die auftrag id muss eine nr sein
if (!ctype_digit($a_id)){
    //Auftrag ist keine Nummer
    $a_id=0; //Für diese Id gibt es keinen Auftrag, daher werden auch keine Zahlungsaufträge hinzugefügt
}

$conn = db_connect();
//der User wird wieder zurückgeleitet.
$redirect_url = "../auftrag.php?id=".$a_id;

//Falls nicht exisitert -> leeres Array
$schueler = (isset($_POST["schueler"])) ? $_POST["schueler"] : [];

//Falls ein Error auftritt wird diese Variable auf true geschaltet
$error=false;

//Hinzufügen der Aufträge
//Die Schueler-Id und Auftrag Id werden aus den anderen Tabellen kopiert.
//Dies ist nur möglich, wenn jetziger Benutzer auch Zugriff auf diese Daten hat
$stmt = $conn->prepare("INSERT INTO zahlungsauftraege
                        (`schueler_id`,`auftrag_id`)
                        SELECT schueler_id, a.id
                        FROM auftraege a
                        INNER JOIN schulklassen sk ON sk.kassier_id = a.kassier_id
                        WHERE sk.schueler_id = ? AND a.id = ? AND a.kassier_id =".$current_user_id); 

if (!$stmt){
    $error=true;
}
else if (!$stmt->bind_param("ii", $schueler_id, $a_id)){
    $error=true;
}
else{
    foreach ($schueler as $index => $schueler_id){
        if (!$stmt->execute()){
            $error=true;
            continue;
        }  
    }
    $stmt->close();
}


if ($error==true){
    $redirect_url.= '&error';
}

header('Location: '. $redirect_url);
$conn->close();

?>