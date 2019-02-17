<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/opleidings_onderdeel.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/project_groep.php';


class ProjectGroepMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Geeft alle projectgroepen voor 1 olod en 1 periode, voor bepaalde begeleiders
     * @param int $olod
     * @paran Periode $periode
     * @param string $begeleider
     * @return OpleidingsOnderdeel[]
     */
    public function geef_project_groepen(int $olod, Periode $periode, string $begeleider="%") :  array {
        $sql = "SELECT id, naam, gebr_gebrnaam ".
                "FROM bevragingen.project_groep " .
                "WHERE gebr_gebrnaam like :begeleider " .
                "AND olod_id = :olod " .
                "AND periode_jaar like :acadjaar " .
                "AND periode_zittijd like :zittijd ";
        $parameters = [
            [':begeleider', $begeleider],
            [':olod', $olod],
            [':acadjaar', $periode->geef_academie_jaar()],
            [':zittijd', $periode->geef_zittijd()]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        // Lege lijst van periodes om de gemapte periodes bij te houden
        $project_groepen = [];
        $huidige_pg = "";

        $gm = new GebruikerMapper();

        foreach($resultaten as $resultaat){
            $pg_id = intval($resultaat['id']);
            $pg_naam = $resultaat['naam'];
            if ($huidige_pg === "" or $huidige_pg->geef_naam() !== $pg_naam){
                $huidige_pg = new Project_Groep($pg_id, $pg_naam, $periode);
                $project_groepen[] = $huidige_pg;
            }
            $pg_lid = $resultaat['gebr_gebrnaam'];
            $lid = $gm->geef_gebruiker($pg_lid);
            $huidige_pg->voeg_lid_toe($lid);
        }

        return $project_groepen;
    }

    /**
     * Geeft een opleiding
     * @return Opleiding|null
     */
    public function geef_project_groep(int $id)  {
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