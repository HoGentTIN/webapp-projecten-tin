<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/sessie_controller.php';

$sessie_controller = new SessieController();

// kijken of we niet op dashboard/start knop hebben geduwd, anders terug naar zijn dashboard/start
if (isset($_POST['btn-start'])){
    $sessie_controller->ga_naar_startpagina();
}