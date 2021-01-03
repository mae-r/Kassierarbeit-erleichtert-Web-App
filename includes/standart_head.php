<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
<!-- Bootstrap core CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">

<!-- jQuery & bootstrap js-->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

<!-- Datepicker plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.de.min.js" integrity="sha512-3V4cUR2MLZNeqi+4bPuXnotN7VESQC2ynlNH/fUljXZiQk1BGowTqO5O2gElABNMIXzzpYg5d8DxNoXKlM210w==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous">

<!-- Icon für IOS; da Icon über manifest.json nicht untersütz wird-->
<link rel="apple-touch-icon" sizes="980x980" href="img/icons/icon980x980.png">

<!-- Manifest für die Web-App -->
<link rel="manifest" href="manifest.json">

<link rel="stylesheet" href="css/style.css">

<!-- Geschrieben Funktionen für das Modal-->
<script src="js/modal.js"></script>

<script>
    //hier ist das heutige Datum für den Datepicker gespeichert
    var date = new Date();
    //es wird nur das Datum, ohne die Zeit gebraucht
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    
    $.fn.datepicker.defaults.format = "dd.mm.yyyy";
    $.fn.datepicker.defaults.todayHighlight = true;
    $.fn.datepicker.defaults.autoclose = true;
    $.fn.datepicker.defaults.language = "de-DE";
    $.fn.datepicker.defaults.disableTouchKeyboard = true;
    $.fn.datepicker.defaults.enableOnReadonly = false;
</script>