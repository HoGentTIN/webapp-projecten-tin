<?php

/**
 * Class Bevraging
 * Ingevuld sjabloon door bepaalde tester voor bepaalde doelgroep
 */
class Bevraging {
    /**
     * Private members
     */
    private $_id;
    private $_doelgroep;
    private $_vragenlijst;
    private $_deadline;
    private $_voltooid_op;
    private $_intedienen_door;
    private $_is_anoniem;
    private $_met_score;
    private $_score;

    /**
     * Bevraging constructor.
     * @param int $id
     * @param string $doelgroep
     * @param Vragenlijst $sjabloon
     * @param Gebruiker|null $intedienden_door
     * @param DateTime|null $voltooidop
     * @param bool $is_anoniem (Standaard false)
     * @param bool $met_score (Standaard true)
     * @param float $score (Standaard 0)
     */
    public function __construct(int $id, string $doelgroep, Vragenlijst $vragenlijst, Gebruiker $intedienden_door=null, DateTime $deadline, DateTime $voltooid_op=null, bool $is_anoniem=false, bool $met_score=true, float $score=0) {
        $this->_id = $id;
        $this->_doelgroep = $doelgroep;
        $this->_vragenlijst = $vragenlijst;
        $this->_deadline = $deadline;
        $this->_voltooid_op = $voltooid_op;
        $this->_intedienen_door = $intedienden_door;
        $this->_is_anoniem = $is_anoniem;
        $this->_met_score = $met_score;
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
    public function geef_doelgroep() : string {
        return $this->_doelgroep;
    }

    /**
     * @return Vragenlijst
     */
    public function geef_vragenlijst() : Vragenlijst {
        return $this->_vragenlijst;
    }

    /**
     * @return Antwoord[]
     */
    public function geef_antwoorden() : array {
        return $this->_vragenlijst->geef_antwoorden();
    }

    /**
     * @return float
     */
    public function geef_score() : float {
        return $this->_score;
    }

    /**
     * @return float
     */
    public function bereken_score() {
        $this->_score = $this->geef_vragenlijst()->geef_score();
    }

    /**
     * @return DateTime
     */
    public function geef_deadline() : DateTime {
        return $this->_deadline;
    }

    /**
     * @return DateTime|null
     */
    public function geef_voltooid_op()  {
        return $this->_voltooid_op;
    }

    /**
     * @return Gebruiker
     */
    public function geef_intedienen_door() : Gebruiker {
        return $this->_intedienen_door;
    }


    /**
     * @return bool
     */
    public function met_score() : bool {
        return $this->_met_score;
    }


    /**
     * @return bool
     */
    public function is_anoniem() : bool {
        return $this->_is_anoniem;
    }

    /**
     * Geeft het object als string terug.
     */
    public function __toString() : string {
        $string = "Klasse: ". self::class .
            "<br>id: " .  $this->geef_id().
            "<br>In te dienen door: " .  $this->geef_intedienen_door() .
            "<br>Is anoniem: " .  ($this->is_anoniem() === true) ? "Ja" : "Nee".
            "<br>Met score: " .  ($this->met_score() === true) ? "Ja" : "Nee".
            "<br>Deadline: " . $this->geef_deadline()->format('d/m/Y') .
            "<br>Voltooid op: " ;
        // nog niet voltooid
        if($this->geef_voltooid_op() === null){
            $string .= ' - ';
        }
        // voltooid
        else {
            $string .= $this->geef_voltooid_op()->format('d/m/Y');
        }
        $string .= "<br>Score: " .  $this->geef_score();
        $string .= "<br>Vragenlijst:" . $this->geef_vragenlijst();
        return $string;
    }
}