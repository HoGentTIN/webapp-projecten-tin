<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/controller.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/periode_mapper.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/domein/periode.php';

/**
 * Class PeriodeController
 */
class PeriodeController extends Controller
{
    // private variabelen
    private $_pm;
    private $_periodes;

    /**
     * PeriodeController constructor.
     */
    public function __construct(){
        // ouder constructor oproepen
        parent::__construct();
        // mapper aanmaken
        $this->_pm = new PeriodeMapper();
        // periodes laden uit DB
        $this->_periodes = $this->_pm->geef_periodes();
    }

    /**
     * Overzicht van alle periodes aanwezig
     * @return Periode[]
     */
    public function geef_periodes(int $jaar=0, int $zittijd=0) : array {
        $periodes_filtered = [];
        foreach($this->_periodes as $periode){
            // 4 gevallen:
            //  1. jaar & zittijd zijn gelijk
            //  2. jaar = 0 en zittijden zijn gelijk
            //  3. jaren zijn gelijk & zittijd = 0
            //  4. jar = 0 & zittijd = 0
            if(($periode->geef_jaar() === $jaar &&
                $periode->geef_zittijd() === $zittijd) ||
                (0 === $jaar &&
                    $periode->geef_zittijd() === $zittijd) ||
                ($periode->geef_jaar() === $jaar &&
                    0 === $zittijd) ||
                (0 === $jaar &&
                    0 === $zittijd)){
                $periodes_filtered[] = $periode;
            }
        }
        return $periodes_filtered;
    }

    /**
     * Overzicht van alle periodes aanwezig
     * @return array
     */
    public function geef_periode (int $jaar, int $zittijd) : Periode {
        foreach($this->_periodes as $periode){
            if($periode->geef_jaar() === $jaar &&
                $periode->geef_zittijd() === $zittijd){
                return $periode;
            }
        }
        return null;
    }

    /**
     * Controleert of een periode bestaat
     * @param int $jaar
     * @param int $zittijd
     * @return bool
     */
    public function bestaat (int $jaar, int $zittijd) : bool{
        foreach($this->_periodes as $periode){
            if($periode->geef_jaar() === $jaar &&
                $periode->geef_zittijd() === $zittijd){
                return true;
            }
        }
        return false;
    }

    /**
     * Verwijdert een periode
     * @param int $jaar
     * @param int $zittijd
     * @return bool
     */
    public function verwijder_periode (int $jaar, int $zittijd) : bool{
        // eerste kijken of ze welbestaat
        if($this->bestaat($jaar, $zittijd)){
            // proberen verwijderen uit databank
            if($this->_pm->verwijder_periode ($jaar, $zittijd)){
                $this->_periodes = array_diff($this->_periodes, [ $this->geef_periode($jaar, $zittijd) ]);
                return true;
            }
            // databank fout
            else {
                $this->_fout = DATABANK::geef_instantie()->geef_sql_fout();
                return false;
            }
        }
        else {
            $this->_fout = "Periode " . $jaar . ", zittijd " . $zittijd . " bestaat niet";
            return false;
        }
    }

    /**
     * Voegt een periode toe
     * @param int $jaar
     * @param int $zittijd
     * @param string $van
     * @param string $tot
     * @return bool
     */
    public function toevoegen_periode (int $jaar, int $zittijd, string $van, string $tot) : bool{
        // eerst kijken of periode nog niet bestaat
        if(!$this->bestaat($jaar, $zittijd)){
            // bestaat nog niet, dus proberen periode aanmaken
            try{
                $datum_van = new DateTime($van);
                $datum_tot = new DateTime($tot);
                // nieuwe periode proberen aanmaken
                $p = new Periode($jaar, $zittijd, $datum_van, $datum_tot);
                // Proberen toevoegen aan de databank
                if($this->_pm->toevoegen_periode($jaar, $zittijd, $van, $tot)){
                    // toevoegen aan onze lijst
                    $this->_periodes[] = $p;
                    return true;
                }
                else {
                    $this->_fout = DATABANK::geef_instantie()->geef_sql_fout();
                    return false;
                }
            }
            catch (ValidatieUitzondering $vu) {
                $this->_fout = $vu->getMessage();
                return false;
            }
            catch(Exception $e) {
                $this->_fout = "Van en/of tot zijn geen geldige datums!";
                return false;
            }
        }
        else {
            $this->_fout = "Periode " . $jaar . ", zittijd " . $zittijd . " bestaat al";
            return false;
        }
    }
}


