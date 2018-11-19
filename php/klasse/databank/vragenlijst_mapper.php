<?php
include_once '/srv/prjtinapp' . '/php/klasse/databank/mapper.php';
include_once '/srv/prjtinapp' . '/php/klasse/databank/vraag_mapper.php';
include_once '/srv/prjtinapp' . '/php/klasse/databank/antwoordgroep_mapper.php';
include_once '/srv/prjtinapp' . '/php/klasse/domein/vragenlijst.php';

/**
 * Class VragenlijstMapper
 */
class VragenlijstMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Geeft de sjablonen terug
     * @param string $gebruikersnaam
     * @return Vragenlijst[]
     */
    public function geef_vragenlijsten($opleiding="") : array {

    }

    /**
     * @param string naam
     * @return Vragenlijst|null
     */
    public function geef_vragenlijst(string $naam) {
        // Unieke vragen voor dit vragenlijst in de juiste volgorde ophalen
        $sql = "SELECT DISTINCT(vraagid), vraagvolgorde, antwoordgroepnaam " .
            "FROM vragenlijst_vraag_antwoord " .
            "WHERE vragenlijstnaam = :naam " .
            "ORDER BY vraagvolgorde ";
        $parameters = [
            [':naam', $naam]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);

        // geen vragenlijst gevonden dus null teruggeven
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() <= 0) { return null; }
        // nieuwe lijst vragen voor dit vragenlijst

        $vragen = [];
        $vm = new VraagMapper();
        $agm = new AntwoordGroepMapper();
        // Elk vraag mappen
        foreach($resultaten as $resultaat) {
            $vraagid = intval($resultaat['vraagid']);
            // de antwoordgroep van de vraag mappen
            $antwoordgroepnaam = $resultaat['antwoordgroepnaam'];
            $antwoordgroep = $agm->geef_antwoordgroep($antwoordgroepnaam);
            // scores voor de antwoorden bij de vraag van dit vragenlijst ophalen
            $sql = "SELECT score " .
                    "FROM vragenlijst_vraag_antwoord " .
                    "WHERE vragenlijstnaam = :naam " .
                    "AND vraagid  = :vid ".
                    "ORDER BY antwoordgroepantw ";
            $parameters = [
                [':naam', $naam],
                [':vid', $vraagid]
            ];
            // Query uitvoeren en alle resultaten opvragen
            $scores = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
            // scores toevoegen aan de antwoordgroep
            $index = 0;
            foreach($scores as $score){
                $s = floatval($score['score']);
                $antwoordgroep->geef_antwoord($index)->zet_score($s);
                $index += 1;
            }
            // vraag mappen
            $vraag = $vm->geef_vraag($vraagid);
            $vraag->zet_antwoordgroep($antwoordgroep);

            // de antwoordgroep mappen bij deze vraag
           // , antwoordgroepnaam, antwoordgroepantw, score
            // De vraag toevoegen aan onze lijst
            $vragen[] = $vraag;
        }
        // Het vragenlijst aanmaken met onze vragen
        return new Vragenlijst($naam, $vragen);
    }
}