<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/olod_mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/enum/gebruiker_type.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/gebruiker.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/mailer.php';

class GebruikerMapper extends Mapper
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Geeft alle gebruikers (al dan niet van een bepaalde opleiding)
     * @param string $opleiding
     * @param string $olod
     * @return Gebruiker[]
     */
    public function geef_gebruikers(string $opleiding="%", string $olod="%") : array {
        // alle olods ophalen die voldoen aan de selectiecriteria
        $sql = "SELECT DISTINCT(gebr_gebrnaam) as 'gnaam'" .
                "FROM gebruiker_olod " .
                "WHERE olod_id in (SELECT DISTINCT(olod_id) " .
                                    "FROM opleiding_olod " .
                                    "WHERE opleiding_id in (SELECT DISTINCT(id) ".
                                                            "FROM opleiding ".
                                                            "WHERE naam LIKE :opl) " .
                                    "AND olod_id in (SELECT DISTINCT(id) ".
                                                    "FROM olod ".
                                                    "WHERE naam LIKE :olod)) " .
                "ORDER BY 1";
        $parameters = [
            [':opl', $opleiding],
            [':olod', $olod]
        ];

        $gebruikersnamen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        // lege array voor onze gebruikers
        $gebruikers=[];
        // gebruikers zoeken die gelinkt zijn aan zo een combinatie van olod / opleiding
        foreach($gebruikersnamen as $gebruikersnaam){
            $gebruikers[] = $this->geef_gebruiker($gebruikersnaam['gnaam']);
        }
        return $gebruikers;
    }

    /**
     * Geeft een gebruiker
     * @param string $gebruikersnaam
     * @param string $wachtwoord
     * @return Gebruiker|null
     */
    public function geef_gebruiker(string $gebruikersnaam, string $wachtwoord="")  {
        $sql = "SELECT voornaam, familienaam, wachtwoord, email, type, doelgroep, actief " .  //, huidige_periode " .
               "FROM gebruiker " .
               "WHERE gebruikersnaam LIKE :gn ";
        $parameters = [
                            [':gn', $gebruikersnaam]
                    ];
        $gebruikers = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
        // kijken of we exact 1 gebruiker hadden
        if (DATABANK::geef_instantie()->geef_sql_aantal_rijen() === 1) {
            // Kijken of er ook validatie moet zijn van het wachtwoord
            if($wachtwoord === "" || ($wachtwoord !== "" && password_verify($wachtwoord, $gebruikers[0]['wachtwoord']))){
                $gebruiker = $gebruikers[0];
                // Gebruiker details ophalen
                $gebruikerstype = $gebruiker['type'];
                $voornaam = $gebruiker['voornaam'];
                $familienaam = $gebruiker['familienaam'];
                $email = $gebruiker['email'];
                $doelgroep = $gebruiker['doelgroep'];
                $is_actief = ($gebruiker['actief'] === 'j' ? true : false);
                // geldig gebruikerstype, dus gebuiker aanmaken en teruggeven
                if(GEBRUIKER_TYPE::has_key($gebruikerstype)) {
                    $sql = "SELECT olod_id " .
                        "FROM gebruiker_olod " .
                        "WHERE gebr_gebrnaam LIKE :gn ";
                    $parameters = [
                        [':gn', $gebruikersnaam]
                    ];
                    $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);
                    $olods = [];
                    $om = new OlodMapper();
                    foreach($resultaten as $resultaat) {
                        $olod_id = intval($resultaat['olod_id']);
                        $olods[] = $om->geef_olod($olod_id);
                    }
                    // zijn huidig ingestelde periode ophalen
                    $pm = new PeriodeMapper();
                    $periode = null;//$pm->geef_periode_via_id($gebruiker['huidige_periode']);
                    // Gebruiker aanmaken met zijn waarden
                    return new Gebruiker($gebruikersnaam, $gebruikerstype, $voornaam, $familienaam, $email, $is_actief, $doelgroep, $olods, $periode);
                }
                //Ongeldig gebruikerstype!
                else {
                    $this->_fout = "Ongeldig gebruikerstype!";
                    return null;
                }
            }
            $this->_fout = "Gebruiker niet gevonden!";
            return null;
        }
        else {
            $this->_fout = "Gebruiker niet gevonden!";
            return null;
        }
    }

    /**
     * Wijzigen van een wachtwoord
     * @param string $gebruikersnaam   De gebruikersnaam
     * @param string $oud_wachtwoord   Het huidige wachtwoord
     * @param string $nieuw_wachtwoord Het nieuwe wachtwoord
     * @return bool                     Gelukt/niet gelukt
     */
    public function wijzig_wachtwoord(string $gebruikersnaam, string $oud_wachtwoord, string $nieuw_wachtwoord) : bool
    {
        $this->_fout="";
        // kijken of het huidige wachtwoord correct is & gebruiker moet actief zijn!
        // Huidig wachtwoord ophalen uit de db
        $sql = "SELECT wachtwoord " .
            "FROM gebruiker " .
            "WHERE gebruikersnaam LIKE :gn " .
            "AND actief = 'j'";
        $parameters = [
                            [':gn', $gebruikersnaam]
                        ];
        $huidig_wachtwoord = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_COLUMN'), $sql, $parameters);

        if(isset($huidig_wachtwoord)){
            // exact 1 gebruiker gewenst want gebruikersnaam is uniek
            // huidige wachtwoorden komen niet overeen
            if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() !== 1 || !password_verify($oud_wachtwoord, $huidig_wachtwoord)) {
                $this->_fout = '"Huidig wachtwoord" is niet juist voor deze gebruiker!';
                return false;
            }
            // controles ok, dus nieuw wachtworod instellen
            $nieuw_wachtwoord = password_hash($nieuw_wachtwoord, PASSWORD_DEFAULT);

            $sql = "UPDATE gebruiker ".
                "SET wachtwoord = :ww " .
                "WHERE gebruikersnaam LIKE :gn ";
            $parameters = [
                [':gn', $gebruikersnaam],
                [":ww", $nieuw_wachtwoord]
            ];
            $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('INSERT'), $sql, $parameters);

            if($aantal_rijen !== -1){
                return true;
            }
            else {
                $this->_fout = "Kon wachtwoord niet bijwerken.";
                return false;
            }
        }
        else {
            $this->_fout = DATABANK::geef_instantie()->geef_sql_fout();
            return false;
        }
    }

    /**
     * Reset een wachtwoord van een gebruiker
     * @param string $gebruikersnaam   De gebruikersnaam
     * @param string $nieuw_wachtwoord   Het nieuwe wachtwoord
     * @return bool             Gelukt/niet gelukt
     */
    public function reset_wachtwoord(string $gebruikersnaam, string $nieuw_wachtwoord) : bool
    {
        // Huidige wachtwoord van de actieve gebruiker ophalen
        $sql = "SELECT wachtwoord, email, voornaam, familienaam " .
            "FROM gebruiker " .
            "WHERE gebruikersnaam LIKE :gn " .
            "AND actief = 'j'";
        $parameters = [
                        [':gn', $gebruikersnaam]
                    ];
        $gebruikers = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql, $parameters);

        // exact 1 actieve gebruiker gewenst want gebruikersnaam is uniek
        if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() !== 1) {
            $this->_fout = "Ongeldige gebruiker!";
            return false;
        }

        $voornaam =$gebruikers[0]['voornaam'];
        $familienaam =$gebruikers[0]['familienaam'];
            // we krijgen tabel terug met 1 element dus eerste index opvragen
        $oud_wachtwoord = $gebruikers[0]['wachtwoord'];
        $email = $gebruikers[0]['email'];
        // nieuwe wachtwoord opslaan
        $sql = "UPDATE gebruiker ".
            "SET wachtwoord = :ww " .
            "WHERE gebruikersnaam LIKE :gn";
        $parameters = [
            [':gn', $gebruikersnaam],
            [":ww", password_hash($nieuw_wachtwoord, PASSWORD_DEFAULT) ] // nieuwe wachtwoord hashen
        ];
        $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('UPDATE'), $sql, $parameters);
        // kijken of we effectief 1 rij hebben bijgewerkt
        if($aantal_rijen !== 1) {
            $this->_fout = DATABANK::geef_instantie()->geef_sql_fout();
            return false;
        }
        // wachtwoord succesvol reset, dus email sturen
        $aan = $email;
        $onderwerp = "[Projecten2sus] Reset wachtwoord";
        //$boodschap = "Uw wachtwoord werd opnieuw ingesteld\nNieuwe wachtwoord: " .  $nieuw_wachtwoord ."\n\n\nGelieve niet op deze e-mail te antwoorden!";
        $boodschap = "Welkom " . $voornaam. " " . $familienaam . "\n\nU kan nu aanmelden op http://projecten2sus.ddns.net met volgende gegevens:\n".
                    "Gebruikersnaam: " . $gebruikersnaam . "\nWachtwoord: " .  $nieuw_wachtwoord ."\n\n\nGelieve niet op deze e-mail te antwoorden!" .
        "\nIndien u nood heeft aan ondersteuning kan u terecht bij sebastiaan.labijn@hogent.be";
        $mailer = new Mailer();
        // Kijken of we een e-mail kunnen versturen
        if(!$mailer->stuur_email($aan,$onderwerp, $boodschap)){
            // niet gelukt dus oude wachtwoord terugzetten
            $sql = "UPDATE gebruiker ".
                "SET wachtwoord = :ww " .
                "WHERE gebruikersnaam LIKE :gn";
            $parameters = [
                [':gn', $gebruikersnaam],
                [":ww", $oud_wachtwoord]
            ];
            DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('UPDATE'), $sql, $parameters);
            // foutboodschap om op scherm te tonen
            $this->_fout = "Kon uw wachtwoord niet resetten! Probeer het later opnieuw.";
            return false;
        }
        // Wachtwoord succesvol reset
        return true;
    }

    /**
     * Wijzigen van de huidige periode van een gebruiker
     * @param string $gebruikersnaam   De gebruikersnaam
     * @param string $academiejaar
     * @param string $zittijd
     * @return bool                     Gelukt/niet gelukt
     */
    public function wijzig_periode(string $gebruikersnaam, string $academiejaar, string $zittijd) : bool
    {
        $this->_fout="";
        // haal id of van de periode
        $sql = "SELECT id " .
            "FROM periode " .
            "WHERE academiejaar LIKE :aj " .
            "AND zittijd LIKE :zt";
        $parameters = [
            [':aj', $academiejaar],
            [':zt', $zittijd]
        ];
        $periode_id = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_COLUMN'), $sql, $parameters);

        if(isset($periode_id)){
            // exact 1 gebruiker gewenst want gebruikersnaam is uniek
            $sql = "UPDATE gebruiker ".
                "SET periode_id = :pid " .
                "WHERE gebruikersnaam LIKE :gn ";
            $parameters = [
                [':gn', $gebruikersnaam],
                [":pid", $periode_id]
            ];
            $aantal_rijen = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('INSERT'), $sql, $parameters);

            if($aantal_rijen !== -1){
                return true;
            }
            else {
                $this->_fout = "Kon periode niet bijwerken.";
                return false;
            }
        }
        else {
            $this->_fout = DATABANK::geef_instantie()->geef_sql_fout();
            return false;
        }
    }

    /**
     * @param $gebruikersnaam
     * @param $olod
     * @return bool
     */
    public function voeg_olod_toe(string $gebruikersnaam, string $olod) : bool{
        $sql = "INSERT INTO gebruiker_olod (gebr_gebrnaam, olod_id) " .
            "VALUES (:gn, (select id from olod where naam = :o))";
        $parameters = [
            [':gn', $gebruikersnaam],
            [":o", $olod]
        ];
        DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('INSERT'), $sql, $parameters);
        // effectief 1 rij moet toegevoegd zijn
        return DATABANK::geef_instantie()->geef_sql_aantal_rijen() === 1;
    }

    /**
     * @param $gebruikersnaam
     * @param $type
     * @param $voornaam
     * @param $familienaam
     * @param $email
     * @param $actief
     * @param array $olods
     * @return bool
     */
    public function voeg_gebruiker_toe($gebruikersnaam, $wachtwoord, $type, $voornaam, $familienaam, $email, $actief, array $olods) : bool{
        // eerst kijken of gebruiker nog niet bestaat
        $gebruiker = $this->geef_gebruiker($gebruikersnaam);
        // bestaat nog niet dus toevoegen
        if($gebruiker === null){
            $sql = "INSERT INTO gebruiker " .
                    "VALUES (:gn, :ww, :t, :vn, :fn, :a, :e, :dg)";
            $parameters = [
                [':gn', $gebruikersnaam],
                // wachtwoord hashed opslaan in de db
                [":ww", password_hash($wachtwoord, PASSWORD_DEFAULT)],
                [":t", $type],
                [":vn", $voornaam],
                [":fn", $familienaam],
                [":a", ($actief === true) ? 'j' : 'n' ],
                [":e", $email],
                [":dg", ""] // lege doelgroep
            ];
            DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('INSERT'), $sql, $parameters);
            // effectief 1 rij moet toegevoegd zijn
            if(DATABANK::geef_instantie()->geef_sql_aantal_rijen() === 1){
                // alle olods koppelen
                foreach($olods as $olod){
                    if(!$this->voeg_olod_toe($gebruikersnaam, $olod)) {
                        $this->verwijder_gebruiker($gebruikersnaam);
                        $this->_fout = "Kon gebruiker niet toevoegen aan OLOD " . $olod . "!";
                        return false;
                    }
                }
                /// TODO: email sturen naar nieuwe gebruiker met wachtwoord
                return true;
            }
            else {
                $this->_fout = "Kon gebruiker niet toevoegen!";
                return false;
            }
        }
        else {
            $this->_fout = "Gebruikter bestaat al!";
            // hem verwijderen
            $this->verwijder_gebruiker($gebruikersnaam);
            return false;
        }
    }

    /**
     * @param $gebruikersnaam
     * @return bool
     */
    public function verwijder_gebruiker($gebruikersnaam) : bool{
        // TODO : checken of hij niet aan een bevraging hangt enzo
        $this->_fout = "";
        // alle olods verwijderen
        $sql = "DELETE FROM gebruiker_olod " .
            "WHERE gebr_gebrnaam = :gn ";
        $parameters = [
            [':gn', $gebruikersnaam]
        ];
        if(DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters) <= 0) {
            $this->_fout = "Kon OLODs niet verwijderen!";
        }
        // Gebruiker verwijderen
        $sql = "DELETE FROM gebruiker " .
            "WHERE gebruikersnaam = :gn ";
        $parameters = [
            [':gn', $gebruikersnaam]
        ];
        // moet effectief 1 rij zijn
        if(DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters) !== 1) {
            $this->_fout .= "Kon OLODs niet verwijderen!";
            return false;
        }
        return true;
    }
}