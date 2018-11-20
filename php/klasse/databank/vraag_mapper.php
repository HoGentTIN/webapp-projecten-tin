<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/vraag.php';

/**
 * Class VraagMapper
 */
class VraagMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return Vraag[]
     */
    public function geef_vragen() : array {
        // antwoordgroep voor deze vraag ophalen en mappen
        $sql = "SELECT id, waarde, type " .
            "FROM vraag " .
            "ORDER BY id";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);

        // Lege lijst van periodes om de gemapte periodes bij te houden
        $vragen = [];
        // Elk resultaat omzetten naar een vraag
        foreach($resultaten as $resultaat) {
            $id = intval($resultaat['id']);
            $waarde = $resultaat['waarde'];
            $type = $resultaat['type'];
            $vragen[] = new  Vraag($id, $waarde, $type);
        }
        return $vragen;
    }

    /**
     * @return Vraag|null
     */
    public function geef_vraag(int $id)  {
        // antwoordgroep voor deze vraag ophalen en mappen
        $sql = "SELECT id, waarde, type " .
                "FROM vraag " .
                "WHERE id = :id";
        $parameters = [
            [':id', $id]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);

        // geen AntwoordGroep gevonden dus null teruggeven
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() <= 0) { return null; }
        // vraag mappen
        $resultaat = $resultaten[0];
        return new Vraag($id, $resultaat['waarde'], $resultaat['type']);
    }

    /**
     * @return string[]
     */
    public function geef_vraagtypes() : array {
        $sql = "SELECT DISTINCT(naam)" .
                "FROM vraagtype " .
                "ORDER BY naam";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        $vraagtypes=[];
        foreach($resultaten as $resultaat){
            $vraagtypes[] = $resultaat['naam'];
        }
        return $vraagtypes;
    }
}