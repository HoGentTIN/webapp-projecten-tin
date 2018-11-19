<?php
/**
 * Beheer pagina voor periodes voor de beheerder
 */

include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/sessie.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/html.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/tabel.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/bevraging.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("STUDENT"));

// BeheerderController aanmaken voor huidige beheerder
include_once '/srv/prjtinapp' . '/php/klasse/controller/student_controller.php';
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$student_controller = new StudentController($gebruikersnaam);

// Bevraging raadplegen
if (isset($_POST['openen'])) {
    $sessie_controller->ga_naar_pagina('/php/pagina/student/raadplegen_bevraging.php?bevragingid='.$_POST['rij-id']);
}

// Bevraging bewerken
if (isset($_POST['bewerken'])) {
    $sessie_controller->ga_naar_pagina('/php/pagina/student/invullen_bevraging.php?bevragingid='.$_POST['rij-id']);
}

// Bevraging opslaan
if(isset($_POST['opslaan']) ){
    $bevraging_id = intval($_POST['rij-id']);
    $bevraging = $student_controller->geef_bevraging($bevraging_id);
    $student_controller->download_bevraging_pdf($bevraging, maak_htmlbevraging_div($bevraging));
}

$huidige_voltooid_op=0;
$voltooid_op = null;

// kijken of we paramter meekregen van overzichtscherm
if(isset($_GET['voltooidop'])){
    if($_GET['voltooidop'] === "true") {
        $voltooid_op = true;
        $huidige_voltooid_op = 1;
    }
    else {
        $voltooid_op = false;
        $huidige_voltooid_op = 2;
    }
    unset($_GET['voltooidop']);
}

// filters zijn aangepast
if(isset($_POST['actie']) && $_POST['actie'] === 'filters') {
    $huidige_voltooid_op = intval($_POST['rij-id']);
    switch($huidige_voltooid_op) {
        case 1: $voltooid_op = true; break;
        case 2: $voltooid_op = false; break;
        default: $voltooid_op = null; break;
    }
}

// Bevragingen opvragen
$bevragingen = $student_controller->geef_bevragingen($voltooid_op, $sessie_controller->geef_aangemelde_gebruiker()->geef_doelgroep());

// Bevraging objecten omzetten naar data voor in tabel te tonen
$headers = ["Type", "Doelgroep", "Tester", "Deadline", "Voltooid", "Score", ""];
$data = [];
$data_ids = [];
$acties=[];
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
        $bevraging->geef_doelgroep(),
        ($bevraging->is_anoniem() === true) ? "Anoniem" : $bevraging->geef_intedienen_door()->geef_voornaam() .  ' ' . $bevraging->geef_intedienen_door()->geef_familienaam(),
        $bevraging->geef_deadline()->format('d/m/Y'),
        $voltooid,
        $score
    ];
    // indien voltooid, acties = raadplegen & downloaden
    if($bevraging->geef_voltooid_op() !== null) {
        $acties[] = [ 'openen' => true, 'opslaan' => true ];
    }
    // indien niet voltooid, acties = invullen
    else  {
        // enkel mogelijk indien deze persoon moet indienen (en dus niet doelgroep is)
        if ($bevraging->geef_intedienen_door()->geef_gebruikersnaam() === $gebruikersnaam) {
            $acties[] = ['bewerken' => true];
        }
        else {
            $acties[] = [];
        }
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
$beschikbaar_voltooid_op = [
    [ "naam" => "Alle", "waarde" => 0 ],
    [ "naam" => "Ja", "waarde" =>  1],
    [ "naam" => "Nee", "waarde" => 2 ],
];

// beschikbare filters voor deze pagina
$filters = [
    // lege filter want eerste rij wordt niet gefilterd
    [
        'type' => TABELCEL_TYPE::LEEG,
    ],
    [
        'type' => TABELCEL_TYPE::LEEG,
    ],
    [
        'type' => TABELCEL_TYPE::LEEG,
    ],
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
<?php include $_SERVER['DOCUMENT_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>