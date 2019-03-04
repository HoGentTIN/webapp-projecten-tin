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
    echo "SQL Type: " . $_POST['sel_sql_type'] . '<br>Query: ' . $_POST['ta_query'];
    $query_results = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value($_POST['sel_sql_type']), $_POST['ta_query']);
    if(is_array($query_results)){
        $query_results = json_encode($query_results);
    }
    else {
        $query_results .= ' ';
    }
}
?>
<!-- Eignelijke inhoud pagina -->
<?php
echo '<div class="container-inhoud">';
echo '<div class="row col-12">';
echo 'Type query:' .
        '<select name="sel_sql_type">' .
            '<option value="SELECT_COLUMN">Selecteer kolom</option>' .
            '<option value="SELECT_ALL">Selecteer alle rijen</option>' .
            '<option value="DELETE">Verwijderen records</option>' .
            '<option value="UPDATE">Bijwerken records</option>' .
            '<option value="INSERT">Toevoegen records</option>' .
            '<option value="MANAGE">Beheer tabellen</option>' .
        '</select>';
echo '</div>';
echo '<br>';
echo '<div class="row col-12">';
echo 'Uit te voeren query: <textarea name="ta_query" cols=100 rows="6">' . DATABANK::geef_instantie()->geef_sql_query() . '</textarea>';
echo '</div>';
echo '<br>';
echo '<button class="btn btn-block btn-primary" type="submit" name="execquery">Execute Query</button>';
echo '<br>';
echo '<div class="row col-12">';
echo 'Resultaten: <textarea name="ta_results" cols="100" rows="15">' . $query_results . '</textarea>';
echo '</div>';
echo '<br>';
echo '<div class="row col-12">';
echo 'Fouten SQL: <textarea name="ta_errors" cols="100" rows="3">' . DATABANK::geef_instantie()->geef_sql_fout() . '</textarea>';
echo '</div>';
echo '</div>';
?>
    <br><br>
<?php echo maak_dashboard_knop(); ?>
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>