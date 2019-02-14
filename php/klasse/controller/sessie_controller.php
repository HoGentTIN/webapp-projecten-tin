<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/databank/databank.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/controller.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/gebruiker_controller.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/periode_controller.php';

class SessieController extends Controller
{
    /**
     * SessieController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    /**
     * Meld een gebruiker aan
     * @param string $gebruikersnaam
     * @param string $wachtwoord
     * @return bool
     */
    public function meld_aan($gebruikersnaam, $wachtwoord){
        $gc = new GebruikerController($gebruikersnaam);
        $gebruiker = $gc->geef_gebruiker($gebruikersnaam, $wachtwoord);
        // kijken of we exact 1 gebruiker hadden en zijn wachtwoord klopt
        if (isset($gebruiker)) {
            // Kijken of de gebruiker actief is of niet
            if ($gebruiker->is_actief()){
                $_SESSION['gebruiker'] = $gebruiker;
                return true;
            }
            else {
                $this->_fout = "Deze gebruiker is inactief!";
                return false;
            }


        }
        else {
            $this->_fout = "Ongeldige gebruikersnaam/wachtwoord!";
            return false;
        }
    }

    /**
     * Meld de huidige gebruiker af
     * @return bool
     */
    public function meld_af() {
        unset($_SESSION['gebruiker']);
        session_destroy();
        $this->_fout = "";
        return true;
    }

    /**
     * Geeft terug of er een gebruiker is aangemeld of niet
     * @return bool
     */
    public function is_aangemeld(){
        return isset($_SESSION['gebruiker']);
    }

    /**
     * Geeft de aangemelde gebruiker terug
     * @return Gebruiker
     */
    public function geef_aangemelde_gebruiker(){
        return $_SESSION['gebruiker'];
    }

    /**
     * Ga naar de startpagina
     */
    public function ga_naar_startpagina($boodschap=""){
        // standaard naar index pagina
        $pagina_url = "/index.php";
        // gebruiker is aangemeld, sturen naar zijn startpagina op basis van zijn type
        if($this->is_aangemeld()) {
            $gebruiker_type = strtolower($this->geef_aangemelde_gebruiker()->geef_gebruikertype());
            $pagina_url = "/php/pagina/" . $gebruiker_type . "/" . $gebruiker_type . ".php" . $boodschap;
        }
        $this->ga_naar_pagina($pagina_url);
    }

    /**
     * Controleert of huidige gebruikertype toegang heeft tot de pagina.
     * Indien niet, doorverbinden naar zijn startpagina
     * @param string $gebruiker_type    <p>Het gewenste gebruikerstype</p>
     */
    public function controleer_toegang_pagina($gebruiker_type) {
        // Kijken of hij aangemeld is
        if($this->is_aangemeld()){
            // Als aangemelde gebruiker zijn type niet overeenkomt met gewenste, naar startpagina
            if(strcasecmp($this->geef_aangemelde_gebruiker()->geef_gebruikertype(), $gebruiker_type)) {
                $this->ga_naar_startpagina();
            }
        }
        // Niet aangemeld dus naar aanmeldpagina
        else {
            $this->ga_naar_startpagina();
        }
    }


    /**
     * Ga naar de startpagina
     */
    public function ga_naar_pagina($pagina_url){
        header("location:" . $_SERVER['SRV_ALIAS'] . $pagina_url);
    }
}