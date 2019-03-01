<!-- Test pagina voor debugging -->
<?php

include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/tabel.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("BEHEERDER"));

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Overzicht php configuratie';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';

$filters=[];
$headers=[ "Naam", "Waarde" ];
$data_ids=[];
$data=[];

foreach ($_SERVER as $k => $v) {
    array_push($data_ids, $k);
}
sort($data_ids);

foreach ($data_ids as $data_id) {
    array_push($data, [ $data_id, $_SERVER[$data_id]]);
}

echo maak_tabel($filters, $headers, $data_ids, $data, "variabelen", 1,0,TOON_AANTAL_ELEMENTEN_PER_PAGINA::ALLES);
?>
<br><br>
<?php echo maak_dashboard_knop(); ?>
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>
