<?php
/**
 * Dashboard pagina voor de lector
 */
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));

// aantal openstaande & ingevulde bevragingen opvragen
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/lector_controller.php';
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$lector_controller = new LectorController($gebruikersnaam);
$aantal_bevragingen = $lector_controller->geef_aantal_bevragingen("%");
$succes = DATABANK::geef_instantie()->geef_sql_query();


// welkom banner + afmelden
$_GET['pagina_titel'] = 'Lector dashboard';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';
?>
<!-- Eignelijke inhoud pagina -->
<?php
echo '<div class="row">';
    echo maak_dashboard_kaart("blauw", $aantal_bevragingen, "Bevragingen", "/php/pagina/lector/overzicht_bevragingen.php", "Bekijk bevragingen");
echo '</div>';
    ?>
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>
