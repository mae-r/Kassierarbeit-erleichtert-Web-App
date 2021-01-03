<?php 
//algemeine Cookie Einstellungen
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
function db_connect()
{   
    //Infomationen zur Datenbank müssen hier bearbeitet werden.
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $db = "maturarbeit";
    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
    
    return $conn;
}

?>