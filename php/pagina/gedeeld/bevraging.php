<?php
/**
 * Zet een bevraging om in toonbare htmlcode
 * @param Bevraging $bevraging    De bevraging
 * @param $modus        De modus waarin html getoond wordt (enum: BEVRAGING_MODUS)
 * @return string       De htmlcode
 */
function maak_htmlbevraging(Bevraging $bevraging) : string {
    $vraagnr = 1;

    $html_code = '<div class="form-group">';
    $html_code .= '<br><table>';
    // Elke vraag uit de vragenlijst tonen
    foreach($bevraging->geef_vragenlijst()->geef_vragen() as $vraag) {
        $html_code .= maak_htmlrij_bevraging($vraagnr, $vraag);
        // lege rij toevoegen als buffer
        $html_code .= '<tr><td>&nbsp;</td></tr>';
        $vraagnr += 1;
    }
    $html_code .= '</table>';
    $html_code .= '</div>';
    return $html_code;
}

/**
 * Zet een bevraging om in toonbare htmlcode
 * @param Bevraging $bevraging    De bevraging
 * @param $modus        De modus waarin html getoond wordt (enum: BEVRAGING_MODUS)
 * @return string       De htmlcode
 */
function maak_htmlbevraging_div(Bevraging $bevraging) : string {
    $vraagnr = 1;

    $html_code = '<div class="container-inhoud">';
    // Elke vraag uit de vragenlijst tonen
    foreach($bevraging->geef_vragenlijst()->geef_vragen() as $vraag) {
        $html_code .= maak_htmlrij_bevraging_div($vraagnr, $vraag);
        // lege rij toevoegen als buffer
        $html_code .= '<div class="row">&nbsp;</div>';
        $vraagnr += 1;
    }
    $html_code .= '</div>';
    return $html_code;
}


/**
 * Zet een vraag van een bevraging om in toonbare htmlcode
 * @param $vraagnr              Het nr van de vraag
 * @param Vraag $vraag          De vraagtekst
 * @param $eerste_antwoord      Het eerste mogelijke antwoord (indien meerkeuze)
 * @param $laatste_antwoord     Het laatste mogelijke antwoord (indien meerkeuze)
 * @param $antwoorden           Alle antwoorden
 * @return string               De htmlcode
 */
function maak_htmlrij_bevraging_div($vraagnr, Vraag $vraag) : string {
    $antwoordgroep = $vraag->geef_antwoordgroep();
    // header rij
    $html_rij = '<div class="row col-12">' .
                    '<div class="col-5 vraagtitel">Vraag ' . $vraagnr . '</div>';
    // indien schaal type de eerste en laatste schaaltekst toevoegen
    if($vraag->geef_vraagtype() == "schaal"){
        $html_rij .= '<div class="col-3 vraagtitel tekstrechts">' . $antwoordgroep->geef_eerste_antwoord()->geef_tekst() . '</div>';
        $html_rij .= '<div class="col-1">&nbsp;</div>';
        $html_rij .= '<div class="col-3 vraagtitel tekstlinks">' . $antwoordgroep->geef_laatste_antwoord()->geef_tekst() .  '</div>';
    }
    $html_rij .= '</div>';
    // inhoud van de vraag
    $aantal_antwoorden = count($antwoordgroep->geef_antwoorden());
    $html_rij .= '<div class="row col-12">' .
                    '<div class="col-5 tekstlinks">' . $vraag->geef_tekst() . '</div>' .
                    '<div class="col-7 tekstmidden">' . maak_html_antwoordgroep($vraag) . '</div>' .
                '</div>';
    return $html_rij;
}

/**
 * Zet een vraag van een bevraging om in toonbare htmlcode
 * @param $vraagnr              Het nr van de vraag
 * @param Vraag $vraag          De vraagtekst
 * @param $eerste_antwoord      Het eerste mogelijke antwoord (indien meerkeuze)
 * @param $laatste_antwoord     Het laatste mogelijke antwoord (indien meerkeuze)
 * @param $antwoorden           Alle antwoorden
 * @return string               De htmlcode
 */
function maak_htmlrij_bevraging($vraagnr, Vraag $vraag) : string {
    $antwoordgroep = $vraag->geef_antwoordgroep();
    // header rij
    $html_rij = '<tr>' .
                    '<th>Vraag ' . $vraagnr . '</th>';
    // indien schaal type de eerste en laatste schaaltekst toevoegen
    if($vraag->geef_vraagtype() == "schaal"){
        $html_rij .= '<th>' . $antwoordgroep->geef_eerste_antwoord()->geef_tekst() . '</th>';
        $html_rij .= '<th>&nbsp;</th>';
        $html_rij .= '<th>' . $antwoordgroep->geef_laatste_antwoord()->geef_tekst() .  '</th>';
    }
    $html_rij .= '</tr>';
    // inhoud van de vraag
    $aantal_antwoorden = count($antwoordgroep->geef_antwoorden());
    $html_rij .= '<tr>'.
                    '<td>' . $vraag->geef_tekst() . '</td>' .
                    '<td style="text-align: center;" colspan="3">' . maak_html_antwoordgroep($vraag) . '</td>' .
                '</tr>';
    return $html_rij;
}

/**
 * @param Vraag $vraag
 * @return string
 */
function maak_html_antwoordgroep(Vraag $vraag) : string {
    // op basis van het type van de vraag een andere voorstelling
    switch ($vraag->geef_vraagtype()){
        case "open": return maak_html_tekstvak($vraag->geef_id(), $vraag->geef_antwoord());
        case "meerkeuze" : return maak_html_meerkeuze($vraag->geef_id(), $vraag->geef_antwoordgroep(), $vraag->geef_antwoord());
        case "schaal" : return maak_html_schaal($vraag->geef_id(), $vraag->geef_antwoordgroep(), $vraag->geef_antwoord());
        default: return "";
    }
}

/**
 * @param string $id
 * @param string $antwoord
 * @return string
 */
function maak_html_schaal (int $id, AntwoordGroep $antwoordgroep, Antwoord $antwoord=null)  : string {
    $html_schaal = "";
    // de huidige antworodgroep mappen en teovoegen aan de lijst
    foreach($antwoordgroep->geef_antwoorden() as $antwoord_uit_groep){
        $html_schaal .= '<input type="radio" name="vraag-' . $id. '" value="' . $antwoord_uit_groep->geef_id() . '"';
        // Nog niet geantwoord, dus verplicht
        if($antwoord === null){
            $html_schaal .= ' required="required" />';
        }
        // al geantwoord dus disabled
        else {
            // kijken of dit antwoord het gekozen antwoord is
            if($antwoord_uit_groep->geef_id() === $antwoord->geef_id()){
                $html_schaal .= ' checked="checked" ';
            }
            $html_schaal .= ' disabled="disabled" />';
        }
    }
    return $html_schaal;
}

/**
 * @param string $id
 * @param string $antwoord
 * @return string
 */
function maak_html_meerkeuze (int $id, AntwoordGroep $antwoordgroep, Antwoord $antwoord=null)  : string {
    // antwoordgroep omvorgen naar named tabel om generieke methode voor select te maken op te kunne roepen
    $lijst = [
        // algemene waarde toevoegen
        [
            "naam" => "-- Kies waarde --",
            "waarde" => ""
        ]
    ];
    // de huidige antworodgroep mappen en teovoegen aan de lijst
    foreach($antwoordgroep->geef_antwoorden() as $antwoord_uit_groep){
        $lijst[] = [
                    "naam" => $antwoord_uit_groep->geef_tekst(),
                    "waarde" => $antwoord_uit_groep->geef_id()
                    ];
    }
    // indien geen antwoord, dus verplicht te kiezen, en actief maken
    $is_verplicht = $antwoord === null;
    $is_actief = $antwoord === null;
    $gekozen_waarde = "";
    // al geantwoord dus antwoord opvragen
    if(!$is_verplicht){
        $gekozen_waarde = $antwoord->geef_id();
    }
    return  maak_html_select ("vraag-" . $id, 1, "", $lijst,
                               $gekozen_waarde, "waarde", "naam", $is_verplicht, $is_actief);
}

/**
 * @param string $id
 * @param string $antwoord
 * @return string
 */
function maak_html_tekstvak (int $id, Antwoord $antwoord=null)  : string{
    $html_tekstvak = '<textarea rows="8" style="width: 100%;" name="vraag-' . $id. '" ';
    // geen antwoord dus invulbaar & verplicht
    if($antwoord === null) {
        $html_tekstvak .= ' required="required">';
    }
    // al ingevuld, dus antwoord tonen en disablen
    else {
        $html_tekstvak .= ' disabled="disabled">' . $antwoord->geef_tekst();
    }
    $html_tekstvak .=  '</textarea>';
    return $html_tekstvak;
}