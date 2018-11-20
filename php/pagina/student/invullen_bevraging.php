<?php

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we student zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("STUDENT"));

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/student_controller.php';

$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$student_controller = new StudentController($gebruikersnaam);
// TESTEN MET 2 (= niet ingevuld)
$id = 2;
// indien gezet ophalen
if(isset($_GET['bevragingid'])){
    $id = $_GET['bevragingid'];
}
$bevraging = $student_controller->geef_bevraging($id);
$doelgroep="";
// Bevraging kon niet worden geladen, dus fout tonen
if ($bevraging === null){
    $fout = 'Kon bevraging (id: ' . $id. ') niet laden.';
}
else {
    $doelgroep = $bevraging->geef_doelgroep();
}

// kijken of bevraging nog niet ingevuld is, anders naar raadpleegpagina
if($bevraging !== null && $bevraging->geef_voltooid_op() !== null){
    $sessie_controller->ga_naar_pagina("/php/pagina/student/raadplegen_bevraging.php?bevragingid=" . $bevraging->geef_id());
}

// kijken of deze gebruiker de bevraging wel mag invullen  (false meegeven omdat we als invuller toegang vragen)
if(!$student_controller->controleer_toegang_bevraging($id, false)) {
    $fout = "U heeft geen toegang tot deze bevraging!";
    $bevraging = null;
}

// Kijken of ze wordt ingediend
if(isset($_POST['opslaan'])){
    // antwoorden toevoegen aan de bevraging
    $fout = "";
    foreach($bevraging->geef_vragenlijst()->geef_vragen() as $vraag) {
        // antwoord ophalen
        if(isset($_POST['vraag-' . $vraag->geef_id()])){
            // kijken welk type vraag
            if($vraag->geef_vraagtype() === "open"){
                $vraag->zet_antwoord(new Antwoord(0, $_POST['vraag-' . $vraag->geef_id()]));
            }
            else {
                $vraag->zet_antwoord(new Antwoord($_POST['vraag-' . $vraag->geef_id()], ""));
            }
        }
        // Vraag was niet beantwoord
        else {
            $fout = "Kon bevraging niet indienen!";
            break;
        }
    }
    // kijken of we de antwoorden konden toevoegen
    if($fout === "") {
        // succesvol ingediend, dus naar "raadplegen" gaan
        if ($student_controller->indienen_bevraging($bevraging)) {
            // daar boodschap tonen dat indienen gelukt is
            $sessie_controller->ga_naar_pagina("/php/pagina/student/raadplegen_bevraging.php?bevragingid=" . $bevraging->geef_id() . "&succes=Bevraging succesvol ingediend!");
        } else {
            // indien niet gelukt, oude gegevens ophalen
            $bevraging = $student_controller->geef_bevraging($id);
            $fout = "Kon bevraging niet indienen!";
        }
    }
}

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Invullen bevraging (Doelgroep: ' . $doelgroep . ')';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';

/**
 * Inhoud van de pagina
 */
// Bevraging kon niet worden geladen
if ($bevraging === null){
// knop om terug naar startpagina te gaan van de gebruiker
    echo maak_dashboard_knop();
}
else {
    include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/bevraging.php';
    echo '<br>';
    echo maak_htmlbevraging_div($bevraging);
    echo '<br>';
    echo maak_dashboard_knop();
    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    echo maak_submit_knop("Indienen", "thumbsup", "opslaan", false, true, "warning");
}

include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php';
