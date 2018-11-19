<?php

/**
 * Class Opleiding
 */
class Opleiding {
    /**
     * Private members
     */
    private $_id;
    private $_naam;

    /**
     * Opleiding constructor
     * @param int $id
     * @param string $naam
     */
    public function __construct(int $id, string $naam) {
        $this->_id = $id;
        $this->_naam = $naam;
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
    public function geef_naam() : string {
        return $this->_naam;
    }

    /**
     * Geeft het object als string terug.
     * Deze methode is nodig om een Opleiding met ander Opleiding te vergelijken
     */
    public function __toString() : string {
        return "Klasse: ". self::class .
            "<br>Id: " .  $this->geef_id() .
            "<br>Naam: " . $this->geef_naam();
    }
}