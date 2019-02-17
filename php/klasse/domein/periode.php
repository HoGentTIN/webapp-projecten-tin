<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/uitzondering/validatie_uitzondering.php';
/**
 * Class Periode
 * Beschrijft een periode binnen een academijaar volgens zittijd
 */
class Periode
{
    private $_academie_jaar;
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
    public function __construct(int $jaar, int $zittijd, DateTime $van, DateTime $tot)
    {
        $this->_academie_jaar = $jaar;
        $this->_zittijd = $zittijd;
        $this->_van = $van;
        $this->_tot = $tot;

        // Kan enkel periode aanmaken tem volgend academiejaar
        $huidig_jaar = date("y"); // enkel laatste 2 cijfer
        // max academiejaar = huidige jaar (xx) + volgend jaar (YY) als xxYY
        $max_academiejaar = $huidig_jaar * 100 + ($huidig_jaar + 1);
        $min_academiejaar = 1718;

        if($jaar < $min_academiejaar || $jaar > $max_academiejaar) {
            throw new ValidatieUitzondering("Academiejaar moet liggen tussen " . $min_academiejaar . " en " . $max_academiejaar . "!");
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
    public function geef_academie_jaar() : int {
        return $this->_academie_jaar;
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
            "<br>Jaar: " .  $this->geef_academie_jaar() .
            "<br>Zittijd: " . $this->geef_zittijd() .
            "<br>Van" . $this->geef_van()->format("d/m/Y") .
            "<br>Tot" . $this->geef_tot()->format("d/m/Y");
        return $string;
    }

    /**
     * Methode die checkt of een periode gelijk is aan een andere
     * @param Periode $p
     * @return bool
     */
    public function is_gelijk_aan(Periode $p) : bool {
        return ($this->geef_academie_jaar() === $p->geef_academie_jaar() and
                    $this->geef_zittijd() === $p->geef_zittijd());
    }
}