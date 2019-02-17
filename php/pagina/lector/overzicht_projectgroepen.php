<?php
/**
 * Dashboard pagina voor de lector
 */
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we lector zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));

// aantal openstaande & ingevulde bevragingen opvragen
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/lector_controller.php';
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$lc = new LectorController($gebruikersnaam);
$aantal_studenten = 4; //$lector_controller->geef_aantal_bevragingen("%");
$succes = DATABANK::geef_instantie()->geef_sql_query();

$olod = $lc->geef_olod(1);
$pc = new PeriodeController();
$periode = $pc->geef_periode(1819, 2);
$groepen = $lc->geef_projectgroepen_olod($olod, $periode);

// welkom banner + afmelden
$_GET['pagina_titel'] = $olod->geef_naam() . ' (Aj. ' . $periode->geef_academie_jaar() . ', Sem. ' . $periode->geef_zittijd() . ') - Overzicht groepen';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';
?>
<!-- Eignelijke inhoud pagina -->
<?php
echo '<div class="row">';
foreach ($groepen as $groep) {
    echo maak_dashboard_kaart("groen", $groep->geef_aantal_studenten(), "Studenten", $_SERVER['SRV_ALIAS'] . "/php/pagina/lector/overzicht_projectgroep.php?groepid=" . $groep->geef_id(), "Bekijk " . $groep->geef_naam());
}
echo '</div>';
echo '<br>';
echo maak_dashboard_knop();
?>
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>
