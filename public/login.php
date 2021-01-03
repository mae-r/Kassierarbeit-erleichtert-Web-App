<html>
<head>
    <?php require("../includes/standart_head.php")?>
    <title>Login</title>
</head>
<body>
<!-- Alles wird zentriert!-->
<div class="d-flex align-items-center min-vh-100">
<!-- kein Horizontaler Scrollbalken darum w-100 -->
<div class="container w-100">
    <div class="card jumbotron text-center m-4">
    <h2>Login</h2>

    <?php 
        //Wenn ein neuer Benutzer sich erfolgreich registriert hat
        if (isset($_GET["new_user"])){
            echo ('<small class="form-text text-success font-weight-bold">Registrierung war erfolgreich.</small>');
        }
    ?>

    <form method="POST" action="auth/login.php">

        <div class="form-group my-4">
            <label>Benutzername:</label>
            <input class="form-control" type="text" name="username" required="">
            <?php 
                //bei ungültigem Benutzer wird diese Meldung angezeigt
                if (isset($_GET["wrong_username"])){
                    echo ('<small class="form-text text-danger font-weight-bold">Dieser Benutzer existiert nicht</small>');
                }
            ?>
        </div>

        <div class="form-group my-4">
            <label class="">Passwort:</label>
            <input class="form-control" type="password" name="password" required="">
            <?php 
                //bei falschem Passwort wird diese Meldung angezeigt
                if (isset($_GET["wrong_password"])){
                    echo ('<small class="form-text text-danger font-weight-bold">Falsches Passwort</small>');
                }
            ?>
        </div>

        <?php 
            if (isset($_GET["url"])){
                //der url auf welchen der User geleitet werden soll, wird in das Form eingetragen
                echo ('<input type="text" name="url" style="display:none" value="'.$_GET["url"].'">');
            }
        ?>
        
        <button type="submit" class="btn btn-primary">Login</button>
    
    </form>

    <div class="text-right">
        <a class="text-muted mr-3" href="registrieren.php">Registrieren <i class="fas fa-angle-right"></i></a>
    </div>

    </div>

</div>
</div>


</body>
</html>