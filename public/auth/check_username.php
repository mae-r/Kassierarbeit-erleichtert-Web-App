<?php
    //Gibt true zurück wenn der Benutzername schon existiert
    //Benutzer name wird in $_POST["username"] übergeben

    require("../../includes/funktionen.php");
    $conn = db_connect();
    $username = $_POST["username"];

    //sql string um das Passwort des Nutzers zu bekommen
    $stmt = $conn->prepare("SELECT username FROM benutzer where username = ?");
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
    if ($result->num_rows > 0){
        //Benutzername ist vorhanden
        echo("true");
    }
    else{
        //Benutzername ist nicht vorhanden.
        echo("false");
    }
?>