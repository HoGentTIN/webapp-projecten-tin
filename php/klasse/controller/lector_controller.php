<?php
include_once '/srv/prjtinapp' . '/php/klasse/controller/gebruiker_controller.php';

/**
 * Class LectorController
 * Gebruikt basis functionaliteit voor elke gebruiker via GebruikerController
 * Extra methodes enkel voor lectoren
 */
class LectorController extends GebruikerController
{
    /**
     * LectorController constructor.
     * @param string $gebruikersnaam
     */
    public function __construct(string $gebruikersnaam){
        parent::__construct($gebruikersnaam);
    }

    /**
     * @param Bevraging $bevragingid
     * @return bool
     */
    public function wissen_bevraging(int $bevragingid) : bool{
        $bm = new BevragingMapper();
        return $bm->wissen_bevraging($bevragingid);
    }

    /**
     * @return Bevraging[]
     */
    public function geef_bevragingen($huidige_tester="%", $huidige_doelgroep="%", $voltooid=null) : array {
        $bm = new BevragingMapper();
        return $bm->geef_bevragingen($huidige_tester, $huidige_doelgroep, $voltooid);
    }

    /**
     * @return int
     */
    public function geef_aantal_bevragingen($gebruikersnaam="", $voltooid=null) : int {
        $bm = new BevragingMapper();
        return $bm->geef_aantal_bevragingen($gebruikersnaam, $voltooid);
    }

    /**
     * @return string[]
     */
    public function geef_doelgroepen() : array {
        $bm = new BevragingMapper();
        return $bm->geef_doelgroepen();
    }

    /**
     * @return string[]
     */
    public function geef_testers() : array {
        $bm = new BevragingMapper();
        return $bm->geef_testers();
    }

    /**
     * @return string[]
     */
    public function geef_types_vragenlijsten($huidige_tester="%", $huidige_doelgroep="%", $voltooid=null) : array {
        $bm = new BevragingMapper();
        return $bm->geef_types_vragenlijsten($huidige_tester, $huidige_doelgroep, $voltooid);
    }
}