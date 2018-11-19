<?php
/**
 * Beheer pagina voor periodes voor de beheerder
 */

include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/sessie.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/html.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/tabel.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/bevraging.php';
// Kijken of we lector zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("LECTOR"));

include_once '/srv/prjtinapp' . '/php/klasse/controller/lector_controller.php';

$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$lector_controller = new LectorController($gebruikersnaam);

// Bevraging raadplegen
if (isset($_POST['openen'])) {
    $sessie_controller->ga_naar_pagina('/php/pagina/lector/raadplegen_bevraging.php?bevragingid='.$_POST['rij-id']);
}

// Bevraging bewerken
if (isset($_POST['wissen'])) {
    $bevraging_id = intval($_POST['rij-id']);
    $lector_controller->wissen_bevraging($bevraging_id);
}

// Bevraging opslaan
if(isset($_POST['opslaan']) ){
    $bevraging_id = intval($_POST['rij-id']);
    $bevraging = $lector_controller->geef_bevraging($bevraging_id);
    $lector_controller->download_bevraging_pdf($bevraging, maak_htmlbevraging_div($bevraging));
}

$huidige_type = "%";
$huidige_doelgroep = "%";
$huidige_tester = "%";
$huidige_voltooid_op=0;
$voltooid_op = null;

// filters zijn aangepast
if(isset($_POST['actie']) && $_POST['actie'] === 'filters') {
    $rij_ids = explode('_', $_POST['rij-id']);
    $huidige_type = $rij_ids[0];
    $huidige_tester = $rij_ids[1];
    $huidige_doelgroep = $rij_ids[2];
    $huidige_voltooid_op = intval($rij_ids[3]);
    switch($huidige_voltooid_op) {
        case 1: $voltooid_op = true; break;
        case 2: $voltooid_op = false; break;
        default: $voltooid_op = null; break;
    }
}

// Bevragingen opvragen
//Create a variable for start time.
$time_start = microtime(true);
$GLOBALS['SQL'] = 0;
//Create a variable for end time.
$bevragingen = $lector_controller->geef_bevragingen($huidige_tester, $huidige_doelgroep, $voltooid_op);
$time_end = microtime(true);
//Subtract the two times to get seconds.
$time = $time_end - $time_start ;

// Bevraging objecten omzetten naar data voor in tabel te tonen
$headers = [ "Type bevraging", "Tester", "Doelgroep", "Deadline", "Voltooid", "Score", ""];
$data = [];
$data_ids = [];
$acties=[];

$succes = "Aantal uitgevoerde queries: " . $GLOBALS['SQL'] . ' in ' . $time . ' seconds' ;

foreach($bevragingen as $bevraging){
    $voltooid = "";
    $score = "";
    if($bevraging->geef_voltooid_op() !== null) {
        $voltooid = "<span ";
        // Indien te laat, tekst rood tonen
        if($bevraging->geef_deadline() < $bevraging->geef_voltooid_op()){
            $voltooid .= ' class="alert-danger"';
        }
        $voltooid .= ">" . $bevraging->geef_voltooid_op()->format('d/m/Y') . "</span>";
        if($bevraging->met_score()) {
            $score = $bevraging->geef_score();
        }
        else {
            $score = "n.v.t.";
        }
    }
    $data[] = [
        $bevraging->geef_vragenlijst()->geef_naam(),
        ($bevraging->is_anoniem() === true) ? "Anoniem" : $bevraging->geef_intedienen_door()->geef_voornaam() .  ' ' . $bevraging->geef_intedienen_door()->geef_familienaam(),
        $bevraging->geef_doelgroep(),
        $bevraging->geef_deadline()->format('d/m/Y'),
        $voltooid,
        $score
    ];
    // indien voltooid: acties = raadplegen, downloaden & wissen
    if($bevraging->geef_voltooid_op() !== null) {
        $acties[] = [ 'openen' => true, 'opslaan' => true, 'wissen' => true ];
    }
    else {
        $acties[] = [];
    }
    $data_ids[] = $bevraging->geef_id();
}
// aantal elementen voor volledige tabel
$totaal_aantal_elementen = count($bevragingen);
// generieke code uitvoeren voor pagina met tabel die bladerbaar is
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/bladeren.php';

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Overzicht bevragingen';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/header.php';

?>
<?php
$beschikbare_types = [
    ["naam" => "Alle", "waarde" => "%"]
];

$types = $lector_controller->geef_types_vragenlijsten();
foreach($types as $type){
    // gebruikersnaam / voornaam familienaam
    $beschikbare_types[] = ["naam" => $type, "waarde" => $type ];
}

$beschikbare_testers = [
    ["naam" => "Alle", "waarde" => "%"]
];

$testers = $lector_controller->geef_testers();
foreach($testers as $tester){
    // gebruikersnaam / voornaam familienaam
    $beschikbare_testers[] = ["naam" => $tester[1], "waarde" => $tester[0] ];
}

$beschikbare_doelgroepen = [
    ["naam" => "Alle", "waarde" => "%"]
];

$groepnamen = $lector_controller->geef_doelgroepen();
foreach($groepnamen as $groepnaam){
    $beschikbare_doelgroepen[] = ["naam" => $groepnaam, "waarde" => $groepnaam];
}

$beschikbaar_voltooid_op = [
    [ "naam" => "Alle", "waarde" => 0 ],
    [ "naam" => "Ja", "waarde" =>  1],
    [ "naam" => "Nee", "waarde" => 2 ],
];

// beschikbare filters voor deze pagina
$filters = [
    [
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["filter-types", 1, "wijzig_filters", $beschikbare_types, $huidige_type, "waarde", "naam"]
    ],
    [
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["filter-tester", 1, "wijzig_filters", $beschikbare_testers, $huidige_tester, "waarde", "naam"]
    ],
    [
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["filter-doelgroep", 1, "wijzig_filters", $beschikbare_doelgroepen, $huidige_doelgroep, "waarde", "naam"]
    ],
    // geen filter voor deadline
    [
        'type' => TABELCEL_TYPE::LEEG,
    ],
    [ // op voltooid of niet filteren
        'type' =>TABELCEL_TYPE::LIJST,
        'waarden' => ["filter-voltooid-op", 1, "wijzig_filters", $beschikbaar_voltooid_op, $huidige_voltooid_op, "waarde", "naam"]
    ]
];
// Kunnen geen bevraging toevoegen dus leeg laten
$nieuwe_rij = [];

// generieke tabel maken
echo maak_tabel($filters, $headers, $data_ids, $data, "bevragingen", $huidige_pagina, $max_paginas, $aantal_elementen_per_pagina, $nieuwe_rij, $acties);
?>
    <br><br>
<?php echo maak_dashboard_knop(); ?>
    <!-- footer includen -->
<?php include '/srv/prjtinapp' . '/php/pagina/gedeeld/footer.php'; ?>