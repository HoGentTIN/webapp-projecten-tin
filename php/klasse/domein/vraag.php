<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/antwoord_groep.php';

/**
 * Class Vraag
 */
class Vraag {
    /**
     * Private members
     */
    private $_id;
    private $_tekst;
    private $_vraagtype;
    private $_antwoordgroep;
    private $_antwoord;

    /**
     * Vraag constructor.
     * @param int $_id
     * @param string $_tekst
     * @param string $_vraagtype
     * @param AntwoordGroep $_antwoordgroep
     * @param Antwoord $antwoord
     */
    public function __construct(int $_id, string $_tekst, string $_vraagtype, AntwoordGroep $_antwoordgroep=null, Antwoord $antwoord=null) {
        $this->_id = $_id;
        $this->_tekst = $_tekst;
        $this->_vraagtype = $_vraagtype;
        $this->_antwoordgroep = $_antwoordgroep;
        $this->_antwoord = $antwoord;
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
     * @return string
     */
    public function geef_vraagtype() : string {
        return $this->_vraagtype;
    }

    /**
     * @return AntwoordGroep|null
     */
    public function geef_antwoordgroep() {
        return $this->_antwoordgroep;
    }

    /**
     * @param AntwoordGroep $antwoordgroep
     */
    public function zet_antwoordgroep(AntwoordGroep $antwoordgroep) {
        $this->_antwoordgroep = $antwoordgroep;
    }

    /**
     * @return float|null
     */
    public function geef_score() : float {
        // geen antwoord dus lege score
        if($this->geef_antwoord() === null){
            return 0.0;
        }
        else {
            return $this->geef_antwoord()->geef_score();
        }
    }

    /**
     * @return Antwoord|null
     */
    public function geef_antwoord()  {
        return $this->_antwoord;
    }

    /**
     * @param Antwoord $antwoord
     */
    public function zet_antwoord(Antwoord $antwoord) {
        $this->_antwoord = $antwoord;
    }

    /**
     * @param int $id
     */
    public function zet_antwoord_via_id(int $id) {
        // in de antwoordengroep zoeken naar antwoord met dit id
        foreach($this->geef_antwoordgroep()->geef_antwoorden() as $antwoord) {
            // antwoord gevonden in de lijst dus inzetten
            if($antwoord->geef_id() === $id){
                $this->_antwoord = $antwoord;
                return;
            }
        }
        // geen antwoord gevonden dus leegmaken
        $this->_antwoord = null;
    }

    /**
     * Geeft het object als string terug.
     */
    public function __toString() : string {
        $string = "Klasse: ". self::class .
            "<br>Id: " .  $this->geef_id().
            "<br>Tekst: " .  $this->geef_tekst().
            "<br>Vraagtype: " .  $this->geef_vraagtype();
        if($this->geef_antwoordgroep() !== null){
            $string .= "<br>Antwoordgroep:<br>" .  $this->geef_antwoordgroep();
        }
        if($this->geef_antwoord() !== null){
            $string .= "<br>Gekozen Antwoord:<br>" .  $this->geef_antwoord();
        }
        return $string;
    }

}