<?php
/**
 * Pagina met een weekrapport van bepaalde groep voor bepaalde lector
 */

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we lector zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));


// welkom banner + afmelden
$_GET['pagina_titel'] = 'Weekrapport X van groep Y';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';
?>
    <!-- Eignelijke inhoud pagina -->
<?php
//Rij met fotos/portretten van betrokken studenten

// Rij met radiobuttons voor status aanwezigheid (standaard aangevinkt op aanwezig)


echo '<div class="row">';
echo maak_dashboard_kaart("blauw", $aantal_bevragingen, "Bevragingen", $_SERVER['SRV_ALIAS'] . "/php/pagina/lector/overzicht_bevragingen.php", "Bekijk bevragingen");
echo '</div>';
?>
    <!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>