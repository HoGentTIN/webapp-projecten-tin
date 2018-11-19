<?php
/**
 * Beheer pagina voor periodes voor de beheerder
 */

include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/sessie.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/tabel.php';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/utilities.php';
// Kijken of we beheerder zijn, anders geen toegang pagina
$sessie_controller->controleer_toegang_pagina(GEBRUIKER_TYPE::value("BEHEERDER"));

// BeheerderController aanmaken voor huidige beheerder
include_once '/srv/prjtinapp' . '/php/klasse/controller/beheerder_controller.php';
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$beheerder_controller = new BeheerderController($gebruikersnaam);

$gebruiker_types = ['Student', 'Lector', 'Extern'];
$gebruiker_kenmerken = ['Voornaam', 'Familienaam', 'Gebruikersnaam', 'Type', 'Email', 'OLOD'];

// Periode toevoegen
if(isset($_POST['importeren']) ){
    $actief = isset($_POST['actief']);
    $overschrijven = isset($_POST['dubbels']);
    // invoer gebruiker voor veldnamen mappen op kolommen databank
    $gebruiker_kenmerken_invoer= [];
    foreach($gebruiker_kenmerken as $gebruiker_kenmerk){
        $gebruiker_kenmerken_invoer[$gebruiker_kenmerk] = $_POST["inp-" . $gebruiker_kenmerk];
    }
    // naam van file uploader instellen voor de parser
    $naam = "file";
    // het csv bestand parsen
    $resultaat = UTILITIES::PARSE_CSV($naam, $gebruiker_kenmerken, $gebruiker_kenmerken_invoer);
    // kijken of het gelukt is
    if(!isset($resultaat["fout"])) {
        $succes = "Geldig CSV bestand<br>";
        // toevoegen
        $succes .= voeg_gebruikers_toe($beheerder_controller, $resultaat['resultaten'], $gebruiker_types);
    }
    else {
        $fout = $resultaat["fout"];
    }
}

function voeg_gebruikers_toe ($beheerder_controller, $nieuwe_gebruikers, $gebruiker_types_toegelaten, $activeren=true){
    $aantal_gelukt = 0;
    $aantal_dubbel = 0;
    $aantal_slechte_vreemde_sleutels = 0;
    $aantal_slecht = 0;
    $aantal_overgeslagen = 0;
    $aantal_records = count($nieuwe_gebruikers);
    $output = "";
    // nieuwe gebruikers importeren in de databank
    foreach($nieuwe_gebruikers as $nieuwe_gebruiker){
        // Controleren of we het type mogen importeren
        if(in_array($nieuwe_gebruiker['Type'], $gebruiker_types_toegelaten)){
            $gebruikersnaam =$nieuwe_gebruiker['Gebruikersnaam'];
            $type = $nieuwe_gebruiker['Type'];
            $voornaam = $nieuwe_gebruiker['Voornaam'];
            $familienaam = $nieuwe_gebruiker['Familienaam'];
            $email = $nieuwe_gebruiker['Email'];
            $olods = [ $nieuwe_gebruiker['OLOD'] ];
            if($beheerder_controller->voeg_gebruiker_toe($gebruikersnaam, $type, $voornaam, $familienaam, $email, $activeren, $olods)){
                $aantal_gelukt++;
            }
            else {
                $output .=  "<br>Fout voor " . $gebruikersnaam . ", " . $type . ", " . $voornaam . ", ". $familienaam . ", " . $email . ', ' . $olods[0];
                $output .= "<br>" . $beheerder_controller()->geef_fout();
                $aantal_slecht++;
            }
        }
        else {
            $aantal_overgeslagen++;
        }
    }

    $tab_teken = UTILITIES::TAB_TEKEN;
    // Afdrukken statistieken importeren
    $output .= "<br>CSV bestand succesvol geïmprteerd<br><br> " .
        "Overzicht resultaten import<br>" .
        $tab_teken ."Totaal aantal records: " . $aantal_records . '<br>' .
        $tab_teken . $tab_teken . 'Aangemaakt: ' . $aantal_gelukt . '<br>' .
        $tab_teken . $tab_teken . 'Mislukt: ' . ($aantal_dubbel + $aantal_slechte_vreemde_sleutels + $aantal_slecht) .'<br>' .
     /*
        $tab_teken . $tab_teken . $tab_teken . 'Dubbels: ' . $aantal_dubbel .'<br>' .
        $tab_teken . $tab_teken . $tab_teken . 'Verkeerde waarden: ' . $aantal_slechte_vreemde_sleutels .'<br>' .
        $tab_teken . $tab_teken . $tab_teken . 'Onbekende fout: ' . $aantal_slecht .'<br>' .*/
        $tab_teken . $tab_teken . 'Overgeslaan: ' . $aantal_overgeslagen . '<br>';
    return $output;
}

// welkom banner + afmelden
$_GET['pagina_titel'] = 'Importeer gebruikers';
$_GET['pagina_uploads'] = '1';
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/header.php';
?>
            <table>
                <tr>
                    <th style="text-align: right">CSV Bestand (Scheidingsteken ; )</th>
                    <th>
                        <input type="text" class="form-control" id="opladen-bestand" disabled>
                    </th>
                    <th>
                        <?php echo maak_oplaad_knop("file", ".csv", "opladen-bestand"); ?>
                    </th>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <th>Kenmerk gebruiker</th>
                    <th>Kolomnaam in CSV bestand</th>
                </tr>
                    <?php
                    foreach($gebruiker_kenmerken as $gebruiker_kenmerk){
                        echo '<tr>' .
                            '<td>' . $gebruiker_kenmerk .'</td>' .
                            // required zetten
                            '<td><input type="text" class="form-control" name="inp-' . $gebruiker_kenmerk . '" value="'. $gebruiker_kenmerk . '" required></td>' .
                            '</tr>';
                    }
                    ?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <th>Type gebruiker</th>
                    <th>Importeren</th>
                </tr>
                <?php
                    foreach($gebruiker_types as $type_gebruiker){
                        echo '<tr>' .
                            '<td>' . $type_gebruiker .'</td>' .
                            '<td>' . maak_html_toggle("type-" . $type_gebruiker, "") . '</td>' .
                            '</tr>';
                    }
                ?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <th>Onmiddellijk actief?</th>
                    <th>
                        <?php echo maak_html_toggle("actief", ""); ?>
                    </th>
                </tr>
                <tr>
                    <th>Indien gebruiker reeds bestaat?</th>
                    <th>
                        <?php echo maak_html_toggle("dubbels", "", true, "Bijwerken", "Negeren"); ?>
                    </th>
                </tr>
            </table>
            <br><br>
            <?php echo maak_dashboard_knop() ?>
            &nbsp;&nbsp;
            <?php echo maak_submit_knop("Importeren", "arrow-swap", "importeren", false, true, "warning"); ?>
<!-- footer includen -->
<?php include '/srv/prjtinapp' . '/php/pagina/gedeeld/footer.php'; ?>