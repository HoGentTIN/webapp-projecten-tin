<?php
include_once '/srv/prjtinapp' . '/php/klasse/databank/mapper.php';
include_once '/srv/prjtinapp' . '/php/klasse/databank/vragenlijst_mapper.php';
include_once '/srv/prjtinapp' . '/php/klasse/databank/vraag_mapper.php';
include_once '/srv/prjtinapp' . '/php/klasse/databank/antwoordgroep_mapper.php';
include_once '/srv/prjtinapp' . '/php/klasse/domein/bevraging.php';

/**
 * Class BevragingMapper
 */
class BevragingMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Geeft de testers terug
     * @return array
     */
    public function geef_testers() : array {
        $sql = "SELECT DISTINCT gebruikersnaam, voornaam, familienaam ".
            "FROM gebruiker " .
            "JOIN bevraging on bevraging.gebruiker = gebruiker.gebruikersnaam " .
            "ORDER BY familienaam, voornaam";
        // Query uitvoeren en het aantal rijen opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        $testers=[];
        foreach($resultaten as $resultaat){
            $testers[] = [ $resultaat['gebruikersnaam'], $resultaat['voornaam'] . ' ' . $resultaat['familienaam']];
        }
        return $testers;
    }

    /**
     * Geeft de doelgroepen terug
     * @return string[]
     */
    public function geef_doelgroepen() : array {
        $sql = "SELECT DISTINCT(groepnaam) ".
            "FROM bevraging " .
            "ORDER BY groepnaam";
        // Query uitvoeren en het aantal rijen opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        $groepnamen=[];
        foreach($resultaten as $resultaat){
            $groepnamen[] = $resultaat['groepnaam'];
        }
        return $groepnamen;
    }

    /**
     * Geeft de types van vragenlijsten terug voor gekozen bevragingen
     * @param $huidige_doelgroep
     * @param $huidige_tester
     * @param $voltooid
     * @return string[]
     */
    public function geef_types_vragenlijsten($huidige_tester="%", $huidige_doelgroep="%", $voltooid=null) : array {
        $sql = "SELECT DISTINCT b.vragenlijst_naam ".
            "FROM bevraging as b " .
            "WHERE b.gebruiker LIKE :gn " .
            "AND b.groepnaam LIKE :grn ";
        // kijken of we alles willen of enkele (on)voltooide
        if(isset($voltooid)){
            $sql .= " AND b.voltooid is ";
            if($voltooid) {
                $sql .= "NOT ";
            }
            $sql .= "NULL";
        }
        $parameters = [
            [':gn', $huidige_tester],
            [':grn', $huidige_doelgroep]
        ];
        // Query uitvoeren en het aantal rijen opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        $types=[];
        // alle ids mappen naar de bevraging zelf
        foreach($resultaten as $resultaat){
            $types[] = $resultaat['vragenlijst_naam'];
        }
        return $types;
    }

    /**
     * Geeft de bevragingen terug
     * @param string $huidige_tester (Standaard % om iedereen te nemen)
     * @param string $huidige_doelgroep (Standaard % om iedereen te nemen)
     * @param bool $voltooid (Standard null)
     * @return array[] Bevraging
     */
    public function geef_bevragingen($huidige_tester="%", $huidige_doelgroep="%", $voltooid=null) : array {
        $sql = "SELECT id ".
            "FROM bevraging " .
            "WHERE gebruiker LIKE :gn " .
          //   "AND vragenlijst_naam LIKE :vn ";
            "AND groepnaam LIKE :grn ";
        // kijken of we alles willen of enkele (on)voltooide
        if(isset($voltooid)){
            $sql .= " AND voltooid is ";
            if($voltooid) {
                $sql .= "NOT ";
            }
            $sql .= "NULL";
        }
        $parameters = [
            [':gn', $huidige_tester],
            [':grn', $huidige_doelgroep]
        ];
        // Query uitvoeren en het aantal rijen opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        $bevragingen=[];
        // alle ids mappen naar de bevraging zelf
        foreach($resultaten as $resultaat){
            $bevragingen[] = $this->geef_bevraging(intval($resultaat[0]));
        }
        return $bevragingen;
    }

    /**
     * Geeft het aantal bevragingen terug
     * @param string $gebruikersnaam
     * @param bool|null $voltooid
     * @return int
     */
    public function geef_aantal_bevragingen($gebruikersnaam="", $voltooid=null) : int {
        $sql = "SELECT count(*) ".
                "FROM bevraging " .
                "WHERE gebruiker LIKE :gn ";
        // kijken of we alles willen of enkele (on)voltooide
        if(isset($voltooid)){
            $sql .= " AND voltooid is ";
            if($voltooid) {
                $sql .= "NOT ";
            }
            $sql .= "NULL";
        }
        $parameters = [
            [':gn', strtolower($gebruikersnaam)]
        ];
        // Query uitvoeren en het aantal rijen opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_COLUMN'), $sql, $parameters);
        // Aantal teruggeven als int
        return intval($resultaten);
    }

    /**
     * Geeft de bevragingen terug
     * @param string $gebruikersnaam
     * @param bool $als_lezer
     * @return array
     */
    public function controleer_toegang (string $gebruikersnaam, int $id, bool $als_lezer) : bool {
        $sql = "SELECT count(*) " .
            "FROM bevraging " .
            "WHERE id = :id ";
        // Indien lezer kan men de gelinkte gebruiker zijn of persoon die tot de doelgroep behoort
        if($als_lezer){
            $sql .= "AND (gebruiker LIKE :gn OR groepnaam LIKE (SELECT doelgroep " .
                                                                "FROM gebruiker " .
                                                                "WHERE gebruikersnaam LIKE :gn))";
        }
        // Indien invuller moet men de gelinkte gebruiker zijn
        else {
            $sql .= "AND gebruiker LIKE :gn";
        }
        $parameters = [
            [':id', $id],
            [':gn', strtolower($gebruikersnaam)]
        ];
        // Query uitvoeren en het aantal rijen opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_COLUMN'), $sql, $parameters);
        // kijken of we effectief 1 hebben gevonden
        return $resultaten[0] === "1";
    }

    /**
     * @param int $id
     * @return Bevraging|null
     */
    public function geef_bevraging(int $id)  {
        // antwoordgroep voor deze vraag ophalen en mappen
        $sql = "SELECT groepnaam, voltooid, deadline, vragenlijst_naam, gebruiker, is_anoniem, met_score, score " .
            "FROM bevraging " .
            "WHERE id = :id ";
        $parameters = [
            [':id', $id]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);

        // geen Bevraging gevonden dus null teruggeven
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() <= 0) { return null; }
        // Vragenlijstmappen
        $resultaat = $resultaten[0];
        $doelgroep = $resultaat['groepnaam'];
        $deadline = new DateTime($resultaat['deadline']);
        // enkel datum invullen indien opgevuld
        $voltooid_op = ($resultaat['voltooid'] !== null) ? new DateTime($resultaat['voltooid']) : null;
        // is anoniem?
        $is_anoniem = ($resultaat['is_anoniem'] === "J") ? true: false;
        // met score?
        $met_score = ($resultaat['met_score'] === "J") ? true: false;
        $score = floatval($resultaat['score']);
        // indien ingediend, ophalen door wie
        $gm = new GebruikerMapper();
        $intedienen_door= $gm->geef_gebruiker($resultaat['gebruiker']);
        // vragenlijst mappen
        $vlm = new VragenlijstMapper();
        $vragenlijst = $vlm->geef_vragenlijst($resultaat['vragenlijst_naam']);
        // Antwoorden aan de vragn koppelen
        $sql = "SELECT vraagid, antwoord " .
            "FROM bevraging_resultaat " .
            "WHERE bevragingid = :id ";
        $parameters = [
            [':id', $id]
        ];
        // Query uitvoeren en alle resultaten opvragen
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        // resultaten overlopen
        foreach($resultaten as $resultaat){
            // vraag ophalen
            $vraag_id = intval($resultaat['vraagid']);
            $vraag = $vragenlijst->geef_vraag($vraag_id);
            // open vraag dus zelf een antwoord "maken"
            if($vraag->geef_vraagtype() === "open") {
                $vraag->zet_antwoord(new Antwoord(0, $resultaat['antwoord']));
            }
            // vast antwoord gekozen via zijn id
            else {
                $vraag->zet_antwoord_via_id(intval($resultaat['antwoord']));
            }
        }

        // De bevraging teruggeven
        return new Bevraging($id, $doelgroep, $vragenlijst, $intedienen_door, $deadline, $voltooid_op, $is_anoniem, $met_score, $score);
    }

    /**
     * @param string $gebruikersnaam
     * @param Bevraging $bevraging
     * @return bool
     */
    public function indienen_bevraging(Bevraging $bevraging) : bool {
        // Voor elke vraag het antworod opslaan
        foreach($bevraging->geef_vragenlijst()->geef_vragen() as $vraag) {
            // kijken of er wel een antwoord is
            if($vraag->geef_antwoord() !== null) {
                $sql = "INSERT INTO bevraging_resultaat " .
                    "VALUES (:bid, :vid, :antw)";
                $parameters = [
                    [':bid', $bevraging->geef_id()],
                    [':vid', $vraag->geef_id()]
                ];
                // indien type vraag "open" is het antwoord opslaan ipv id
                if ($vraag->geef_vraagtype() === "open") {
                    $parameters[] = [':antw', $vraag->geef_antwoord()->geef_tekst()];
                } else {
                    $parameters[] = [':antw', $vraag->geef_antwoord()->geef_id()];
                }
                // Query uitvoeren en aantal gewijzigde rijen opvragen
                $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('INSERT'), $sql, $parameters);
                // Geen rij toevoegd dus rollbacken
                if ($aantal_rijen !== 1) {
                    // reeds toegevoegde antwoorden verwijderen
                    $this->verwijder_antwoorden($bevraging->geef_id());
                    return false;
                }
            }
            // niet alle vragen zijn beantwoord
            else {
                // reeds toegevoegde antwoorden verwijderen
                $this->verwijder_antwoorden($bevraging->geef_id());
                return false;
            }
        }
        $score = 0;
        // update de score
        if ($bevraging->met_score()) {
            $score = $bevraging->geef_score();
        }
        $sql = "UPDATE bevraging " .
            "SET voltooid = :v, score = :s " .
            "WHERE id = :bid";
        $parameters = [
            [':bid', $bevraging->geef_id()],
            [':v', (new DateTime())->format('Y-m-d H:i:s')],
            [':s', $score]

        ];
        // Query uitvoeren en aantal gewijzigde rijen opvragen
        $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('UPDATE'), $sql, $parameters);
        // Kon bevraging niet indienden
        if($aantal_rijen !== 1) {
            // reeds toegevoegde antwoorden verwijderen
            $this->verwijder_antwoorden($bevraging->geef_id());
            return false;
        }

        // De bevraging teruggeven
        return true;
    }

    /**
     * @param int $bevragingid
     * @return bool
     */
    private function verwijder_antwoorden(int $bevragingid) : bool{
        $sql = "DELETE FROM bevraging_resultaat ".
            "WHERE bevragingid = :bid";
        $parameters = [
            [':bid', $bevragingid]
        ];
        $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters);
        return $aantal_rijen > 0;
    }

    /**
     * @param int $bevragingid
     * @return bool
     */
    public function verwijderen_bevraging(int $bevragingid) : bool {
        //Antwoorden verwijderen
        if($this->verwijder_antwoorden($bevragingid)) {
            // details zelf verwijderen
            $sql = "DELETE FROM bevraging ".
                    "WHERE id = :bid";
            $parameters = [
                [':bid', $bevragingid]
            ];
            $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters);
            // Moet exact 1 zijn
            return $aantal_rijen === 1;
        }
        // niet gelukt te verwijderen
        return false;
    }

    /**
     * @param int $bevragingid
     * @return bool
     */
    public function wissen_bevraging(int $bevragingid) : bool {
        //Antwoorden verwijderen
        if($this->verwijder_antwoorden($bevragingid)) {
            // details zelf verwijderen
            $sql = "UPDATE bevraging ".
                    "SET voltooid = null " .
                    "WHERE id = :bid";
            $parameters = [
                [':bid', $bevragingid]
            ];
            $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters);
            // Moet exact 1 zijn
            return $aantal_rijen === 1;
        }
        // niet gelukt te verwijderen
        return false;
    }
}