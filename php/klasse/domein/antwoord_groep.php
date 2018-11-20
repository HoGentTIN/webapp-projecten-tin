<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/antwoord.php';

/**
 * Class AntwoordGroep
 */
class AntwoordGroep
{
    private $_naam;
    private $_antwoorden;

    /**
     * AntwoordGroep constructor.
     * @param string $_naam
     * @param array $_antwoorden
     */
    public function __construct(string $naam, array $antwoorden=[])
    {
        $this->_naam = $naam;
        $this->_antwoorden = $antwoorden;
    }

    /**
     * @return string
     */
    public function geef_naam(): string
    {
        return $this->_naam;
    }

    /**
     * @return Antwoord[]
     */
    public function geef_antwoorden(): array
    {
        return $this->_antwoorden;
    }

    /**
     * @param int $index
     * @return Antwoord|null
     */
    public function geef_antwoord(int $index)
    {
        if($index >= 0 && count($this->_antwoorden) > $index) {
            return $this->_antwoorden[$index];
        }
        else {
            return null;
        }
    }

    /**
     * @return Antwoord|null
     */
    public function geef_eerste_antwoord()
    {
        return $this->geef_antwoord(0);
    }

    /**
     * @return Antwoord|null
     */
    public function geef_laatste_antwoord()
    {
        return $this->geef_antwoord(count($this->_antwoorden) - 1);
    }

    /**
     * @param array $antwoorden
     */
    public function zet_antwoorden(array $antwoorden=[]){
        $this->_antwoorden = $antwoorden;
    }

    /**
     * Geeft het object als string terug.
     * Deze methode is nodig om een Anwtoord met ander Antwoord te vergelijken
     */
    public function __toString()
    {
        $string = "Klasse: ". self::class .
            "<br>Naam: " .  $this->geef_naam() .
            "<br># Antwoorden: " . count($this->_antwoorden);
        return $string;
    }
}