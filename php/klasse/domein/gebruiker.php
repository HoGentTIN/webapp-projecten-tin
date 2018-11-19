<?php
include_once '/srv/prjtinapp' . '/php/klasse/databank/mapper.php';

/**
 * Gebruiker
 */
class Gebruiker {
    /**
     * Private members
     */
    private $_gebruikersnaam;
    private $_olods;
    private $_is_actief;
    private $_type;
    private $_voornaam;
    private $_familienaam;
    private $_email;
    private $_doelgroep;

    /**
     * Gebruiker constructor.
     * @param string $gebruikersnaam
     * @param string $type (Standaard null)
     * @param string $voornaam
     * @param string $familienaam
     * @param string $email (Standaard null)
     * @param bool $is_actief (Standaard false)
     * @param array $olods (Standaard [])
     */
    public function __construct(string $gebruikersnaam, string $type=null, string $voornaam, string $familienaam, string $email=null, bool $is_actief=false, string $doelgroep=null, array $olods=[]) {
        $this->_gebruikersnaam = $gebruikersnaam;
        $this->_type = $type;
        $this->_voornaam = $voornaam;
        $this->_familienaam = $familienaam;
        $this->_email = $email;
        $this->_is_actief = $is_actief;
        $this->_doelgroep = $doelgroep;
        $this->_olods = $olods;
    }

    /**
     * Geeft de gebruikersnaam terug
     * @return string
     */
    public function geef_gebruikersnaam() : string {
        return $this->_gebruikersnaam;
    }

    /**
     * Geeft het gebruikertype terug
     * @return string
     */
    public function geef_gebruikertype() : string {
        return $this->_type;
    }

    /**
     * Geeft de voornaam terug
     * @return string
     */
    public function geef_voornaam() : string {
        return $this->_voornaam;
    }

    /**
     * Geeft de familienaam terug
     * @return string
     */
    public function geef_familienaam() : string {
        return $this->_familienaam;
    }

    /**
     * Geeft het emailadres terug
     * @return string
     */
    public function geef_email() : string {
        return $this->_email;
    }

    /**
     * Geeft de doelgroep terug
     * @return string
     */
    public function geef_doelgroep() : string {
        return $this->_doelgroep;
    }

    /**
     * Geeft de OpleidingsOnderdelen terug van de gebruiker
     * @return OpleidingsOnderdeel[]
     */
    public function geef_opleidingsonderdelen() : array {
        return $this->_olods;
    }

    /**
     * Reset het wachtwoord van de gebruiker
     * @param $nieuw_wachtwoord
     */
    public function reset_wachtwoord($nieuw_wachtwoord) {
        $gm = new GebruikerMapper();
        $gm->reset_wachtwoord($this->geef_gebruikersnaam(), $nieuw_wachtwoord);
    }

    /**
     * Gebruiker is actief of niet
     * @return bool
     */
    public function is_actief() : bool {
        return $this->_is_actief;
    }

    /**
     * Geeft het object als string terug.
     */
    public function __toString() : string {
        $string = "Klasse: ". self::class .
            "<br>Naam: " .  $this->geef_voornaam() . ' ' . $this->geef_familienaam() .
            "<br>Type: " . $this->geef_gebruikertype() .
            "<br>Doelgroep: " . $this->geef_doelgroep() .
            "<br>E-mail: " . $this->geef_email() .
            "<br>Is actief: " .  $this->is_actief();
        return $string;
    }
}