<?php
include_once '/srv/prjtinapp' . '/php/klasse/domein/uitzondering/validatie_uitzondering.php';
/**
 * Class Periode
 * Beschrijft een periode binnen een academijaar volgens zittijd
 */
class Periode
{
    private $_jaar;
    private $_zittijd;
    private $_van;
    private $_tot;

    /**
     * Periode constructor.
     * @param int $jaar
     * @param int $zittijd
     * @param DateTime $van
     * @param DateTime $tot
     * @throws ValidatieUitzondering
     */
    public function __construct(int $jaar, int $zittijd, DateTime $van,DateTime $tot)
    {
        $this->_jaar = $jaar;
        $this->_zittijd = $zittijd;
        $this->_van = $van;
        $this->_tot = $tot;

        // Kan enkel periode aanmaken tem volgend academiejaar
        $huidig_jaar = date("Y");
        if($jaar < 2017 || $jaar > $huidig_jaar + 1) {
            throw new ValidatieUitzondering("Jaar ($jaar) moet liggen tussen 2017 en " . ($huidig_jaar + 1) + "!");
        }
        // Tot moet na van komen
        if($van >= $tot) {
            throw new ValidatieUitzondering("Datum van (" . $van->format('d/m/Y') . ") moet voor datum tot (" . $tot->format('d/m/Y') .") liggen!");
        }

        // Zittijd moet 1, 2, 3 zijn
        if($zittijd < 0 || $zittijd > 3) {
            throw new ValidatieUitzondering("Zittijd  ($zittijd) moet 1, 2 of 3 zijn!");
        }
    }

    /**
     * Geeft het jaartal terug van de periode
     * @return int
     */
    public function geef_jaar() : int {
        return $this->_jaar;
    }

    /**
     * Geeft de zittijd terug van de periode
     * @return int
     */
    public function geef_zittijd() : int {
        return $this->_zittijd;
    }

    /**
     * Geeft de startdatum van de periode
     * @return DateTime
     */
    public function geef_van() : DateTime {
        return $this->_van;
    }

    /**
     * Geeft de einddatum van de periode
     * @return DateTime
     */
    public function geef_tot() : DateTime {
        return $this->_tot;
    }

    /**
     * Geeft het object als string terug.
     */
    public function __toString() : string {
        $string = "Klasse: ". self::class .
            "<br>Jaar: " .  $this->geef_jaar() .
            "<br>Zittijd: " . $this->geef_zittijd() .
            "<br>Van" . $this->geef_van()->format("d/m/Y") .
            "<br>Tot" . $this->geef_tot()->format("d/m/Y");
        return $string;
    }
}