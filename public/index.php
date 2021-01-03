<?php
require("../includes/funktionen.php");
require("../auth/current_user.php");

$conn = db_connect();

//sql string um offene Aufträge zu bekommen
$sql="SELECT * FROM `auftraege` WHERE `kassier_id`=" . $current_user_id . " AND status=0";

$result = $conn->query($sql);

$auftraege = [];
while($row = $result->fetch_assoc()){
    //das Datum wird so gespeichert damit es vom Date-Picker gelesen werden kann.
    $erhalten_str = date("d.m.Y", strtotime($row["erhalten"]));

    $auftraege[$row["id"]] = array("titel" => $row["titel"], "preis" => $row["preis"], "erhalten" => $erhalten_str);
}
$conn->close();
?>

<html>
<head>
    <?php require("../includes/standart_head.php")?>
    <script>
        let auftraege_list = <?php echo json_encode($auftraege)?>
    </script>

    <script>
        function auftrag_abgeben(a_id){
            let modal_args = {
                title: "Auftrag abschliessen",
                form_action: "data_handler/edit_auftrag.php",
                form_elements: [
                    {type: "date", label: "Datum:", date:today, attr: {name:"abgegeben"}},
                    {type: "text", hidden: true, attr: {value: "abgeben", name: "action"}},
                    {type: "text", hidden: true, attr: {value: a_id,name: "a_id"}}              
                ],
                btn:{text: "Auftrag abschliessen"}
            };
            abgeben_modal.initialize(modal_args);
            abgeben_modal.show();
        }

        function edit_auftrag(a_id) {
            let modal_args = {
                title: "Auftrag bearbeiten",
                header_btn: [
                    {type: "delete", confirm: {action: "delete", text:"Auftrag mit Zahlungsaufträgen komplett löschen?"}}
                ],
                form_action: "data_handler/edit_auftrag.php",
                form_elements: [
                    {type: "text", label: "Titel:", attr: {name: "titel", value: auftraege_list[a_id]["titel"], readOnly: true}, edit_button: true},
                    {type: "text", label: "Preis:", attr: {name: "preis", value: auftraege_list[a_id]["preis"], type: "number", step: 0.05, readOnly: true}, group_addon:{text:"CHF"}, edit_button: true},
                    {type: "date", label: "Erhalten:", date: auftraege_list[a_id]["erhalten"], attr: {name:"erhalten", readOnly: true}, edit_button: true},
                    {type: "text", hidden: true, attr: {value: a_id,name: "a_id"}}              
                ],
                btn:{text: "Änderungen bestätigen"}
            };
            edit_modal.initialize(modal_args);
            edit_modal.show();
        }
    </script>

    <title>Offene Aufträge</title>
</head>
<body>

<div class="content">
    <div class="container">
        <br>
        <h1 class="text-center">Offene Aufträge</h1>
        <br>

        <?php 
        //wenn ein Error von data_handler/.. gesendet wird
        if (isset($_GET["error"])){
            echo '<div class="text-center col-12"><p class="form-text text-danger font-weight-bold">Es ist ein Fehler aufgetreten.</p></div>';
        }

        ?>    

    <div class="row">
        <div class="col text-center">
            <a class="text-muted" onclick="add_modal.show()"><i class="fas fa-2x fa-plus-circle"></i>
        </div>
    </div>
    <br>
    <!-- Auflistung der offenen Auträge -->
    <?php foreach ($auftraege as $id => $auftrag){
        echo '<div class="m-3">
                    <div class="border container round">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row align-items-center">
                                <div class="col-lg-5 col-md-6">
                                    <a class="text-muted text-break" href="auftrag.php?id='.$id.'">
                                        <h5 class="mt-3">'.$auftrag["titel"].'</h5>
                                        <p class="text"><small>'.$auftrag["preis"].' CHF</small></p>
                                    </a>
                                </div>
                                <div class="col">
                                    <p class=""><small>Erhalten: '.$auftrag["erhalten"].'</small></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-right">
                            <button type="button" class="btn btn-light border mb-1" onclick="auftrag_abgeben('.$id.')"><i class="text-success fas fa-check"></i></button>
                            <br>
                            <button type="button" class="btn btn-light border" onclick="edit_auftrag('.$id.')"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                    </div>
                </div>
            </div>';
    } ?>
        
        <div class="text-center pt-5">
            <a class="btn text-muted" href="auftraege_abgeschlossen.php"><h5>abgeschlossene Aufträge <i class="fas fa-angle-right"></i></h5></a>
        </div>

        
    </div>
</div>
<?php include("../includes/navigation.php")?>

<script>
edit_modal = new modaljs();
add_modal = new modaljs();
abgeben_modal = new modaljs();

let modal_args = {
    title: "Neuer Auftrag",
    form_action: "data_handler/create_auftrag.php",
    form_elements: [        
        {type: "text", label: "Titel:", attr: {name: "titel", required: true}},
        {type: "text", label: "Preis:", attr: {name: "preis", type: "number", step:0.05, required: true}, group_addon:{text:"CHF",type:"append"}},
        {type: "date", label: "Erhalten:", date: today, attr: {name:"erhalten"}}
    ],
    btn:{text: "Neuen Auftrag erfassen"}
};
add_modal.initialize(modal_args);

</script>

</body>
</html>