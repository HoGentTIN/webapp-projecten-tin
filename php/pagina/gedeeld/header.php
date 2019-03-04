<?php
    include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';

    // Kijken of er een sessiecontroller is voor deze pagina
    if(!isset($sessie_controller)) { $sessie_controller = new SessieController(); }
    // Kijken of we terug gaan naar zijn startpagina (btn-start)
    if(isset($_POST['start'])) { $sessie_controller->ga_naar_startpagina(); }
    // Indien fout of succes via url werd meegegeven deze opvragen
    if(isset($_GET['fout'])) { $fout = $_GET['fout']; }
    if(isset($_GET['succes'])) { $succes = $_GET['succes']; }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?php echo $_SERVER['SRV_ALIAS'] ?>/vendor/driftyco/ionicons/css/ionicons.css" rel="stylesheet">
    <link href="<?php echo $_SERVER['SRV_ALIAS'] ?>/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $_SERVER['SRV_ALIAS'] ?>/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="<?php echo $_SERVER['SRV_ALIAS'] ?>/css/style.css" rel="stylesheet">
    <title> <?php echo $_GET['pagina_titel'] ?> </title>
</head>
<body>
<?php
// navigatie tonen indien aangemeld
if ($sessie_controller->is_aangemeld()) {
    include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/navigatie.php';
}
?>
<!-- eigenlijke inhoud pagina starten -->
<div class="container col-10">
    <div class="row justify-content-center">
        <!-- afhankelijk van feit of we aangemeld zijn of niet, meer witruimte aan de zijden laten -->
        <?php
            if ($sessie_controller->is_aangemeld()) {
                echo '<div class="form-container col-12">';
            }
            else {
                echo '<div class="form-container col-8">';
            }
        ?>
            <form id="form" method="post" action="#"
                <?php
                // als uploadpagina is extra fromulierattributen
                if(isset($_GET['pagina_uploads'])){
                    echo  'action="' . $_SERVER["PHP_SELF"] . '" enctype="multipart/form-data" ';
                }
                // form tag afsluiten
                echo '>';
                ?>
                <!-- verborgen invoerveld om via javascript het type van form actie te kunnen manipuleren
                     Dit laat ons toe een POST var in te stellen via javascript -->
                <input type="hidden" id="form-actie" name="actie" value="">
                <input type="hidden" id="form-rij-id" name="rij-id" value="">
                <?php
                    // standaard de eerste 10 elementen tonen
                    if(!isset($aantal_elementen_per_pagina)) { $aantal_elementen_per_pagina = 10; }
                    // de waarde van de huidige pagina bijhouden in hidden veld om er na een submit noaan te kunnen
                    echo '<input type="hidden" id="form-aantal-elementen" name="aantal-elementen" value="'. $aantal_elementen_per_pagina . '">';
                    // standaard de eerste pagina tonen
                    if(!isset($huidige_pagina)) { $huidige_pagina = 1; }
                    // de waarde van de huidige pagina bijhouden in hidden veld om er na een submit noaan te kunnen
                    echo '<input type="hidden" id="form-huidige-pagina" name="huidige-pagina" value="'. $huidige_pagina . '">';
                    // De foutboodschappen/succesboodschappen tonen indien die er zijn
                    if (isset($fout)) {
                        echo '<div class="alert alert-danger">' .
                            '<i class="ion-android-warning"></i> &nbsp; ' . $fout .
                            '</div>';
                    }
                    if (isset($succes)) {
                    echo '<div class="alert alert-success">' .
                            '<i class="ion-android-done"></i> &nbsp; ' . $succes .
                        '</div>';
                    }
                    // De titel van de pagina
                    echo '<h2>' . $_GET['pagina_titel'] . '</h2>';
                ?>
                <hr />
