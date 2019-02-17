<?php
/**
 * Pagina met een logboek van bepaalde groep voor bepaalde lector
 */

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we lector zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/lector_controller.php';

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Logboek week X van groep Y';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/html.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/tabel.php';

function maak_logboek_tabel(array $headers, array $data_ids, array $data, array $nieuwe_rij=[]) {
    $aantal_kolommen_tabel = count($headers);
    $html_tabel = '<div class="table-responsive">'.
        '<table class="table table-hover">';
    $html_tabel .= maak_tabel_header_rij($headers);
    $html_tabel .= '<tbody>';
    $aantal_elementen = count($data);
    // start index in tabel bepalen
    $huidig_element = 0;
    // acties voor elke rij (indien vergrendeld => geen acties meer)
    $acties = true;
    while ($huidig_element < $aantal_elementen) {
        $html_tabel .= maak_tabel_logboek_detail_rij($data_ids[$huidig_element], $data[$huidig_element], $acties);
        $huidig_element += 1;
    }
    if($nieuwe_rij !== []) {
        $html_tabel .= maak_tabel_toevoeg_rij($nieuwe_rij);
    }
    $html_tabel .= '</tbody></table></div>';
    return $html_tabel;
}

function maak_tabel_logboek_detail_rij($rij_id, array $details_rij, bool $acties=true){
    $html_rij = '<tr>';
    // details van de rij toevoegen
    foreach($details_rij as $detail_rij){
        $html_rij .= '<td>' . $detail_rij .'</td>';
    }
    // acties toevoeven voor deze rij
    $html_rij .= '<td>';
    if ($acties === true) {
        $html_rij .= maak_submit_knop("", "edit ", "bewerken", false, false, "link", "submit_na_opslaan_id('" . $rij_id . "');");
        $html_rij .= maak_submit_knop("", "trash-a", "verwijderen", false, false, "link", "submit_na_opslaan_id('" . $rij_id . "');");
    }
    $html_rij .= '</td>';
    $html_rij .= '</tr>';
    return $html_rij;
}

function maak_logboek_tabel_totalen(array $headers, array $data) {
    $html_tabel = '<div class="table-responsive">'.
        '<table class="table table-hover">';
    $html_tabel .= maak_tabel_header_rij($headers);
    $html_tabel .= '<tbody>';
    $aantal_elementen = count($data);
    // start index in tabel bepalen
    $huidig_element = 0;
    // geen acties mogelijk
    $acties = [];
    while ($huidig_element < $aantal_elementen) {
        $html_tabel .= maak_tabel_detail_rij(0, $data[$huidig_element], $acties);
        $huidig_element += 1;
    }
    $html_tabel .= '</tbody></table></div>';
    return $html_tabel;
}
?>
    <!-- Eignelijke inhoud pagina -->
<?php
// tabel met volgende info
// rij met alle titels
// ingevulde rijen
// extra lege rij om toe te voegen

// beschikbare filters voor deze pagina
$filters = [];
$headers = [ "Datum", "Student", "Omschrijving taak", "Duur (min.)", "OLOD", "Commit/Revision", "" ];
$data_ids = [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ];
$data = [
  [ "20/2/2018", "Jef", "Alles gedaan", "20", "OP", 0],
  [ "20/2/2018", "Mieke", "Domeinmodel", "120", "OA", 0],
  [ "21/2/2018", "Piet", "EERD", "210", "DB", 0],
  [ "21/2/2018", "Jef", "Alles gedaan", "20", "OP", 0],
  [ "21/2/2018", "Mieke", "Domeinmodel", "120", "OA", 0],
  [ "21/2/2018", "Piet", "EERD", "210", "DB", 0],
  [ "22/2/2018", "Jef", "Alles gedaan", "20", "OP", 0],
  [ "23/2/2018", "Mieke", "Domeinmodel", "120", "OA", 0],
  [ "23/2/2018", "Piet", "EERD", "210", "DB", 0],
];
echo '<div class="row">';
echo '<div class="col-lg-7">';
echo maak_logboek_tabel($headers, $data_ids, $data, []);
echo '</div>';
echo '<div class="col-lg-1"></div>';
$headers_totalen = [ "Student", "OA", "OO", "OP", "DB", "Andere", "Totaal" ];
$data_totalen = [
    [ "Jef", 0, 0, "1u", 0, 0, "1u"],
    [ "Mieke", "6u", 0, 0, 0, 0, "6u"],
    [ "Piet", 0, 0, 0, "10u30", 0, "10u30"],
    [ "Groep", 0, "6u", "1u", "10u30", 0, "17u30"],
    [ "Gemiddeld pp", 0, "2u", "20m", "3u30", "0", "5u50"],
];
echo '<div class="col-lg-4">';
echo '<H3>Totalen deze week</H3>';
echo maak_logboek_tabel_totalen($headers_totalen, $data_totalen);
echo '<br><H3>Totalen volledig project</H3>';
echo maak_logboek_tabel_totalen($headers_totalen, $data_totalen);
echo '</div>';
echo '</div>';

echo '<br>';
echo maak_submit_knop("overzicht groep X", "arrow-return-left", "groep", false, false);
?>
    <!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>