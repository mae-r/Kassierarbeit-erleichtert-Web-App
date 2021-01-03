<?php 
require("../includes/funktionen.php");
require("../auth/current_user.php");

$conn = db_connect();

//sql string um Schüler zu bekommen
$sql = "SELECT schulklassen.*, benutzer.name, benutzer.type
        FROM schulklassen
        LEFT JOIN benutzer
        ON schulklassen.schueler_id = benutzer.id 
        WHERE kassier_id =" . $current_user_id;

$result = $conn->query($sql);

//array wird später für Erstellung Html-Code verwendet
$meine_klasse = [];

while($row = $result->fetch_assoc()){
    $meine_klasse[] = array("id" => $row["id"],"name" => $row["name"], "type" => $row["type"]);
}

$conn->close();
?>



<html>
<head>
    <?php require("../includes/standart_head.php")?>

    <script>
        function delete_click(btn) {
            btn.parentNode.parentNode.style = "display: none";
            
            let input = btn.parentNode.parentNode.children[0].children[0];
            input.disabled = false;
            
            //wenn der name schüler_name[...] ist
            if (/^schueler_name[[0-9]*]$/.test(input.name)){
                let id = input.name.match(/[0-9]+/);
                input.name = "delete_schueler["+ id +"]";
                input.value = id;
            }
            else {
                //Der Wert wird nicht gesendet wenn disabled 
                //Fall das dieser Schüler gerade erst erstellt wurde
                input.disabled=true;
            }
        }

        function edit_click(btn) {
            let input = btn.parentNode.parentNode.children[0].children[0];
            input.disabled = false;
            input.readOnly = false;
            input.focus();
            btn.style = "display: none";
        }

        function add_input(list_id,name) {
            let all_schueler = document.getElementById(list_id);
            
            let new_input = create_element("div","row mt-2 mb-2");
            all_schueler.appendChild(new_input);
            new_input.innerHTML += '<div class="col"><input class="form-control-sm form-control" type="text" name="'+name+'"></div>'
            + '<div class="col-auto"><button class="btn btn-light" type="button" onclick="delete_click(this)"><i class="fas fa-trash-alt"></i></button></div></div>'; //HTML-Code für das INPut-Feld
            
            //Das neue Feld wird fokusiert
            new_input.children[0].children[0].focus();
        }

    </script>
    <title>Klasse</title>
</head>
<body>

<div class="content">
    <div class="container">
        <br>
        <h1 class="text-center">Klasse</h1>
        <br>

        <?php 
        //wenn ein Error von data_handler/.. gesendet wird
        if (isset($_GET["error"])){
            echo '<div class="text-center col-12"><p class="form-text text-danger font-weight-bold">Es ist ein Fehler aufgetreten.</p></div><br>';
        }

        ?> 

    <h4 class="text-center">Meine Klasse<a class="text-muted ml-2" data-toggle="collapse" data-target="#collapseKlasse" onclick="let icon = this.children[0]; let collapse=document.getElementById('collapseKlasse').className; if (collapse.includes('show')){icon.className='fas fa-angle-down';} else if (!collapse.includes('collapsing')){icon.className='fas fa-angle-up';}"><i class="fas fa-angle-down"></i></a></h4>
    
    <div id="collapseKlasse" class="collapse">
      <div class="card-body text-center">
        <div class="border round p-4">
            <form class="mx-auto" action="data_handler/meine_klasse.php" method="POST" style="max-width: 40rem">
                <div id="schueler">
                <?php
                //für jeden Schüler wird ein Feld mit seinem Namen und zwei dazugehörige Buttons erstellt
                foreach ($meine_klasse as $schueler){
                    echo '<div class="row mt-2 mb-2">
                    <div class="col"><input class="form-control-sm form-control" type="text" name="schueler_name['.$schueler["id"].']" disabled="true" value="'.$schueler["name"].'"></div>
                    <div class="col-auto">
                        <button class="btn btn-light" type="button" onclick="edit_click(this)"><i class="fas fa-pencil-alt"></i></button>';
                    if ($schueler["type"] == 2){
                        //nur wenn der Benutzer selber kein Kassier ist, kann er gelöscht werden
                        echo '<button class="btn btn-light" type="button" onclick="delete_click(this)"><i class="fas fa-trash-alt"></i></button>';
                    }
                    echo'</div></div>';
                }
                
                ?> 
                </div>
                <div class="text-center mt-4 mb-2">
                    <button class="btn btn-light" type="button" onclick="add_input('schueler','new_schueler[]')"><i class="fas fa-plus-circle fa-lg"></i></button>
                </div>

                <div class="text-right mt-4 mb-2">
                    <button class="btn btn-primary" type="submit">Änderungen speichern</button>
                </div>

            </form>
        </div>
      </div>
    
    
    
    </div>
 </div>   


    
</div>
<?php include("../includes/navigation.php")?>
<script>
klasse_modal = new modaljs();
</script>
</body>
</html>