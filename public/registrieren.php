<html>
<head>
    <?php require("../includes/standart_head.php")?>
    <script>

        //Es wird eine Anfrage geschickt ob der Benutzername noch verfügbar ist
        function check_username(){
         
            var xhttp = new XMLHttpRequest();
            var url = 'auth/check_username.php';
            var params = 'username='+$("#username").val();
            xhttp.open('POST', url, true);
            
            //richtiger Header muss gesetzt werden
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhttp.onreadystatechange = function() {
                if(xhttp.readyState == 4 && xhttp.status == 200) {
                    if (xhttp.responseText == "false"){
                        //Wenn Benutzername noch nicht existiert
                        $("#username_warning").hide();
                    }
                    else{
                        $("#username_warning").show();
                    }
                    check_state();
                }
            }
            xhttp.send(params);
        }

        //schaut ob das Passwort genügend lang ist
        function check_length(len){
            let pw1 = $("#pw1").val();
            
            if (pw1.length < len){
                $("#pw1_warning").show();
            }
            else{
                $("#pw1_warning").hide();
            }
            check_state();
        }

        //schaut ob passwort richtig wiederholt wurde
        function compare_pwd(){
            if (($("#pw1").val() != $("#pw2").val())){
                //Nur wenn das Feld auch nicht leer ist
                if ($("#pw2").val() != ""){
                    $("#pw2_warning").show();
                }
            }
            else{
                $("#pw2_warning").hide();
            }
            
            check_state();
        }

        //Schaut ob Form abgesendet werden darf
        function check_state(){
            //schaut ob der Warnhinweis versteckt ist
            function hd(id){
                if ($("#"+id).is(":hidden")){
                    return true;
                }
                return false;
            }

            let submit = document.getElementById("submit");
            if (hd("username_warning") && hd("pw1_warning") && hd("pw2_warning")){
                submit.disabled = false;
            }
            else{
                submit.disabled = true;
            }
        }

    </script>
    <title>Registrieren</title>
</head>
<body>
<div class="d-flex align-items-center min-vh-100">
<div class="container w-100">
    <div class="card jumbotron text-center m-4">
    <h2>Registrieren</h3>
    <?php 
        //bei ungültigem Benutzer wird diese Meldung angezeigt
        if (isset($_GET["error"])){
            echo ('<small class="form-text text-danger font-weight-bold">Es ist ein Fehler aufgetreten</small>');
        }
    ?>
    <form method="POST" action="auth/registrieren.php">

        <div class="form-group my-4">
            <label>Name:</label>
            <input class="form-control" type="text" name="name" required="">
        </div>
        
        <div class="form-group my-4">
            <label>Benutzername:</label>
            <input class="form-control" type="text" name="username" id="username" onchange="check_username()" required="">
            <!-- bei ungültigem Benutzername wird diese Meldung angezeigt -->
            <small id="username_warning" style="display: none" class="form-text text-danger font-weight-bold">Dieser Benutzername ist schon vergeben</small>
            
        </div>

        <div class="form-group my-4">
            <label class="">Passwort:</label>
            <input class="form-control" type="password" name="password" id="pw1" onchange="check_length(6);compare_pwd();" required="">
            <!-- Wenn Passwort zu kurz -->
            <small id="pw1_warning" style="display: none" class="form-text text-danger font-weight-bold">Passwort muss 6 Zeichen lang sein</small>
        </div>

        <div class="form-group my-4">
            <label class="">Passwort wiederholen:</label>
            <input class="form-control" type="password" id="pw2" onchange="compare_pwd()" required="">
            <!--stimmen die Passwörter nicht überein wird diese Meldung angezeigt -->
            <small id="pw2_warning" style="display: none" class="form-text text-danger font-weight-bold">Passwörter stimmen nicht überein</small>
        </div>        
        <button id="submit" type="submit" class="btn btn-primary">Registrieren</button>
    
    </form>

    <div class="text-right mr-3">
        <a class="text-muted" href="login.php">Zum Login <i class="fas fa-angle-right"></i></a>
    </div>   

    </div>
</div>
</div>


</body>
</html>