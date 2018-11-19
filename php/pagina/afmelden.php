<?php
    include_once '/srv/prjtinapp' . '/php/klasse/controller/sessie_controller.php';
    // Gebruiker afmelden
    $sc = new SessieController();
    $sc->meld_af();
    // Terugsturen naar de aanmeldpagina
    $sc->ga_naar_startpagina();
?>