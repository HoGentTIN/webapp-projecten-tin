<?php

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/bevraging.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/bevraging_mapper.php';

$bm = new BevragingMapper();
$bevragingen = $bm->geef_bevragingen("%", "Analyse", true);

$filename = "resultaten.csv"; // File Name
// Download file
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");

if(count($bevragingen) > 0) {
    $eerste_bevraging = $bevragingen[0];
    $nr = 1;
    $divider = ";";
    echo "Ingevuld door" . $divider;
    echo "ID bevraging" . $divider;
// kolomnamen (=vragen)
    foreach($eerste_bevraging->geef_vragenlijst()->geef_vragen() as $vraag) {
        echo "Vraag " . $nr . $divider;
        $nr++;
    }
    echo "\r\n";

// data (=antwoorden) van elke bevraging
    foreach ($bevragingen as $bevraging) {
        echo $bevraging->geef_intedienen_door()->geef_voornaam() . " " .
            $bevraging->geef_intedienen_door()->geef_familienaam() . $divider;
        echo $bevraging->geef_id() . $divider;
        foreach ($bevraging->geef_antwoorden() as $antwoord) {
            // enters in tekst wegwerken
            echo str_replace("\r\n", "", $antwoord->geef_tekst()) . $divider;
        }
        echo "\r\n";
    }
}