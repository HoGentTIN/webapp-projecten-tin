<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/gebruiker_controller.php';

/**
 * Class StudentController
 * Gebruikt basis functionaliteit voor elke gebruiker via GebruikerController
 * Extra methodes enkel voor studenten
 */
class StudentController extends GebruikerController
{
    /**
     * StudentController constructor.
     * @param string $gebruikersnaam
     */
    public function __construct(string $gebruikersnaam){
        parent::__construct($gebruikersnaam);
    }

    /**
     * @param string $gebruikersnaam
     * @param Bevraging $bevraging
     * @return bool
     */
    public function indienen_bevraging(Bevraging $bevraging) : bool{
        $bm = new BevragingMapper();
        return $bm->indienen_bevraging($bevraging);
    }

    /**
     * @return int
     */
    public function geef_aantal_open_bevragingen() : int {
        $bm = new BevragingMapper();
        return $bm->geef_aantal_bevragingen($this->geef_gebruikersnaam(), false);
    }

    /**
     * @return int
     */
    public function geef_aantal_ingevulde_bevragingen() : int {
        $bm = new BevragingMapper();
        return $bm->geef_aantal_bevragingen($this->geef_gebruikersnaam(), true);
    }

    /**
     * @return Bevraging[]
     */
    public function geef_bevragingen($voltooid=null, $doelgroep="") : array {
        $bm = new BevragingMapper();
        // bevrangen ophalen waarbij hij tester is
        $bevragingen_tester =  $bm->geef_bevragingen($this->geef_gebruikersnaam(), "%", $voltooid);
        // bevragingen ophalen waarbij hij doelgroep is
        $bevragingen_doelgroep =  $bm->geef_bevragingen("%", $doelgroep, $voltooid);
        return array_merge($bevragingen_tester, $bevragingen_doelgroep);
    }
}