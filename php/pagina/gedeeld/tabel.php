<?php
/**
 * Bevat alle functionaliteit om een tabel met filters, paginering & aanpasbaar aantal elementen te tonen op een pagina
 */

/**
 * Enum voor het soort van types bij te houden die we in tabelcel kunenn tonen
 */
abstract class TABELCEL_TYPE {
    const LEEG = 0;
    const LIJST = 1;
    const TOGGLE = 2;
    const DATUM = 3;
    const KNOP = 4;
}

/**
 * Enum voor het soort van types bij het bladeren door website
 */
abstract class BLADEREN_TYPE {
    const EERSTE = 0;
    const VOLGENDE = 1;
    const VORIGE = 2;
    const LAATSTE = 3;
}

/**
 * Enum voor het bijhouden hoeveel elementen er op 1 pagina vna een tabel getoond mogen worden
 */
abstract class TOON_AANTAL_ELEMENTEN_PER_PAGINA {
    const TIEN = 10;
    const VIJFENTWINTIG = 25;
    const VIJFTIG = 50;
    const ALLES = 0;
}

define ('BESCHIKBARE_AANTALLEN_ELEMENTEN_PER_PAGINA',    [
        [ "naam" => "10 rijen", "waarde" => TOON_AANTAL_ELEMENTEN_PER_PAGINA::TIEN ],
        [ "naam" => "25 rijen", "waarde" => TOON_AANTAL_ELEMENTEN_PER_PAGINA::VIJFENTWINTIG ],
        [ "naam" => "50 rijen", "waarde" => TOON_AANTAL_ELEMENTEN_PER_PAGINA::VIJFTIG ],
        [ "naam" => "Alles", "waarde" => TOON_AANTAL_ELEMENTEN_PER_PAGINA::ALLES ]
    ]);

/**
 * Maakt een filterrij voor de tabel
 * @param $filters
 * @param int $aantal_kolommen
 * @param $aantal_elementen_per_pagina
 * @return string
 */
function maak_tabel_filter_rij($filters, int $aantal_kolommen, $aantal_elementen_per_pagina=TOON_AANTAL_ELEMENTEN_PER_PAGINA::TIEN) {
    // Enkel filters tonen als deze zijn opgegeven)
    if ($filters !== []){
        $filter_rij = '<thead>';
        foreach($filters as $filter){
            $filter_rij .= '<th scope="col">';
            switch ($filter['type']){
                case TABELCEL_TYPE::LIJST:
                    $filter_rij .= call_user_func_array("maak_html_select", $filter['waarden']); break;
                case TABELCEL_TYPE::TOGGLE:
                    $filter_rij .= call_user_func_array("maak_html_toggle", $filter['waarden']); break;
                // lege kolom zonder filter
                default: break;
            }
            $filter_rij .= '</th>';
        }
        // laatste kolom = actie kolom, daarboven lijst tonen met Toon x rijen
        $filter_rij .= '<th colspan="' . ($aantal_kolommen - count($filters) - 1) . '"></th>';
        $filter_rij .= '<th>';
        $filter_rij .= maak_html_select("toon-aantal", 1, "this.form.submit", constant('BESCHIKBARE_AANTALLEN_ELEMENTEN_PER_PAGINA'), $aantal_elementen_per_pagina, "waarde", "naam");
        $filter_rij .= '</th>';
        $filter_rij .= '</thead>';
        return $filter_rij;
    }
    else {
        return "";
    }
}

/**
 * Maakt een toevoegrij voor de tabel
 * @param $kolommen
 * @return string
 */
function maak_tabel_toevoeg_rij($kolommen) {
    $toevoeg_rij = '<tr>';
    foreach($kolommen as $kolom){
        $toevoeg_rij .= '<td>';
        switch ($kolom['type']){
            case TABELCEL_TYPE::LIJST:
                $toevoeg_rij .= call_user_func_array("maak_html_select", $kolom['waarden']); break;
            case TABELCEL_TYPE::DATUM:
                $toevoeg_rij .= call_user_func_array("maak_datumveld", $kolom['waarden']); break;
            case TABELCEL_TYPE::KNOP:
                $toevoeg_rij .= call_user_func_array("maak_submit_knop", $kolom['waarden']); break;
        }
        $toevoeg_rij .= '</td>';
    }
    $toevoeg_rij .= '</tr>';
    return $toevoeg_rij;
}

/**
 * Maakt een headerrij voor de tabel
 * @param $headers
 * @return string
 */
function maak_tabel_header_rij($headers) {
    $header_rij = '<thead class="thead-dark">';
    foreach($headers as $header){
        $header_rij .= '<th scope="col">' . $header . '</th>';
    }
    $header_rij .= '</thead>';
    return $header_rij;
}


/**
 * Maakt een rij voor de tabel zodat gebladerd kan worden door de resultaten
 * @param $filters
 * @return string
 */
function maak_tabel_bladeren_rij($nr_kolom, $huidige_pagina, $max_paginas) {
    $bladeren_rij = '<tr class="bladeren_rij">';
    // eerst lege kolommen aanmaken VOOR de cel met bladeren
    $bladeren_rij .= '<td colspan="' . $nr_kolom. '">';
    // bladeren cel maken
    // naar eerste pagina (disabled indien op eerste pagina)
    $bladeren_rij .= maak_blader_knop("", "skip-backward", "eerste-pagina", false, false, "link", $huidige_pagina !== 1);
    // naar vorige pagina (disabled indien op eerste pagina)
    $bladeren_rij .= maak_blader_knop("", "arrow-left-b", "vorige-pagina", false, false, "link", $huidige_pagina !== 1);
    $bladeren_rij .= "$huidige_pagina/$max_paginas";
    // naar volgende pagina (disabled indien op laatste pagina)
    $bladeren_rij .= maak_blader_knop("", "arrow-right-b", "volgende-pagina", false, false, "link", $huidige_pagina !== $max_paginas);
    // naar laatste pagina (disabled indien op laatste pagina)
    $bladeren_rij .= maak_blader_knop("", "skip-forward", "laatste-pagina", false, false, "link", $huidige_pagina !== $max_paginas);
    $bladeren_rij .= '</td>';
    // rij afsluiten
    $bladeren_rij .= '</tr>';
    // gemaakte hmtl code teruggeven
    return $bladeren_rij;
}

function maak_blader_knop($tekst, $icoon_klasse, $knop_naam, $volle_lijn=true, $validatie=true, $knop_klasse="primary", $enabled=true) {
    $submit_knop = '<button type="submit" name="' . $knop_naam . '" class="btn ';
    if($volle_lijn) { $submit_knop .= ' btn-block '; }
    $submit_knop .= ' btn-' . $knop_klasse. '"';
    if(!$validatie) { $submit_knop .= " formnovalidate "; }
    if(!$enabled) { $submit_knop .= " disabled "; }
    $submit_knop .= '>'.
        '<i class="ion-' . $icoon_klasse. '"></i>&nbsp;' . $tekst.
        '</button>';
    return $submit_knop;
}


/**
 * maakt een html tabel aan
 * @param array $filters
 * @param array $headers
 * @param $data_ids
 * @param $data
 * @param string $datatype
 * @param int $huidige_pagina
 * @param int $max_paginas
 * @param int $aantal_elementen_per_pagina
 * @param array $nieuwe_rij
 * @param array $acties
 * @return string
 */
function maak_tabel(array $filters, array $headers, array $data_ids, array $data, string $datatype, int $huidige_pagina, int $max_paginas, int $aantal_elementen_per_pagina, array $nieuwe_rij=[], array $acties=[]) {
    $aantal_kolommen_tabel = count($headers);
    $html_tabel = '<div class="table-responsive">'.
        '<table class="table table-hover">';
    $html_tabel .= maak_tabel_filter_rij($filters, $aantal_kolommen_tabel, $aantal_elementen_per_pagina);
    $html_tabel .= maak_tabel_header_rij($headers);
    $html_tabel .= '<tbody>';
    $aantal_elementen = count($data);
    $met_bladeren="1";
    // Exact aantal elementen invullen indien we alles willen zien
    if($aantal_elementen_per_pagina === TOON_AANTAL_ELEMENTEN_PER_PAGINA::ALLES){
        $met_bladeren="0";
        $aantal_elementen_per_pagina = $aantal_elementen;
    }
    // Er zijn geen elementen dus "fout"boodschap tonen
    if ($aantal_elementen <= 0) {
        $html_tabel .=  '<tr><td colspan="' . $aantal_kolommen_tabel . '">'. "Er zijn geen " . $datatype. " die voldoen aan de geselecteerde filters." . '</td></tr>';
    }
    // tabel opvullen met warden
    else {
        // start index in tabel bepalen
        $huidig_element = ($huidige_pagina - 1) * $aantal_elementen_per_pagina;
        // bijhouden hoeveel elementen we al getoond hebben
        $aantal_elementen_getoond = 0;
        // elementen voor de pagina toevoegen
        while ($huidig_element < $aantal_elementen && $aantal_elementen_getoond < $aantal_elementen_per_pagina) {
            if (isset($acties[$huidig_element])){
                $html_tabel .= maak_tabel_detail_rij($data_ids[$huidig_element], $data[$huidig_element], $acties[$huidig_element]);
            }
            else {
                $html_tabel .= maak_tabel_detail_rij($data_ids[$huidig_element], $data[$huidig_element], []);
            }
            $huidig_element += 1;
            $aantal_elementen_getoond += 1;
        }
    }
    if($nieuwe_rij !== []) {
        $html_tabel .= maak_tabel_toevoeg_rij($nieuwe_rij);
    }
    if ($met_bladeren === "1") {
        $html_tabel .= maak_tabel_bladeren_rij($aantal_kolommen_tabel, $huidige_pagina, $max_paginas);
    }
    $html_tabel .= '</tbody></table></div>';
    return $html_tabel;
}

/**
 * @param $rij_id
 * @param array $details_rij
 * @param bool $acties_toevoegen          Duidt aan of er actie moeten toevoegd worden of niet
 * @param bool $verwijderen
 * @param bool $toevoegen
 * @param bool $opslaan
 * @param bool $openen
 * @return string
 */
function maak_tabel_detail_rij($rij_id, array $details_rij, array $acties){
    $html_rij = '<tr>';
    // details van de rij toevoegen
    foreach($details_rij as $detail_rij){
        $html_rij .= '<td>' . $detail_rij .'</td>';
    }
    // acties toevoeven voor deze rij
    if (count($acties) > 0) {
        $html_rij .= '<td>';
        if (array_key_exists("toevoegen", $acties) && $acties['toevoegen'] === true) {
            $html_rij .= maak_submit_knop("", "plus", "toevoegen", false, true, "link");
        }
        if (array_key_exists( "openen", $acties) && $acties['openen'] === true) {
            $html_rij .= maak_submit_knop("", "search", "openen", false, false, "link", "submit_na_opslaan_id('" . $rij_id . "');");
        }
        if (array_key_exists( "bewerken", $acties) && $acties['bewerken'] === true) {
            $html_rij .= maak_submit_knop("", "edit ", "bewerken", false, false, "link", "submit_na_opslaan_id('" . $rij_id . "');");
        }
        if (array_key_exists("wissen", $acties) && $acties['wissen'] === true) {
            $html_rij .= maak_submit_knop("", "loop", "wissen", false, false, "link", "submit_na_opslaan_id('" . $rij_id . "');");
        }
        if (array_key_exists("verwijderen", $acties) && $acties['verwijderen'] === true) {
            $html_rij .= maak_submit_knop("", "trash-a", "verwijderen", false, false, "link", "submit_na_opslaan_id('" . $rij_id . "');");
        }
        if (array_key_exists( "opslaan", $acties) && $acties['opslaan'] === true) {
            $html_rij .= maak_submit_knop("", "archive", "opslaan", false, false, "link", "submit_na_opslaan_id('" . $rij_id . "');");
        }
    }
    $html_rij .= '</td>';
    $html_rij .= '</tr>';
    return $html_rij;
}
