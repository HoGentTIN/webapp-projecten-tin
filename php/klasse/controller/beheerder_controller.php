<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/controller/gebruiker_controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/controller/periode_controller.php';

class BeheerderController extends GebruikerController
{
    /**
     * BeheerderController constructor.
     * @param Gebruiker $beheerder
     */
    public function __construct(string $gebruikersnaam)
    {
        parent::__construct($gebruikersnaam);
    }

    /**
     * Verwijdert een periode
     * @param int $jaar
     * @param int $zittijd
     * @return bool
     */
    public function verwijder_periode (int $jaar, int $zittijd) : bool{
        $pc = new PeriodeController();
        $gelukt = $pc->verwijder_periode ($jaar, $zittijd);
        if(!$gelukt) {
            $this->_fout = $pc->geef_fout();
        }
        return $gelukt;
    }

    /**
     * Toevoegen van een periode
     * @param int $jaar
     * @param int $zittijd
     * @param string $van
     * @param string $tot
     * @return bool
     */
    public function toevoegen_periode (int $jaar, int $zittijd, string $van, string $tot) : bool{
        $pc = new PeriodeController();
        $gelukt = $pc->toevoegen_periode ($jaar, $zittijd, $van, $tot);
        if(!$gelukt) {
            $this->_fout = $pc->geef_fout();
        }
        return $gelukt;
    }

    /**
     * Geef lijst van periodes die voldoen aan jaartal en zittijd
     * @param int $jaar
     * @param int $zittijd
     * @return Periode[]
     */
    public function geef_periodes(int $jaar=0, int $zittijd=0) : array {
        $pc = new PeriodeController();
        return $pc->geef_periodes($jaar, $zittijd);
    }

    /**
     * @param string $opleiding
     * @return Gebruiker[]
     */
    public function geef_gebruikers($opleiding="%", $opleidingsonderdeel="%") : array {
        $gm = new GebruikerMapper();
        return $gm->geef_gebruikers($opleiding, $opleidingsonderdeel);
    }

    /**
     * @param $gebruikersnaam
     * @param $type
     * @param $voornaam
     * @param $familienaam
     * @param $email
     * @param $actief
     * @param array $olods
     * @return bool
     */
    public function voeg_gebruiker_toe($gebruikersnaam, $type, $voornaam, $familienaam, $email, $actief, array $olods) : bool{
        $wachtwoord = $this->genereer_wachtwoord();
        $gm = new GebruikerMapper();
        $gelukt = $gm->voeg_gebruiker_toe($gebruikersnaam, $wachtwoord, $type, $voornaam, $familienaam, $email, $actief, $olods);
        if(!$gelukt) {
            $this->_fout = $gm->geef_fout();
        }
        return $gelukt;
    }
}