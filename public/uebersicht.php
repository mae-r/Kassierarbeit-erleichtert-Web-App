<?php
require("../includes/funktionen.php");
require("../auth/current_user.php");

$conn = db_connect();

//sql string um alle aufträge und dazugehörige za zu bekommen
$sql = "SELECT a.*, za.* 
        FROM auftraege a
        INNER JOIN zahlungsauftraege za
        ON a.id = za.auftrag_id 
        WHERE a.status=0 AND za.status=1 AND a.kassier_id =" . $current_user_id; //Auftrag darf nicht abgeschlossen sein, aber Zahlungsauftrag muss abgeschlossen sein

$result = $conn->query($sql);
$zahlungsmittel = array("Bar","TWINT");

$auftraege = [];  //Informationen über alle Auftraege
$geld; //Unterteilung nach Zahlungsmittel
$geld_total; //Unterteilung der Totals
$geld_total["total"] = 0; //Gesamtbetrag
foreach ($zahlungsmittel as $zm){
    $geld[$zm]= [];
    $geld_total["zm"][$zm]=0; //unterteilung total nach Zahlunsmittel
}
while($row = $result->fetch_assoc()){
    $preis= $row["preis"];
    $a_id = $row["auftrag_id"];
    if (!isset($auftraege[$a_id])){
        $auftraege[$a_id] = array("preis" => $preis,"titel" => $row["titel"]);
        $geld_total["a"][$a_id] =0; //Unterteilung Total nach Zahlungsauftrag
        foreach ($zahlungsmittel as $zm){
            $geld[$zm][$a_id] = 0;
        }
    }
    //Alle totale werden um den Preis erhöht
    $geld[$row["zahlungsmittel"]][$a_id] += $preis;
    $geld_total["zm"][$row["zahlungsmittel"]] += $preis;
    $geld_total["a"][$a_id] +=$preis;
    $geld_total["total"] += $preis;

}
$conn->close();
?>





<html>
<head>
    <?php require("../includes/standart_head.php")?>

    <script>
    //vesteckt oder zeigt alle Elemente mit einem angegebenen ClassName an
    function change_column(className,show){
        let style = "display: none";
        if (show){
            style="";
        }
        let cells = document.getElementsByClassName(className);
        for (let i in cells){
            cells[i].style=style;
        }

    }

    function checkbox_clicked(box){
        //wen box angewählt zeige alle jewiligen elemente
        if (box.checked){
            change_column(box.id,true);
        }
        else{
            change_column(box.id,false);
        }
    }
    </script>

    <title>Übersicht</title>
</head>
<body>

<div class="content">
    <div class="container">
        <br>
        <h1 class="text-center">Übersicht Geld</h1>
        <br>

        <div class="row align-items-center">
            <div class="col-auto my-4">
                <a class="text-muted ml-2" data-toggle="collapse" data-target="#collapseZahlungsmittel"><i class="fas fa-filter"></i></a>
            </div>
            <div class="col">
                <div id="collapseZahlungsmittel" class="collapse">
                    <div class="card-body text-center py-2">
                        <div class="border round p-2 row">
                        <?php 
                        foreach ($zahlungsmittel as $zm){
                            //Auswahlbox für jedes Zahlungsmittel
                            echo '<div class="col-auto">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" onclick="checkbox_clicked(this)" id="'.$zm.'">
                                    <label class="custom-control-label" for="'.$zm.'">'.$zm.'</label>
                                </div>
                            </div>';
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-sm round" id="table">
            <thead>
                <tr>
                <th></th>
                <th scope="col"><h5>Total:</h5></th>
                <?php 
                foreach ($zahlungsmittel as $zm){
                    //Spaltentitel für jedes Zahlungsmittel
                    echo '<th style="display:none" scope="col" class="'.$zm.'" ><h5>'.$zm.'</h5></th>';
                }
                ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                <th scope="row"><h5>Total:</h5></th>
                <th scope="row"><h5><?php echo $geld_total["total"]?></h5></th>

                <?php foreach ($geld_total["zm"] as $zm=>$total)
                {
                    //Total für jedes Zahlungsmittel
                    echo '<th style="display:none" scope="row" class="'.$zm.'" ><h5>'.$total.'</h5></th>';
                }
                ?>
                </tr>
            </tbody>
            <tbody>
            <?php
                foreach ($auftraege as $id=>$info){
                    echo '<tr>
                    <td><a class="text-muted" href="auftrag.php?id='.$id.'">'.$info["titel"].'</a></td>
                    <th scope="row">'.$geld_total["a"][$id].'</th>';
                    foreach ($zahlungsmittel as $zm){
                        echo '<th style="display: none"class="'.$zm.'">'.$geld[$zm][$id].'</th>';
                    }
                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>

    </div>
</div>

<?php include("../includes/navigation.php")?>


</body>
</html>