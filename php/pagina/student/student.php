<?php
/**
 * Dashboard pagina voor de student
 */

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we student zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("STUDENT"));

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Student dashboard';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';

// aantal openstaande & ingevulde bevragingen opvragen
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/student_controller.php';
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$student_controller = new StudentController($gebruikersnaam);
$aantal_open_bevragingen = $student_controller->geef_aantal_open_bevragingen();
$aantal_ingevulde_bevragingen = $student_controller->geef_aantal_ingevulde_bevragingen();
?>
<!-- Eignelijke inhoud pagina -->
<div class="row">
<?php
    $url_pagina = $_SERVER['SRV_ALIAS'] . "/php/pagina/student/overzicht_bevragingen.php";
    echo maak_dashboard_kaart("groen", $aantal_open_bevragingen, "Openstaande bevragingen", $url_pagina . "?voltooidop=false", "Bekijk bevragingen");
    echo maak_dashboard_kaart("rood", $aantal_ingevulde_bevragingen, "Ingevulde bevragingen", $url_pagina . "?voltooidop=true", "Bekijk bevragingen");
?>
<div>
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>