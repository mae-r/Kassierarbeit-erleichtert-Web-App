<?php
require("../../includes/funktionen.php");
require("../../auth/current_user.php");
$conn = db_connect();
//der User wird wieder zurückgeleitet.
$redirect_url = "../klasse.php";

//Falls nicht exisitert -> leeres Array
$change_schueler = (isset($_POST["schueler_name"])) ? $_POST["schueler_name"] : [];
$new_schueler = (isset($_POST["new_schueler"])) ? $_POST["new_schueler"] : [];
$delete_schueler = (isset($_POST["delete_schueler"])) ? $_POST["delete_schueler"] : [];

//Falls ein Error auftritt wird diese Variable auf true geschaltet
$error=false;

//für jede Liste wird einmal das Statement definiert und dann für jeden Schüler in der jeweiligen Liste ausgeführt

//Es werden alle schueler welche gelöscht werden sollen, gelöscht
if(sizeof($delete_schueler) > 0){
    //Statement um einen Schüler zu löschen
    $stmt = $conn->prepare("DELETE benutzer
                            FROM benutzer
                            INNER JOIN schulklassen
                            ON benutzer.id = schulklassen.schueler_id
                            WHERE schulklassen.id = ? AND kassier_id = ".$current_user_id); //AND kassier_id damit nicht belibeige User gelöscht werden können
    
    $stmt2 = $conn->prepare("DELETE FROM schulklassen WHERE id = ? AND kassier_id = ".$current_user_id);
    if ($stmt === false || $stmt2 === false){
        $error=true;
        
    }
    else if (($stmt->bind_param("i", $schueler_id) === false) || ($stmt2->bind_param("i", $schueler_id) === false) ){
        $error=true;
    }
    else{
        //Statement wird für alle Schüler ausgeführt
        foreach ($delete_schueler as $schueler){
            $schueler_id = $schueler;
            if (!$stmt->execute() || !$stmt2->execute() ){
                $error=true;
            }  
        }
        $stmt->close();
        $stmt2->close();
    }
    
}

//Es werden alle schueler welche hinzugefügt werden sollen, hinzugefügt
if(sizeof($new_schueler) > 0){
    $stmt = $conn->prepare("INSERT INTO benutzer (`name`,`type`) VALUES (?,2)");
    $stmt2 = $conn->prepare("INSERT INTO schulklassen (`kassier_id`,`schueler_id`) VALUES (".$current_user_id.", LAST_INSERT_ID() )");
    if ($stmt === false || $stmt2 === false){
        $error=true;
    }
    else if ($stmt->bind_param("s", $schueler_name) === false){
        $error=true;
    }
    else{
        foreach ($new_schueler as $schueler){
            $schueler_name = htmlspecialchars($schueler);
            //Benutzer ohne Namen werden nicht erstellt
            if ($schueler_name==""){
                continue;
            }
            if (!$stmt->execute() || !$stmt2->execute() ){
                $error=true;
            }  
        }
        $stmt->close();
        $stmt2->close();
    }
}

//Änderung der Namen der Schüler
if(sizeof($change_schueler) > 0){
    $stmt = $conn->prepare("UPDATE benutzer
                            INNER JOIN schulklassen
                            ON benutzer.id = schulklassen.schueler_id
                            SET benutzer.name = ?
                            WHERE schulklassen.id = ? AND kassier_id = ".$current_user_id); //AND kassier_id damit nicht belibeige User geändert werden können

    if ($stmt === false){
        $error=true;
    }
    else if ($stmt->bind_param("si", $schueler_name, $schueler_id) === false){
        $error=true;
    }
    else{
        foreach ($change_schueler as $id => $schueler){
            $schueler_name = htmlspecialchars($schueler);
            $schueler_id = $id;
            if (!$stmt->execute()){
                $error=true;
                continue;
            }  
        }
        $stmt->close();
    }
    
}

if ($error==true){
    $redirect_url.= '?error';
}
header('Location: '. $redirect_url);
$conn->close();

?>