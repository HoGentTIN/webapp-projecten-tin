<?php
/**
 * Dashboard pagina voor de beheerder
 */

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("BEHEERDER"));

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Beheerder dashboard';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
// BeheerderController aanmaken voor huidige beheerder
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/beheerder_controller.php';
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$beheerder_controller = new BeheerderController($gebruikersnaam);

$aantal_periodes = count($beheerder_controller->geef_periodes());
$aantal_actieve_gebruikers = 5; //$gebruiker->geef_aantal_gebruikers(null, null, "J");
$aantal_gebruikers = 20; //$gebruiker->geef_aantal_gebruikers();
?>
<!-- Eignelijke inhoud pagina -->
<?php
echo '<div class="row">';
    $url_pagina = $_SERVER['SRV_ALIAS'] . '/php/pagina/beheerder/';
    echo maak_dashboard_kaart("blauw", $aantal_periodes, "Periodes", $url_pagina . "beheer_periodes.php", "Beheer Periodes");
    echo maak_dashboard_kaart("rood", $aantal_actieve_gebruikers, "Actieve gebruikers", $url_pagina . "beheer_gebruikers.php", "Beheer Gebruikers");
    echo maak_dashboard_kaart("groen", $aantal_gebruikers, "Gebruikers", $url_pagina . "importeer_gebruikers.php", "Importeer Gebruikers");
    echo maak_dashboard_kaart("antraciet", 1, "Databank", $url_pagina . "databank_query.php", "Query Databank");
    echo maak_dashboard_kaart("hemelsblauw", count(array_keys($_SERVER)), "Server variabelen", $url_pagina . "server.php", "PHP configuratie");
echo '</div>';
?>
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>