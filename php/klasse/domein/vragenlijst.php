<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/vraag.php';

/**
 * Class Vragenlijst
 */
class Vragenlijst {
    /**
     * Private members
     */
    private $_naam;
    private $_vragen;

    /**
     * Sjabloon constructor.
     * @param string $naam
     * @param array $vragen
     */
    public function __construct(string $naam, array $vragen=[]) {
        $this->_naam = $naam;
        $this->_vragen = $vragen;
    }

    /**
     * @return string
     */
    public function geef_naam() : string {
        return $this->_naam;
    }

    /**
     * @return Vraag[]
     */
    public function geef_vragen() : array {
        return $this->_vragen;
    }

    /**
     * @return Antwoord[]
     */
    public function geef_antwoorden() : array {
        $antwoorden =[];
        foreach($this->_vragen as $vraag) {
            $antwoorden[] = $vraag->geef_antwoord();
        }
        return $antwoorden;
    }

    /**
     * @return float
     */
    public function geef_score() : float {
        $score = 0.0;
        foreach($this->_vragen as $vraag){
            $score += $vraag->geef_score();
        }
        return $score;
    }

    /**
     * @return Vraag|null
     */
    public function geef_vraag($id) {
        foreach($this->_vragen as $vraag){
            if($vraag->geef_id() === $id){
                return $vraag;
            }
        }
        return null;
    }

    /**
     * Geeft het object als string terug.
     */
    public function __toString() : string {
        $string = "Klasse: ". self::class .
            "<br>Naam: " .  $this->geef_naam() .
            "<br>Vragen:";
        foreach($this->geef_vragen() as $vraag){
            $string .= '<br>' . $vraag;
        }
        return $string;
    }
}
