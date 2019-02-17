<?php

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we lector zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/lector_controller.php';

$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$lector_controller = new LectorController($gebruikersnaam);

// geen id meegeven, dus terug naar startpagina
if(!isset($_GET['bevragingid'])){
    $sessie_controller->ga_naar_startpagina();
}

$id = intval($_GET['bevragingid']);
$bevraging = $lector_controller->geef_bevraging($id);

$doelgroep="";
// Bevraging kon niet worden geladen, dus fout tonen
if ($bevraging === null){
    $fout = 'Kon bevraging (id: ' . $id. ') niet laden.';
}
else {
    $doelgroep = $bevraging->geef_doelgroep();
}

// kijken of bevraging wel ingevuld is, anders naar dashboard
if($bevraging !== null && $bevraging->geef_voltooid_op() === null){
    $sessie_controller->ga_naar_startpagina();
}

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Raadplegen bevraging (Doelgroep: ' . $doelgroep . ')';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/bevraging.php';

// Kijken of we de bevragingwillen downloaden als pdf
if(isset($_POST['download'])) {
    $lector_controller->download_bevraging_pdf($bevraging, maak_htmlbevraging_div($bevraging));
}

/**
 * Inhoud van de pagina
 */
// Bevraging kon niet worden geladen
if ($bevraging === null){
// knop om terug naar startpagina te gaan van de gebruiker
    echo maak_dashboard_knop();
}
else {
    if($bevraging->met_score()) {
        echo '<H4>SUS Score: ' . $bevraging->geef_score() . '</H4>';
    }
    echo '<H4>Ingediend op: ' .  $bevraging->geef_voltooid_op()->format('d/m/Y') . '</H4>';
    echo '<H4>Ingediend door: ';
    if($bevraging->is_anoniem()){
        echo "Anoniem";
    }
    else {
        echo $bevraging->geef_intedienen_door()->geef_voornaam() .  ' ' . $bevraging->geef_intedienen_door()->geef_familienaam();
    }
    echo '</H4>';
    echo '<br>';
    echo maak_htmlbevraging_div($bevraging);
    echo '<br>';
    echo maak_dashboard_knop();
    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    echo maak_submit_knop("Opslaan als pdf", "archive", "download", false, false, "warning");
}

include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php';