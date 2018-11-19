<?php
/**
 * Beheer pagina voor periodes voor de beheerder
 */

include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/sessie.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/tabel.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("BEHEERDER"));

// BeheerderController aanmaken voor huidige beheerder
include_once '/srv/prjtinapp' . '/php/klasse/controller/beheerder_controller.php';
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$beheerder_controller = new BeheerderController($gebruikersnaam);

// Periode verijderen
if (isset($_POST['verwijderen'])) {
    // informatie uit hidden veld ophalen op basis van _ gescheiden
    $rij_ids = explode('_', $_POST['rij-id']);
    $jaartal = $rij_ids[0];
    $zittijd = $rij_ids[1];

    if($beheerder_controller->verwijder_periode($jaartal, $zittijd)) {
        $succes = "Periode $jaartal, zittijd $zittijd succesvol verwijdert!";
    }
    else {
        $fout = "Kon periode niet wijderen. " . $beheerder_controller->geef_fout();
    }
}

// Periode toevoegen
if(isset($_POST['toevoegen']) ){
    $jaar = $_POST['periode-jaar'];
    $zittijd = $_POST['periode-zittijd'];
    $van =  $_POST['periode-van'] ;
    $tot = $_POST['periode-tot'] ;

    if ($beheerder_controller->toevoegen_periode($jaar, $zittijd, $van, $tot)) {
        $succes = "Periode $jaar, zittijd $zittijd succesvol toegevoegd!";
    } else {
        $fout = "Kon periode niet toevoegen. " . $beheerder_controller->geef_fout();
    }
}

$huidig_jaar=0;
$huidige_zittijd=0;

// filters zijn aangepast
if(isset($_POST['actie']) && $_POST['actie'] === 'filters') {
    // informatie uit hidden veld ophalen op basis van _ gescheiden
    $rij_ids = explode('_', $_POST['rij-id']);
    $huidig_jaar = intval($rij_ids[0]);
    $huidige_zittijd = intval($rij_ids[1]);
}

// Periodes opvragen
$periodes = $beheerder_controller->geef_periodes($huidig_jaar, $huidige_zittijd);

// periodes omzetten naar toonbare data
$data = [];
// aparte tabel om de ids voor elk object bij te houden
$data_ids = [];
$acties = [];
foreach($periodes as $periode){
    $data[] = [
        $periode->geef_jaar(),
        $periode->geef_zittijd(),
        $periode->geef_van()->format('d/m/Y'),
        $periode->geef_tot()->format('d/m/Y')
    ];
    // toegestane acties per rij, enkel verwijderen
    $acties[] = ["verwijderen" => true];
    $data_ids[] = $periode->geef_jaar() . '_' . $periode->geef_zittijd();
}
// aantal elementen voor volledige tabel
$totaal_aantal_elementen = count($periodes);
// generieke code uitvoeren voor pagina met tabel die bladerbaar is
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/bladeren.php';

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Beheer periodes';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/header.php';

?>
<?php
$beschikbare_jaren = [
    [ "naam" => "Alles", "waarde" => 0 ],
    [ "naam" => "2017", "waarde" => 2017 ],
    [ "naam" => "2018", "waarde" => 2018 ],
    [ "naam" => "2019", "waarde" => 2019 ]
];

$beschikbare_zittijden = [
    [ "naam" => "Alles", "waarde" => 0 ],
    [ "naam" => "1", "waarde" => 1 ],
    [ "naam" => "2", "waarde" => 2 ],
    [ "naam" => "3", "waarde" => 3 ]
];

$headers = ["Jaar", "Zittijd", "Van", "Tot", ""];
// beschikbare filters voor deze pagina
$filters = [
    [ // periodes filteren
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["filter-periode-jaar",1, "wijzig_filters", $beschikbare_jaren, $huidig_jaar, "waarde", "naam"]
    ],
    [ // zittijden filteren
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["filter-periode-zittijd",1, "wijzig_filters", $beschikbare_zittijden, $huidige_zittijd, "waarde", "naam"]
    ]
];

$beschikbare_jaren = [
    [ "naam" => "-- Kies jaartal --", "waarde" => "" ],
    [ "naam" => "2017", "waarde" => "2017" ],
    [ "naam" => "2018", "waarde" => "2018" ],
    [ "naam" => "2019", "waarde" => "2019" ]
];

$beschikbare_zittijden = [
    [ "naam" => "-- Kies zittijd --", "waarde" => "" ],
    [ "naam" => "1", "waarde" => "1" ],
    [ "naam" => "2", "waarde" => "2" ],
    [ "naam" => "3", "waarde" => "3" ]
];
// elementen om de rij voor nieuwe periode toe te voegen te maken in de tabel
$nieuwe_rij = [
    [ // periodes filteren
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["periode-jaar", 1, "", $beschikbare_jaren, "", "waarde", "naam", true]
    ],
    [ // zittijden filteren
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["periode-zittijd", 1, "", $beschikbare_zittijden, "", "waarde", "naam", true]
    ],
    [ // zittijden filteren
        'type' =>TABELCEL_TYPE::DATUM,
        'waarden' => ["periode-van", "2017-01-01", "20119-01-02", true]
    ],
    [ // zittijden filteren
        'type' =>TABELCEL_TYPE::DATUM,
        'waarden' => ["periode-tot", "2017-01-01", "20119-01-02", true]
    ],
    [ // zittijden filteren
        'type' =>TABELCEL_TYPE::KNOP,
        'waarden' => ["", "plus", "toevoegen", false, true, "link"]
    ]
];
    echo maak_tabel($filters, $headers, $data_ids, $data, "periodes", $huidige_pagina, $max_paginas, $aantal_elementen_per_pagina, $nieuwe_rij, $acties);
?>
    <br><br>
    <?php echo maak_dashboard_knop(); ?>
    <!-- footer includen -->
<?php include $_SERVER['DOCUMENT_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>