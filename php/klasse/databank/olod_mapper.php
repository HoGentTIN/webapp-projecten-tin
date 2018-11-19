<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/databank/mapper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/opleiding.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/opleidings_onderdeel.php';


class OlodMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Geeft alle olods
     * @return OpleidingsOnderdeel[]
     */
    public function geef_olods() :  array {
        $sql = "SELECT o.id, o.naam, o_o.opleiding_id as 'oplid', opl.naam as 'oplnaam' " .
            "FROM olod as o " .
            "JOIN opleiding_olod as o_o on o_o.olod_id = o.id " .
            "JOIN opleiding as opl on opl.id = o_o.opleiding_id " .
            "ORDER BY o.naam";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        // Lege lijst van periodes om de gemapte periodes bij te houden
        $olods = [];
        foreach($resultaten as $resultaat){
            $olod_id = intval($resultaat['id']);
            $olod_naam = $resultaat['naam'];
            $opl_id = intval($resultaat['oplid']);
            $opl_naam = $resultaat['oplnaam'];
            $opl = new Opleiding($opl_id, $opl_naam);
            $olods[] = new OpleidingsOnderdeel($olod_id, $olod_naam, $opl);
        }
        return $olods;
    }

    /**
     * Geeft een olod
     * @return OpleidingsOnderdeel|null
     */
    public function geef_olod(int $id) {
        // opleiding selecteren voor dit olod
        $sql = "SELECT o.naam, o_o.opleiding_id as 'oplid' " .
            "FROM olod as o " .
            "JOIN opleiding_olod as o_o on o_o.olod_id = o.id " .
            "WHERE o.id = :olodid";
        $parameters = [
            [':olodid', $id]
        ];
        // Query uitvoeren en enkel eerste resultaat opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        $olod = null;
        // willen maar 1 resultaat, anders null
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() === 1){
            $opl = $this->geef_opleiding($resultaten[0]['oplid']);
            // opleiding gevondne dus nu ook opleidingsonderdeel mappen
            if($opl !== null){
                $naam = $resultaten[0]['naam'];
                $olod = new OpleidingsOnderdeel($id, $naam, $opl);
            }
        }

        return $olod;
    }

    /**
     * Geeft alle opleidingen
     * @return Opleiding[]
     */
    public function geef_opleidingen() :  array {
        $sql = "SELECT id, naam " .
            "FROM opleiding " .
            "ORDER BY naam";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        // Lege lijst van periodes om de gemapte periodes bij te houden
        $opleidingen = [];
        // Elk resultaat omzetten naar een periode
        foreach($resultaten as $resultaat) {
            $id = intval($resultaat['id']);
            $naam = $resultaat['naam'];
            $opleidingen[] = new Opleiding($id, $naam);
        }

        return $opleidingen;
    }


    /**
     * Geeft een opleiding
     * @return Opleiding|null
     */
    public function geef_opleiding(int $id)  {
        // opleiding selecteren voor dit olod
        $sql = "SELECT naam " .
            "FROM opleiding " .
            "WHERE id = :oplid";
        $parameters = [
            [':oplid', $id]
        ];
        // Query uitvoeren en enkel eerste resultaat opvragen
        $resultaat = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_COLUMN'), $sql, $parameters);
        $opleiding = null;
        // willen maar 1 resultaat, anders null
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() === 1){
            $opleiding = new Opleiding($id, $resultaat);
        }

        return $opleiding;
    }

    /**
     * Verwijdert een periode
     * @param int $jaartal
     * @param int $zittijd
     * @return bool
     */
    public function verwijder_olod (int $jaar, int $zittijd) : bool {
        $sql = "DELETE " .
            "FROM periode " .
            "WHERE jaar = :jt " .
            "AND zittijd = :zt";
        $parameters = [
            [':jt', $jaar],
            [':zt', $zittijd]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters);
        // kijken we of we effectief iets verwijdert hebben
        return $resultaten > 0;
    }


    /**
     * Toevoegen een periode
     * @param int $jaartal
     * @param int $zittijd
     * @param string $van
     * @param string $tot
     * @return bool
     */
    public function toevoegen_olod (int $jaar, int $zittijd, string $van, string $tot) : bool {
        $sql = "INSERT " .
            "INTO periode (jaar, zittijd, van, tot) " .
            "VALUES (:jt, :zt, :van, :tot)";
        $parameters = [
            [':jt', $jaar],
            [':zt', $zittijd],
            [':van', $van],
            [':tot', $tot]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('INSERT'), $sql, $parameters);
        // kijken we of we effectief een periode hebben toegevoegd
        return $resultaten === 1;
    }
}