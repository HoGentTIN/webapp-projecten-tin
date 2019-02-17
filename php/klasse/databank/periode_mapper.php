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
            "ORDER BY jaar, zittijd";
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        // Lege lijst van periodes om de gemapte periodes bij te houden
        $periodes = [];
        // Elk resultaat omzetten naar een periode
        foreach($resultaten as $resultaat) {
            $jaar = $resultaat['jaar'];
            $zittijd = $resultaat['zittijd'];
            $van = new DateTime($resultaat['van']);
            $tot = new DateTime($resultaat['tot']);

            try {
                $periodes[] = new Periode($jaar, $zittijd, $van, $tot);
            }
            catch (ValidatieUitzondering $ve) {
            }
        }

        return $periodes;
    }

    /**
     * Verwijdert een periode
     * @param int $jaartal
     * @param int $zittijd
     * @return bool
     */
    public function verwijder_periode (int $jaar, int $zittijd) : bool {
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
    public function toevoegen_periode (int $jaar, int $zittijd, string $van, string $tot) : bool {
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