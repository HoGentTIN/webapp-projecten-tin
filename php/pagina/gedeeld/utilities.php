<?php
include_once '/srv/prjtinapp' . '/php/klasse/databank/enum/databank_fouten.php';
// klasse met alle standaard hulpmiddelen voor de website
class UTILITIES
{
    const TAB_TEKEN = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    /**
     * Methode die de waarde van een variabele teruggeeft indien opgevuld, anders de standaardwaarde
     * @param $variable         De variabele
     * @param null $standaard De standaardwaarde indien de variabele leeg is
     * @return null             De waarde
     */
    public static function GEEF_WAARDE_VARIABELE($variable, $standaard = null)
    {
        return isset($variable) ? $variable : $standaard;
    }

    /**
     * Methode die de index van een waarde geeft in een lijst, -1 indien niet gevonden
     * @param $lijst        De lijst
     * @param $waarde       De te zoeken waarde
     * @return int          De index (-1 indien niet gevonden)
     */
    public static function GEEF_INDEX_IN_LIJST($lijst, $waarde)
    {
        $index = 0;
        foreach ($lijst as $element) {
            // lower case vergelijken
            if (strcasecmp($element, $waarde) == 0) {
                return $index;
            }
            $index++;
        }
        return -1;
    }


    /**
     * Methode die de index van een waarde geeft in een lijst, -1 indien niet gevonden
     * @param array $lijst        De lijst
     * @param $waarde       De te zoeken waarde
     * @return          De sleutel voro de waarde;
     */
    public static function GEEF_SLEUTEL_VOOR_WAARDE(array $lijst, $waarde) {
        foreach ($lijst as $sleutel => $w) {
            // lower case vergelijken
            if (strcasecmp($w, $waarde) == 0) {
                return $sleutel;
            }
        }
        return null;
    }

    /**
     * Methode die de waarde van een bepaalde sleutel geeft, indien de waarde van de zoeksleutel overeenkomt
     * @param $lijst                De lijst
     * @param $zoek_waarde          De te zoeken waarde
     * @param $zoek_sleutel         De naam van de sleutel van de welke we de waarde zoeken
     * @param $resultaat_sleutel    De naam van de sleutel van de welke we de waarde als resultaat willen
     * @return object               De waarde voor $resultaat_sleutel
     */
    public static function GEEF_WAARDE_VOOR_SLEUTEL_VOOR_WAARDE($lijst, $zoek_waarde, $zoek_sleutel, $resultaat_sleutel)
    {
        // door de lijst van lijsten lopen
        foreach($lijst as $tabel) {
            // voor elke lijst de sleutels afgaan
            foreach ($tabel as $sleutel => $w) {
                // lower case vergelijken
                // Kijken of binnen de huidige lijst er een sleutel bestaat met gewenste waarde
                if (strcasecmp($sleutel, $zoek_sleutel) == 0 &&
                    strcasecmp($w, $zoek_waarde) == 0) {
                    // van de huidige lijst de waarde van de gewenste sleutel teruggeven
                    return $tabel[strtolower($resultaat_sleutel)];
                }
            }
        }
        return null;
    }

    /**
     * Parsed een CSV met scheidingsteken ;
     * @param string $naam                De naam in $_FILES waar het csv bestand zit
     * @param array $kenmerken_tabel      De kolomnamen in de databank die we per rij zoeken
     * @param array $kenmerken_bestand    De kolomnamen uit het bestand die we per rij nodig hebben
     * @return array                Resultaten
     */
    public static function PARSE_CSV(string $naam, array $kenmerken_tabel=[], array $kenmerken_bestand=[]) : array {
        // aangepaste delimiter ; (standard is ,)
        $csvAlsTabel = array_map(function ($v) { return str_getcsv($v, ";"); }, file($_FILES[$naam]["tmp_name"]));
        $aantal_records = count($csvAlsTabel) - 1;  // header rij niet meetellen
        // Minstens 0 "echte" rijen nodig (= zonder headerrij)
        if ($aantal_records < 0) {
            $resultaten = ["fout" => "Ongeldig CSV bestand"];
        }
        else {
            // eerste rij = headerrij
            $eerste_rij = true;
            // csv bestand parsen en resultaten bijhouden
            $kenmerken_parsed = [];
            // tabel die alle rijen bijhoudt met de waarden die nodig zijn uit de CSV
            $verwerkte_rijen = [];
            // elke rij uit het bestand overlopen
            foreach ($csvAlsTabel as $rij) {
                // Eerste rij = headerrij => Indexen zoeken die horen bij opgegeven kolomnamen
                if ($eerste_rij) {
                    $aantal_gevonden_kenmerken = 0;
                    $index = 0;
                    foreach ($rij as $kolom) {
                        // kijken of de kolomnaam uit het bestand te vinden is in de lijst die door gebruiker op website is ingegeven
                        $kenmerk = UTILITIES::GEEF_SLEUTEL_VOOR_WAARDE($kenmerken_bestand, $kolom);
                        // Kenmerk gevonden
                        if (isset($kenmerk)) {
                            // Kenmerk werd nog niet eerder gevonden
                            if (!isset($kenmerken_parsed[$kenmerk])) {
                                $kenmerken_parsed[$kenmerk] = $index;
                                $aantal_gevonden_kenmerken++;
                            } // al gevonden, dus error
                            else {
                                $resultaten = ["fout" => "Kon CSV niet parsen. Dubbele kolomnamen! (" . $kenmerken_parsed[$kenmerk]. ")"];
                                // break 2 lussen
                                break 2;
                            }
                        }
                        // kolomindex verhogen
                        $index++;
                    }

                    // kijken of de eerste rij alle kenmerkenbevatte
                    if ($aantal_gevonden_kenmerken != count($kenmerken_tabel)) {
                        $resultaten = ["fout" => "Kon CSV niet parsen. Niet alle kolomnamen (" . $aantal_gevonden_kenmerken . "/" . count($kenmerken_tabel) . ") werden gevonden!"];
                        break;
                    }
                    // eerte rij is geparsed dus vlag uitzetten
                    $eerste_rij = false;
                } // rij parsen
                else {
                    // tabel aanmaken voor de verwerkte resultaten van deze rij
                    $verwerkte_rij = [];
                    // voor elk kenmerk de waarde uit de bijhorende kolom op die rij ophalen
                    foreach ($kenmerken_tabel as $kenmerk) {
                        // Controleren of de kolom bestaat
                        // gebruikerkenmerk toevoegen
                        if (isset($rij[$kenmerken_parsed[$kenmerk]])) {
                            $verwerkte_rij[$kenmerk] = $rij[$kenmerken_parsed[$kenmerk]];
                        } // Kolom niet gevonden, dus ongeldige rij
                        else {
                            return [ "fout" => "Kon CSV niet parsen. Sommige rijen bevatten niet alle records!"];
                        }
                    }
                    $verwerkte_rijen[] = $verwerkte_rij;
                }
                // alles gelukt dus resultaten van parsing toevoegen a ntabel
                $resultaten = [ "resultaten" => $verwerkte_rijen ];
            }
            // opgeladen bestand verwijderen
            unlink($_FILES[$naam]["tmp_name"]);
        }
        // resultaten teruggeven
        return $resultaten;
    }
}
?>