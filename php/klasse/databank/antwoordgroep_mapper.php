<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/databank/mapper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/antwoord_groep.php';

/**
 * Class AntwoordGroepMapper
 */
class AntwoordGroepMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return AntwoordGroep[]
     */
    public function geef_antwoordgroepen() : array {
        // antwoordgroep voor deze vraag ophalen en mappen
        $sql = "SELECT naam, antwoord_id " .
                "FROM antwoord_groep " .
                "ORDER BY naam, volgorde";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);

        // Lege lijst van periodes om de gemapte periodes bij te houden
        $antwoord_groepen = [];
        // geen groepen gevonden dus lege lijst teruggeven
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() <= 0) { return []; }
        // eerste gevonden groep instellen met zijn naam
        $huidige_naam = $resultaten[0]['naam'];
        // nieuwe lijst antwoorden voor deze antwoordgroep
        $antwoorden = [];
        // Elk resultaat omzetten naar een antwoordgroep
        foreach($resultaten as $resultaat) {
            $naam = $resultaat['naam'];
            if($naam !== $huidige_naam) {
                // vorige antwoordgroep toevoegen aan de lijst
                $antwoord_groepen[] = new AntwoordGroep($huidige_naam, $antwoorden);
                $huidige_naam = $naam;
                // nieuwe lijst antwoorden voor deze antwoordgroep
                $antwoorden = [];
            }
            $antwoord_id= $resultaat['antwoord_id'];
            // antwoord ophalen uit databank
            $antwoorden[] = $this->geef_antwoord($antwoord_id);
        }
        // laatste antwoordgroep toevoegen
        $antwoord_groepen[] = new AntwoordGroep($huidige_naam, $antwoorden);

        return $antwoord_groepen;
    }

    /**
     * @param $id
     * @return Antwoord|null
     */
    public function geef_antwoord($id) {
        // antwoordgroep voor deze vraag ophalen en mappen
        $sql = "SELECT waarde " .
                "FROM antwoord " .
                "WHERE id = :id";
        $parameters = [
            [':id', $id]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaat = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_COLUMN'), $sql, $parameters);
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() != 1) {
            return null;
        }
        else {
            return new Antwoord($id, $resultaat);
        }
    }


    /**
     * @param string naam
     * @return AntwoordGroep|null
     */
    public function geef_antwoordgroep(string $naam) {
        // antwoordgroep voor deze vraag ophalen en mappen
        $sql = "SELECT antwoord_id " .
            "FROM antwoord_groep " .
            "WHERE naam = :naam " .
            "ORDER BY volgorde ";
        $parameters = [
            [':naam', $naam]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);

        // geen AntwoordGroep gevonden dus null teruggeven
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() <= 0) { return null; }
        // nieuwe lijst antwoorden voor deze antwoordgroep
        $antwoorden = [];
        // Elk antwoord van deze groep teovoegen
        foreach($resultaten as $resultaat) {
            $antwoord_id= $resultaat['antwoord_id'];
            // antwoord ophalen uit databank
            $antwoorden[] = $this->geef_antwoord($antwoord_id);
        }
        // laatste antwoordgroep toevoegen
        return new AntwoordGroep($naam, $antwoorden);
    }
}