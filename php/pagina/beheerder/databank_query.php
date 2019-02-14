<?php
/**
 * Databank pagina voor de beheerder om zo querys uit te voeren op de DB
 */

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("BEHEERDER"));

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Query databank';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
// BeheerderController aanmaken voor huidige beheerder
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/beheerder_controller.php';

$query_results = "";
if (isset($_POST['execquery'])) {
    $query_results = DATABANK::geef_instantie()->voer_query_uit($_POST['sel_sql_type'], $_POST['ta_query']);
}
?>
<!-- Eignelijke inhoud pagina -->
<?php
echo '<div class="container-inhoud">';
echo '<div class="row col-12">';
echo '<div class="col-">Type query' .
        '<select name="sel_sql_type">' .
            '<option value="SELECT_COLUMN">Selecteer kolom</option>' .
            '<option value="SELECT_ALL">Selecteer alle rijen</option>' .
            '<option value="DELETE">Verwijderen records</option>' .
            '<option value="UPDATE">Bijwerken records</option>' .
            '<option value="INSERT">Toevoegen records</option>' .
        '</select>';
echo '</div>';
echo '<div class="row col-12">';
echo 'Uit te voeren query: <textarea name="ta_query">' . DATABANK::geef_instantie()->geef_sql_query() . '</textarea>';
echo '</div>';
echo '<button class="btn btn-block btn-primary" type="submit" name="execquery">Execute Query</button>';
echo '<div class="row col-12">';
echo 'Resultaten: <textarea name="ta_results">' . $query_results . '</textarea>';
echo '</div>';
echo '<div class="row col-12">';
echo 'Fouten SQL: <textarea name="ta_errors">' . DATABANK::geef_instantie()->geef_sql_fout() . '</textarea>';
echo '</div>';
echo '</div>';
?>
    <br><br>
<?php echo maak_dashboard_knop(); ?>
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>