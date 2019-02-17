<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/periode.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/gebruiker.php';

/**
 * Project_Groep
 */
class Project_Groep {
    /**
     * Private members
     */
    private $_id;
    private $_naam;
    private $_periode;
    private $_leden;

    /**
     * Gebruiker constructor.
     * @param string $naam
     * @param array $leden (Standaard [])
     */
    public function __construct(int $id, string $naam, Periode $periode, array $leden=[]) {
        $this->_id = $id;
        $this->_naam = $naam;
        $this->_periode = $periode;
        $this->_leden = $leden;
    }

    /**
     * Geeft de id terug
     * @return int
     */
    public function geef_id() : int {
        return $this->_id;
    }

    /**
     * Geeft de naam terug
     * @return string
     */
    public function geef_naam() : string {
        return $this->_naam;
    }

    /**
     * Voegt een gebruiker toe als lid aan de projectgroep
     * @param $nieuw_lid
     */
    public function voeg_lid_toe($nieuw_lid) {
        foreach($this->geef_leden() as $lid){
            if($lid->geef_gebruikersnaam() === $nieuw_lid->geef_gebruikersnaam()){
                // lid gevonden dus kan niet meer toevoegen
                return;
            }
        }
        // lid niet gevonden dus toevoegen
        array_push($this->_leden, $nieuw_lid);
    }

    /**
     * @return Gebruiker[]
     */
    public function geef_leden() : array {
        return $this->_leden;
    }

    /**
     * Geeft het anatal studenten terug
     * @return int
     */
    public function geef_aantal_studenten() : int {
        $aantal = 0;
        foreach($this->_leden as $lid){
            if ($lid->geef_gebruikertype() === "Student"){
                $aantal += 1;
            }
        }
        return $aantal;
    }

    /**
     * Geeft de studenten terug
     * @return Gebruiker[]
     */
    public function geef_studenten() : array {
        $studenten = [];
        foreach($this->_leden as $lid){
            if ($lid->geef_gebruikertype() === "Student"){
                $studenten[] = $lid;
            }
        }
        return $studenten;
    }

    /**
     * Geeft de begeleiders terug
     * @return Gebruiker[]
     */
    public function geef_begeleiders() : array {
        $begeleiders = [];
        foreach($this->_leden as $lid){
            if ($lid->geef_gebruikertype() === "Lector"){
                $begeleiders[] = $lid;
            }
        }
        return $begeleiders;
    }

    /**
     * Geeft de periode terug
     * @return string
     */
    public function geef_periode() : string {
        return $this->_periode;
    }

    /**
     * Geeft het object als string terug.
     */
    public function __toString() : string {
        $string = "Klasse: ". self::class .
                    "<br>Id: " .  $this->geef_id() .
                    "<br>Naam: " .  $this->geef_naam() .
                    "<br>Periode: " . $this->geef_periode() .
                    "<br>Leden: ";
        foreach($this->geef_leden() as $lid) {
            $string .= '<br>' . $lid->geef_voornaam() . ' ' . $lid->geef_familienaam() . ' ' . $lid->geef_gebruikertype();
        }
                    "<br>Studenten: ";
        foreach($this->geef_studenten() as $lid) {
            $string .= '<br>' . $lid->geef_voornaam() . ' ' . $lid->geef_familienaam();
        }
        $string .= "<br>Begeleiders: ";
        foreach($this->geef_begeleiders() as $lid) {
            $string .= '<br>' . $lid->geef_voornaam() . ' ' . $lid->geef_familienaam();
        }
        return $string;
    }

    /**
     * Methode die checkt of een projectgroep gelijk is aan een andere
     * @param Project_Groep $pg
     * @return bool
     */
    public function is_gelijk_aan(Project_Groep $pg) : bool {
        return ($this->geef_naam() === $pg->geef_naam() and
                $this->geef_periode()->is_gelijk_aan($pg->geef_periode()));
    }
}