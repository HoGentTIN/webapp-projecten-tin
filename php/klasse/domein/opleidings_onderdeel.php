<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/opleiding.php';

/**
 * Class OpleidingsOnderdeel
 */
class OpleidingsOnderdeel {
    /**
     * Private members
     */
    private $_id;
    private $_naam;
    private $_opleiding;

    /**
     * OpleidingsOnderdeel constructor
     * @param int $id
     * @param string $naam
     */
    public function __construct(int $id, string $naam, Opleiding $opleiding) {
        $this->_id = $id;
        $this->_naam = $naam;
        $this->_opleiding = $opleiding;
    }

    /**
     * @return int
     */
    public function geef_id() : int {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function geef_naam(): string {
        return $this->_naam;
    }

    /**
     * @return Opleiding
     */
    public function geef_opleiding(): Opleiding {
        return $this->_opleiding;
    }

    /**
     * Geeft het object als string terug.
     * Deze methode is nodig om een Opleiding met ander Opleiding te vergelijken
     */
    public function __toString() : string {
        return "Klasse: ". self::class .
            "<br>Id: " . $this->geef_id() .
            "<br>Naam: " . $this->geef_naam() .
            "<br>Opleiding:<br> " . $this->geef_opleiding();
    }
}