<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/gebruiker_controller.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/olod_mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/project_groep_mapper.php';

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

    /**
     * @return OpleidingsOnderdeel
     */
    public function geef_olod($olodid) : OpleidingsOnderdeel {
        $om = new OlodMapper();
        return $om->geef_olod($olodid);
    }

    /**
     * @param OpleidingsOnderdeel $olod
     * @param Periode $periode
     * @return Project_Groep[]
     */
    public function geef_projectgroepen_olod(OpleidingsOnderdeel $olod, Periode $periode) : array {
        $pgm = new ProjectGroepMapper();
        return $pgm->geef_project_groepen($olod->geef_id(), $periode);
    }

    /**
     * Geeft de olods waar de lector voor huidige periode is aan verbonden
     * @param int $jaar             Nummer van academiejaar (Default: 0 voor allemaal)
     * @param int $zittijd          Nummer van zittijd (Default: 0 voor alles)
     * @return OpleidingsOnderdeel[]
     */
    public function geef_olods(int $jaar=0, int $zittijd=0) : array {
        $om = new OlodMapper();
        return $om->geef_olods_van_lector($this->geef_gebruikersnaam(), $jaar, $zittijd);
    }
}