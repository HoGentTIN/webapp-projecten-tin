<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/controller/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/databank/gebruiker_mapper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/databank/bevraging_mapper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/enum/gebruiker_type.php';

/**
 * Class GebruikerController
 */
class GebruikerController extends Controller
{
    /**
     * Private members
     */
    private $_gebruikersnaam;
    private $_gebruiker_mapper;

    /**
     * GebruikerController constructor.
     * @param string $gebruikersnaam
     */
    public function __construct(string $gebruikersnaam){
        parent::__construct();
        $this->_gebruikersnaam = $gebruikersnaam;
        $this->_gebruiker_mapper = new GebruikerMapper();
    }

    /**
     * @return string
     */
    public function geef_gebruikersnaam() : string {
        return $this->_gebruikersnaam;
    }

    /**
     * Meld een gebruiker aan
     * @param string $gebruikersnaam
     * @param string $wachtwoord
     * @return Gebruiker|null
     */
    public function geef_gebruiker(string $gebruikersnaam, string $wachtwoord) {
        $gebruiker = $this->_gebruiker_mapper->geef_gebruiker($gebruikersnaam, $wachtwoord);
        if(isset($gebruiker)) {
            return $gebruiker;
        }
        else {
            return null;
        }
    }

    /**
     * Reset een wachtwoord van een gebruiker
     * @param string $oud_wachtwoord   Het huidige wachtwoord
     * @param string $nieuw_wachtwoord Het nieuwe wachtwoord
     * @return bool             Gelukt/niet gelukt
     */
    public function wijzig_wachtwoord(string $oud_wachtwoord, string $nieuw_wachtwoord)
    {
        // wachtwoord resetten in DB
        if(!$this->_gebruiker_mapper->wijzig_wachtwoord($this->geef_gebruikersnaam(), $oud_wachtwoord, $nieuw_wachtwoord)){
            $this->_fout = $this->_gebruiker_mapper->geef_fout();
            return false;
        }
        return true;
    }

    /**
     * Reset een wachtwoord van de gebruiker
     * @param string $gebruikersnaam   De gebruikersnaam
     * @return bool             Gelukt/niet gelukt
     */
    public function reset_wachtwoord()
    {
        // nieuw wachtwoord genereren
        $nieuw_wachtwoord = $this->genereer_wachtwoord();
        // wachtwoord resetten in DB
        if(!$this->_gebruiker_mapper->reset_wachtwoord($this->geef_gebruikersnaam(), $nieuw_wachtwoord)){
            $this->_fout = $this->_gebruiker_mapper->geef_fout();
            return false;
        }
        return true;
    }

    /**
     * Genereert een random nieuw wachtwoord
     * @return string
     */
    protected function genereer_wachtwoord () : string {
        // beschikbare tekens
        $alfabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        // nieuwe wachtwoord;
        $wachtwoord = "";
        $alfabet_lengte = strlen($alfabet) - 1;
        // lengte nieuw wachtwoord
        $AANTAL_TEKENS = 20;
        // voeg teken per teken random toe
        for ($i = 0; $i < $AANTAL_TEKENS; $i++) {
            // willekeurig teken nemen
            $n = rand(0, $alfabet_lengte);
            // teken toevoegen aan wachtwoord
            $wachtwoord .= $alfabet[$n];
        }
        // geef tabel terug als string
        return $wachtwoord;
    }

    /**
     * @param int $id
     * @return Bevraging|null
     */
    public function geef_bevraging(int $id){
        $bm = new BevragingMapper();
        return $bm->geef_bevraging($id);
    }

    /**
     * Controleert of de student toegang heeft tot de bevraging.
     * @param int $id
     * @param bool $als_lezer
     * @return bool
     */
    public function controleer_toegang_bevraging(int $id, bool $als_lezer) : bool {
        $bm = new BevragingMapper();
        return $bm->controleer_toegang($this->geef_gebruikersnaam(), $id, $als_lezer);
    }

    /**
     * Genereren html code om een bevraging in pdf formaat te kunnen downloaden
     * @param Bevraging $bevraging  De bevraging
     */
    public function download_bevraging_pdf(Bevraging $bevraging, string $htmlbevraging){
        // include voor pdf te kunnen genereren
        include_once $_SERVER['DOCUMENT_ROOT'] . '/php/pagina/gedeeld/pdf.php';
        // TODO: groep linken voor overzicht sus score
        $html_code = '<H2>Bevraging SUS score</H2>';
        if($bevraging->met_score()) {
            $html_code .= '<H4>SUS Score: ' .  $bevraging->geef_score() . '</H4>';
        }
        $html_code .= '<H4>Doelgroep: ' .  $bevraging->geef_doelgroep() . '</H4>';
        $html_code .= '<H4>Ingediend op: ' .  $bevraging->geef_voltooid_op()->format('d/m/Y') . '</H4>';
        if(!$bevraging->is_anoniem()) {
            $html_code .= '<H4>Ingediend door: ' .  $bevraging->geef_intedienen_door()->geef_voornaam() .  ' ' . $bevraging->geef_intedienen_door()->geef_familienaam() . '</H4>';
        }
        $html_code .= '<br>';
        $html_code .= $htmlbevraging;
        $pdf_naam = str_replace(' ', '_', $bevraging->geef_vragenlijst()->geef_naam()) . '_' . $bevraging->geef_id() . ".pdf";

        download_pdf($html_code, $pdf_naam);
    }
}