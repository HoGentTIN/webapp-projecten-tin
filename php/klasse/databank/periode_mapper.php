<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/periode.php';

class PeriodeMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Geeft alle periodes
     * @return Periode[]
     */
    public function geef_periodes() :  array {
        $sql = "SELECT * " .
            "FROM periode " .
            "ORDER BY academiejaar, zittijd";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        // Lege lijst van periodes om de gemapte periodes bij te houden
        $periodes = [];
        // Elk resultaat omzetten naar een periode
        foreach($resultaten as $resultaat) {
            $academiejaar = $resultaat['academiejaar'];
            $zittijd = $resultaat['zittijd'];
            $van = new DateTime($resultaat['van']);
            $tot = new DateTime($resultaat['tot']);

            try {
                $periodes[] = new Periode($academiejaar, $zittijd, $van, $tot);
            }
            catch (ValidatieUitzondering $ve) {
            }
        }

        return $periodes;
    }

    /**
     * Geeft alle academiejaren
     * @return int[]
     */
    public function geef_academiejaren() :  array {
        $sql = "SELECT distinct academiejaar " .
            "FROM periode " .
            "ORDER BY academiejaar desc";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        // Lege lijst van periodes om de gemapte periodes bij te houden
        $academiejaren = [];
        // Elk resultaat omzetten naar een periode
        foreach($resultaten as $resultaat) {
            $academiejaren[] = $resultaat['academiejaar'];
        }

        return $academiejaren;
    }

    /**
     * Geeft alle zittijden
     * @return int[]
     */
    public function geef_zittijden() :  array {
        $sql = "SELECT distinct zittijd " .
            "FROM periode " .
            "ORDER BY zittijd";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        // Lege lijst van periodes om de gemapte periodes bij te houden
        $zittijden = [];
        // Elk resultaat omzetten naar een periode
        foreach($resultaten as $resultaat) {
            $zittijden[] = $resultaat['zittijd'];
        }

        return $zittijden;
    }

    /**
     * Geeft een periode op basis van zijn id
     * @param int $id
     * @return Periode | null
     */
    public function geef_periode_via_id($id) :  Periode {
        $sql = "SELECT * " .
            "FROM periode " .
            "WHERE id LIKE :id ";
        $parameters = [
            [':id', $id]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        if (isset($resultaten[0])) {
            $resultaat = $resultaten[0];
            $academiejaar = $resultaat['academiejaar'];
            $zittijd = $resultaat['zittijd'];
            $van = new DateTime($resultaat['van']);
            $tot = new DateTime($resultaat['tot']);

            try {
                return new Periode($academiejaar, $zittijd, $van, $tot);
            } catch (ValidatieUitzondering $ve) {}
        }
        return null;
    }

    /**
     * Geeft een periode op basis van zijn academiejaar en zittijd
     * @param int $academiejaar
     * @param int $zittijd
     * @return Periode | null
     */
    public function geef_periode_via_aj_zt($academiejaar, $zittijd) :  Periode {
        $sql = "SELECT * " .
            "FROM periode " .
            "WHERE academiejaar = :aj " .
            "AND zittijd = :zt";
        $parameters = [
            [':aj', $academiejaar],
            [':zt', $zittijd]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        if (isset($resultaten[0])) {
            $resultaat = $resultaten[0];
            $van = new DateTime($resultaat['van']);
            $tot = new DateTime($resultaat['tot']);

            try {
                return new Periode($academiejaar, $zittijd, $van, $tot);
            } catch (ValidatieUitzondering $ve) {}
        }
        return null;
    }


    /**
     * Verwijdert een periode
     * @param int $academiejaar
     * @param int $zittijd
     * @return bool
     */
    public function verwijder_periode (int $academiejaar, int $zittijd) : bool {
        $sql = "DELETE " .
                "FROM periode " .
                "WHERE academiejaar = :aj " .
                "AND zittijd = :zt";
        $parameters = [
            [':aj', $academiejaar],
            [':zt', $zittijd]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters);
        // kijken we of we effectief iets verwijdert hebben
        return $resultaten > 0;
    }


    /**
     * Toevoegen een periode
     * @param int $academiejaar
     * @param int $zittijd
     * @param string $van
     * @param string $tot
     * @return bool
     */
    public function toevoegen_periode (int $academiejaar, int $zittijd, string $van, string $tot) : bool {
        $sql = "INSERT " .
            "INTO periode (academiejaar, zittijd, van, tot) " .
            "VALUES (:aj, :zt, :van, :tot)";
        $parameters = [
            [':aj', $academiejaar],
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