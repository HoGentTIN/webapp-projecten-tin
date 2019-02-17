<?php
/**
 * Pagina met een overzicht van een bepaalde groep voor bepaalde lector binnen projecten
 */

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we lector zijn, anders geen toegang pagina
//$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/lector_controller.php';

// welkom banner + afmelden
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/utilities.php';

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/project_groep.php';

$olodid = $_GET['olodid'];
$groepid = $_GET['groepid'] ;
$lc = new LectorController("slab754");
$olod = $lc->geef_olod($olodid);
// $groep = ... query db
$project_groep = new Project_Groep("Groep $groepid");
$project_groep->voeg_lid_toe($lc->geef_gebruiker("208188sd", ""));
$project_groep->voeg_lid_toe($lc->geef_gebruiker("214261eo", ""));
$project_groep->voeg_lid_toe($lc->geef_gebruiker("424987jt", ""));
$project_groep->voeg_lid_toe($lc->geef_gebruiker("426069ac", ""));
// begeleidende lector
$project_groep->voeg_lid_toe($lc->geef_gebruiker("slab754", ""));

$_GET['pagina_titel'] = $olod->geef_naam() . ' - ' . $project_groep->geef_naam();
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';
?>
    <!-- Eignelijke inhoud pagina -->
<!-- rij met portret foto's en namen studenten -->
<div class="row">
    <div class="col-lg-2"></div>
<?php
    foreach ($project_groep->geef_leden() as $lid){
        // enkel studenten tonen
        if ($lid->geef_gebruikertype() === "Student" ) {
            echo '<div class="col-lg-2" style="text-align: center;">' .
                UTILITIES::GEEF_HTML_PROFIELFOTO($lid->geef_gebruikertype(), $lid->geef_gebruikersnaam()) . '<br />' .
                $lid->geef_voornaam() . ' ' . $lid->geef_familienaam() .
                '</div>';
        }
    }
?>
</div>
<div class="row">
    <div class="col-lg-2" style="text-align: right;"># Ongewettigd afw</div>
<?php
    foreach ($project_groep->geef_leden() as $lid){
        if ($lid->geef_gebruikertype() === "Student" ) {
            echo '<div class="col-lg-2" style="text-align: center;">' . '0' . '</div>';
        }
    }
?>
</div><br>
<?php
    // Voor elke week een knap voor opvolging & logboek
    foreach(range(1,6) as $i) {
        echo '<br><div class="row">';
            echo '<div class="col-lg-2" style="text-align: right;">' . "Week $i" . '</div>';
            echo '<div class="col-lg-2">' . maak_submit_knop("Opvolging", "clipboard", "verslag", false, false, "info") . '</div>';
            echo '<div class="col-lg-2">' . maak_submit_knop("Logboek", "calendar", "logboek", false, false, "info") . '</div>';
            echo '<div class="col-lg-2" style="text-align: right;">' . "Week " . ($i+6) . '</div>';
            echo '<div class="col-lg-2">' . maak_submit_knop("Opvolging", "clipboard", "verslag", false, false, "info") . '</div>';
            echo '<div class="col-lg-2">' . maak_submit_knop("Logboek", "calendar", "logboek", false, false, "info") . '</div>';
        echo '</div>';
    }
    // Knoppen voor een totaal logboek & opvolging
    echo '<br><br><div class="row">';
    echo '<div class="col-lg-2" style="text-align: right;">' . "Alles" . '</div>';
    echo '<div class="col-lg-2">' . maak_submit_knop("Opvolging", "clipboard", "verslag", false, false, "info") . '</div>';
    echo '<div class="col-lg-2">' . maak_submit_knop("Logboek", "calendar", "logboek", false, false, "info") . '</div>';
    echo '</div>';
    echo '<br><br><br>';
    echo maak_submit_knop("Overzicht groepen", "arrow-return-left", "groep", false, false);
?>
    <!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>