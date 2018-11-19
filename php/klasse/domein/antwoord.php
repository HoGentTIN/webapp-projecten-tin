<?php

/**
 * Class Antwoord
 */
class Antwoord {
    /**
     * Private members
     */
    private $_id;
    private $_tekst;
    private $_score;

    /**
     * Antwoord constructor.
     * @param int $_id
     * @param string $_tekst
     */
    public function __construct(int $_id, string $_tekst, float $score=0.0)
    {
        $this->_id = $_id;
        $this->_tekst = $_tekst;
        $this->_score = $score;
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
    public function geef_tekst() : string {
        return $this->_tekst;
    }

    /**
     * @return float
     */
    public function geef_score() : float {
        return $this->_score;
    }

    /**
     * @return string
     */
    public function zet_score(float $score) {
        $this->_score = $score;
    }

    /**
     * Geeft het object als string terug.
     * Deze methode is nodig om een Anwtoord met ander Antwoord te vergelijken
     */
    public function __toString() : string {
        return "Klasse: ". self::class .
            "<br>Id: " .  $this->geef_id() .
            "<br>Tekst: " . $this->geef_tekst().
            "<br>Score: " . $this->geef_score();
    }
}