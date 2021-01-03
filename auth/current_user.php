<?php 
session_start();
if (!isset($_SESSION["user_id"])){
    //Der URL auf welchen der User später geleitet wird wird bestimmt.
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        //Verbindung erfolgt über https
        $url_host = "https://";   
    else  
        //kein https also http
        $url_host = "http://";   
    $url_host.= $_SERVER['HTTP_HOST'];   
    $url= $url_begin . $_SERVER['REQUEST_URI']; 
    
    header('Location: '.$url_host.'/login.php?url='.$url);
    die();
}
else{
    // $current_user_id kann von den Script jetzt verwendet werden.
    $current_user_id = $_SESSION["user_id"];
}
?>