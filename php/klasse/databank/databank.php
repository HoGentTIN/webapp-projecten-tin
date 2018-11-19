<?php
include_once '/srv/prjtinapp' . . '/php/klasse/databank/enum/sql_query_type.php';

/**
 * Class DATABANK
 */
class DATABANK
{
    // Instantie bijhouden in static variabele
    private static $_instantie = null;
    // de aangemaakte verbinding naar de databank
    private $_verbinding;
    // SQL fout indien query niet kan uitgevoerd worden
    private $_sql_fout;
    // Bevat het aantal rijen die laatste query bewerkt heeft
    private $_sql_aantal_rijen;
    // houdt de laatste uitgevoerde query bij (DEBUG)
    private $_sql_query;

    /**
     * Databank constructor.
     */
    private function __construct() { }

    /**
     * Geeft instantie van de klasse terug, indien leeg wordt er instantie aangemaakt
     * @return DATABANK
     */
    public static function geef_instantie() : DATABANK {
        if(!self::$_instantie) {
            self::$_instantie = new DATABANK();
        }
        return self::$_instantie;
    }

    /**
     * Geeft de foutboodschap terug voor de laatst uitgevoerde SQL query
     * @return string    De foutboodschap
     */
    public function geef_sql_fout() : string {
        if(!isset($this->_sql_fout)){
            $this->_sql_fout = "";
        }
        return $this->_sql_fout;
    }

    /**
     * Geef het aantal rijen terug voor de laatste SQL query
     * @return int      Het aantal rijen (-1 indien SQL query faalde)
     */
    public function geef_sql_aantal_rijen() : int{
        return $this->_sql_aantal_rijen;
    }

    /**
     * Geef de laatst uitgevoerde query terug
     * @return string      De query
     */
    public function geef_sql_query() : string {
        return $this->_sql_query;
    }

    /**
     * Maakt een verbinding met de databank
     */
    private function maak_verbinding() {
        // Constante waarden voor de databank
        $databank_config = parse_ini_file( '/srv/prjtinapp' . . '/php/configuratie/.databank.ini');
        $HOST = $databank_config['HOST'];
        $POORT = $databank_config['POORT'];
        $DATABANK = $databank_config['DATABANK'];
        $MYSQL = "mysql:host=$HOST:$POORT;dbname=$DATABANK";
        $GEBRUIKERSNAAM = $databank_config['GEBRUIKERSNAAM'];
        $WACHTWOORD = $databank_config['WACHTWOORD'];

        // Proberen om een connectie met de databank aan te maken
        try {
            $this->_verbinding = new PDO($MYSQL, $GEBRUIKERSNAAM, $WACHTWOORD);
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * Kijken of we een verbinding hebben met de databank
     */
    private function heeft_verbinding() : bool {
        return $this->_verbinding === null;
    }

    /**
     * Sluit de verbinding met de databank
     */
    private function sluit_verbinding() {
        $this->_verbinding = null;
    }

    /**
     * Voer een SQL query uit
     * @param $sql_query_type           Het type van de query (databank.enum.sql_query_type)
     * @param string $sql_query                De query die uitgevoerd moet worden
     * @param array $sql_parameters     Tabel met paramters voor de query (Standaard=[])
     * @return array|mixed|null         De resutlaten voo de query
     */
    public function voer_query_uit($sql_query_type, string $sql_query, array $sql_parameters=[]){
        try {
            $this->maak_verbinding();
            // Voorbereiden uitvoeren query
            $stmt = $this->_verbinding->prepare($sql_query);
            // Voeg parameters toe aan query indien meegegeven
            foreach($sql_parameters as $sql_parameter) {
                $stmt->bindparam($sql_parameter[0], $sql_parameter[1]);
            }
            // Voer query uit
            $stmt->execute();
            // query b ijhouden
            $this->_sql_query = $stmt->queryString;
            if (count($sql_parameters) > 0) {
                $this->_sql_query .= '<br>Parameters:<br>';
                foreach($sql_parameters as $sql_parameter) {
                    $this->_sql_query .= $sql_parameter[0] . ' => ' . $sql_parameter[1];
                }
            }
            // Aantal rijen bijhouden
            $this->_sql_aantal_rijen = $stmt->rowCount();
            // Afhankelijk van het type query geven we een ander resultaat terug
            $resultaat = null;
            switch($sql_query_type){
                case SQL_QUERY_TYPE::value('SELECT_COLUMN'): $resultaat = $stmt->fetchColumn(); break;
                case SQL_QUERY_TYPE::value('SELECT_ALL'): $resultaat = $stmt->fetchAll(); break;
                case SQL_QUERY_TYPE::value('DELETE'): $resultaat = $stmt->rowCount(); break;
                case SQL_QUERY_TYPE::value('UPDATE'): $resultaat = $stmt->rowCount(); break;
                case SQL_QUERY_TYPE::value('INSERT'): $resultaat = $stmt->rowCount(); break;
                default:
                    $this->_sql_fout = "Ongeldig type SQL query";
                    $this->_sql_aantal_rijen = -1;
                    break;
            }
            $this->sluit_verbinding();

            //$GLOBALS['SQL'] += 1;

            return $resultaat;
        }
        catch(PDOException $e)
        {
            $this->_sql_aantal_rijen = -1;
            $this->_sql_fout = "Technisch probleem. Probeer het later opnieuw!";
            return null;
        }
    }
}
