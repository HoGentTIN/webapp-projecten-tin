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
    private $_projectgroepen;

    /**
     * OpleidingsOnderdeel constructor
     * @param int $id
     * @param string $naam
     */
    public function __construct(int $id, string $naam, Opleiding $opleiding, $project_groepen=[]) {
        $this->_id = $id;
        $this->_naam = $naam;
        $this->_opleiding = $opleiding;
        $this->_projectgroepen = $project_groepen;
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
     * @return Project_Groep[]
     */
    public function geef_project_groepen() : array {
        return $this->_projectgroepen;
    }

    /**
     * Voeg projectgroep toe aan olod
     * @param Project_Groep $pg
     * @throws ValidatieUitzondering
     */
    public function voeg_project_groep_toe(Project_Groep $pg) {
        // Enkel toevoegen indien nog geen groep met die naam voor die periode gekoppeld is
        foreach ($this->_projectgroepen as $project_groep){
            if ($project_groep->is_gelijk_aan($pg)) {
                throw new ValidatieUitzondering("Project groep bestaat reeds voor dit opleidingsonderdeel!");
            }
        }
        // uniek dus toevoegen
        array_push($this->_projectgroepen, $pg);
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