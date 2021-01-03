<?php 
require("../includes/funktionen.php");
require("../auth/current_user.php");

$conn = db_connect();

//die id muss eine nr sein
$current_auftrag_id = (isset($_GET["id"])) ? $_GET["id"] : 0;
if (!ctype_digit($current_auftrag_id)){
  $current_auftrag_id=0;
}

//sql string um Auftrag zu bekommen
$sql = "SELECT * FROM `auftraege` WHERE `id`=" . $current_auftrag_id . " AND `kassier_id`=".$current_user_id;  //prepared Statement nicht nötig da die Auftrag Id eine Nummer ist
$result = $conn->query($sql);

//wenn dieser Auftrag nicht gefunden wird, wird der Benutzer zurück zur Auwahl aller Aufträge geleitet.
if(mysqli_num_rows($result) == 0){
  header('Location: ./');
  die("Diesen Auftrag gibt es nicht");
}

$row = $result->fetch_assoc();
$auftrag = $row["titel"];
$preis = $row["preis"];
//Auf der Seite steht der Preis in CHF
$preis_txt = $preis . " CHF";

$current_auftrag_status = $row["status"];


//sql string um Zahlungsaufträge zu bekommen und den dazugehörigen Namen des Benutzers
$sql = "SELECT zahlungsauftraege.*, benutzer.name
        FROM zahlungsauftraege
        LEFT JOIN benutzer
        ON zahlungsauftraege.schueler_id = benutzer.id 
        WHERE auftrag_id = " . $current_auftrag_id;

$result = $conn->query($sql);

//aus dem Resultat werden zwei htmlcode Strings generiert (offen und abgeschlossenen Zahlungsaufträge)
$zas_offen = [];
$zas_abg = [];

//array for js
$zahlungsauftraege=[];

while($row = $result->fetch_assoc()){
  //Fall: Benutzer existiert nicht
  if($row["name"]===NULL){
    $row["name"] = "gelöschter Benutzer";
    //nicht für modal
    $name = "<i>gelöschter Benutzer</i>";
  }
  else{
    $name = $row["name"];
  }

  $id = $row["id"];
  $status = $row["status"];
  //das Datum wird so gespeichert damit es vom Date-Picker gelesen werden kann.
  $date_str = date("d-m-Y", strtotime($row["datum"]));

  
  //für js
  $zahlungsauftraege[$id]["name"] = $row["name"];
  $zahlungsauftraege[$id]["zahlungsmittel"] = $row["zahlungsmittel"];
  $zahlungsauftraege[$id]["date"] = $date_str;

  //wenn status 0: zahlungsauftrag ist offen
  if ($status== 0){
    $zas_offen[] = array("id" => $id, "name" => $name);
  }
  
  //wenn status 1: zahlungsauftrag ist abgeschlossen
  else if ($status == 1){
    $zas_abg[] = array("id" => $id,"name" => $name);
  }
}

$zas_offen_total = count($zas_offen);
$zas_abg_total = count($zas_abg);
//Der schon bezahlte und der Gesammtbetrag wird berechnet
$bezahlt_geld = $zas_abg_total * $preis;
$total_geld = $bezahlt_geld + $zas_offen_total * $preis;




//Dieser Teil ist für das hinzufügen von Zahlungsaufträgen zuständig
//sql string um Schüler zu bekommen
$sql = "SELECT schulklassen.*, benutzer.name, benutzer.id as benutzer_id
        FROM benutzer
        INNER JOIN schulklassen
        ON benutzer.id = schulklassen.schueler_id
        WHERE kassier_id = " . $current_user_id;

$result = $conn->query($sql);

//aus dem Resultat wird eine Liste mit allen Schülern gemacht
$schueler = [];

while($row = $result->fetch_assoc()){
  array_push($schueler,array("id" => $row["benutzer_id"],"name" => $row["name"]));
}



$conn->close();
?>











<html>
<head>
    <?php require("../includes/standart_head.php")?>

    <script>
      //Eine Liste mit allen Schülern dieses Kassiers und allen Zahlungsauftraegen wird ausgegeben.
      let zahlungsauftraege_list = <?php echo(json_encode($zahlungsauftraege))?>;
      let schueler_list = <?php echo(json_encode($schueler))?>;
      
    </script>

    <script>
      let standart_form_elements = [
              {type: "text", hidden: true, attr: {value: <?php echo $current_auftrag_id?>, name: "a_id"}},
              {type: "text", label: "Für:", attr: {value: "<?php echo $auftrag?>", readOnly: true}},
              {type: "text", label: "Preis:", attr:{value: "<?php echo $preis_txt?>", readOnly: true}}
          ]
      let standart_zahlungsmittel = [
        {text: "Bar"},{text: "TWINT"}];

      function neue_Zahlung(za_id) {
        let modal_args = {
          title: "Neue Zahlung",
          header_btn: [
            {type: "delete", confirm: {action: "delete", text:"Zahlungsauftrag komplett löschen?"}}
          ],
          form_action: "data_handler/zahlung.php",
          form_elements: standart_form_elements.concat([
              {type: "text", label: "Name:", attr: {value: zahlungsauftraege_list[za_id]["name"], readOnly: true}},
              {type: "dropdown", label: "Zahlungs-mittel:", attr: {name: "zahlungsmittel"}, options: standart_zahlungsmittel},
              {type: "date", label: "Datum:", date: today, attr: {name:"date"}},
              {type: "text", hidden: true, attr: {value: za_id,name: "za_id"}}              
          ]),
          btn:{text: "Zahlung bestätigen"}

        };
        modal.initialize(modal_args);
        modal.show();
      }
      //öffnet modal mit den Informationen zu einer erfolgten Zahlung
      function Zahlung(za_id) {
        let modal_args = {
          title: "Zahlung",
          header_btn: [
            {type: "undo", confirm: {action: "reset", text: "Zahlung als unbezahlt speichern?"}} , {type: "delete", confirm: {action: "delete", text:"Zahlungsauftrag komplett löschen?"}}
          ],
          form_action: "data_handler/zahlung.php",
          form_elements: standart_form_elements.concat([
              {type: "text", label: "Name:", attr: {value: zahlungsauftraege_list[za_id]["name"], readOnly: true}},
              {type: "dropdown", label: "Zahlungs-mittel:", attr: {name: "zahlungsmittel", value: zahlungsauftraege_list[za_id]["zahlungsmittel"], disabled: true}, options: standart_zahlungsmittel, edit_button: true},
              {type: "date", label: "Datum:", date: zahlungsauftraege_list[za_id]["date"], attr: {name:"date", readOnly: true}, edit_button: true},
              {type: "text", hidden: true, attr: {value: za_id,name: "za_id"}},              
          ]),
          btn:{text: "Änderungen speichern"}

        };
        modal.initialize(modal_args);
        modal.show();
      }
    </script>
    <title><?php echo $auftrag?></title>
</head>
<body>



<div class="content">
<div class="container">
    <br>  
    <h3 class="text-center"><?php echo $auftrag?></h3>  
    <br>
    
    <!-- Beschreibung des Artikels -->

    <div class="row">
      <?php 
      //wenn ein Error von data_handler/zahlung.php gesendet wird
      if (isset($_GET["error"])){
        echo '<div class="text-center col-12"><p class="form-text text-danger font-weight-bold">Es ist ein Fehler aufgetreten.</p></div>';
      }

      //Meldung fals Auftrag abgeschlossen ist
      if ($current_auftrag_status == 1){
        echo '<div class="text-center col-12"><p class="form-text text-danger font-weight-bold">Dieser Auftrag ist abgeschlossen.</p></div>';
      }
      ?>
      <div class="col text-left">
        <p>Preis:  <?php echo $preis_txt?></p>
      </div>

      <!-- Button um neue Zahlungsaufträge hinzuzufügen -->
      <div class="col text-right">
        <button class="btn btn-light" type="button" onclick="new_za_modal.show()"><i class="fas fa-plus"></i></button>
      </div>
      </div>
    
    <!-- Statusanzeige -->
    <div class="progress big" style="height: 2rem">
      <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo ($bezahlt_geld/$total_geld*100) ?>%"><?php echo ("CHF ".$bezahlt_geld."/".$total_geld) ?></div>
    </div>        
    <br>
    
    <!-- Liste der offenen Zahlungsaufträgen-->
    <h5>Offen: <?php echo $zas_offen_total?>/<?php echo ($zas_abg_total+$zas_offen_total)?></h5> 
    <ul class="list-group">
    <?php 
        foreach ($zas_offen as $za){
          echo "<li class='list-group-item list-group-item-action' onclick='neue_Zahlung(".$za["id"].")'>".$za["name"]."</li>";
        }
    ?>
    </ul>

    <br>
    
    <!-- Liste der abgeschlossenen Zahlungsaufträgen-->
    <h5>Bezahlt: <?php echo $zas_abg_total?>/<?php echo ($zas_abg_total+$zas_offen_total)?></h5> 
    <ul class="list-group">
    <?php 
        foreach ($zas_abg as $za){
          echo "<li class='list-group-item list-group-item-action' onclick='Zahlung(".$za["id"].")'>".$za["name"]."</li>";
        }
    ?>
    </ul>

</div>
</div>

<?php include("../includes/navigation.php") ?>

<script>
modal = new modaljs();
new_za_modal = new modaljs();

//Checkboxen für alle Schüler
let schueler_form_elements = [];
for (id in schueler_list){
  let schueler = schueler_list[id];
  schueler_form_elements.push({type: "checkbox", check: {name:"schueler[]", value:schueler["id"], className:"new_za_checkbox"}, check_label:{textContent: schueler["name"], className:"form-control-sm form-control", readOnly: true} })
}

let modal_args = {
  title: "Zahlungsaufträge hinzufügen für:",
  form_action: "data_handler/neue_zahlungsauftraege.php",
  classNames:{form_row:"form-row mt-2 mb-2"},
  form_elements: [
      {type: "text", hidden: true, attr: {value: <?php echo $current_auftrag_id?>, name: "a_id"}},
      {type: "checkbox", check:{id:"select_all_schueler"}, check_label:{textContent: "Alle Auswählen", className:"font-weight-bold mb-2"} }
      //{type: "text", label: "", attr: {value: name, readOnly: true,className:"form-control-sm"}},
      //{type: "text", label: "Datum:", attr: {type:"checkbox", name:"checked[marr]", className:"form-check-input"}},

  ].concat(schueler_form_elements),
  btn:{text: "Zahlungsaufträge hinzufügen"}
};
new_za_modal.initialize(modal_args);

document.getElementById("select_all_schueler").addEventListener("click", () => {
  let state = document.getElementById("select_all_schueler").checked;
  let checkboxes = document.getElementsByClassName("new_za_checkbox");
  for(var i = 0; i < checkboxes.length; i++)
  {
    checkboxes[i].checked=state;;
  }
  checkbox_clicked();
}); 

//wenn eine checkbox ungechecked wird, muss die Checkbox um alle auszuwählen auch unchecked werden
function checkbox_clicked(){
  let select_all = document.getElementById("select_all_schueler");
  //alle Checkboxen werden auf ihren status überprüft
  let tot_checked = 0;
  let tot_unchecked = 0;
  let checkboxes = document.getElementsByClassName("new_za_checkbox");
  for(var i = 0; i < checkboxes.length; i++)
  {
    if (checkboxes[i].checked){
      tot_checked ++;
    }
    else{
      tot_unchecked ++;
    }
  }
  if (tot_unchecked == 0){
    select_all.checked = true;
  }
  else {
    select_all.checked = false;
  }

  //Die Anzahl der Aufträge, welche hinzugefügt wird, wird auf dem Bestätigen Button angezeigt
  new_za_modal.footer.children[0].textContent = tot_checked + " " + modal_args.btn.text;

}
let checkboxes = document.getElementsByClassName("new_za_checkbox");
for(var i = 0; i < checkboxes.length; i++)
{
  checkboxes[i].addEventListener("click", checkbox_clicked);
}



<?php
//Wenn noch keine Zahlunsaufträge vorhanden sind öffnet sich das Fenster um welche hinzuzufügen
if ($zas_offen_total+$zas_abg_total == 0){
  echo("new_za_modal.show();");
}

?>
</script>
</body>
</html>