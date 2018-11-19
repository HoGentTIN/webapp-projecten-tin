<?php
// Zet document_root map in indien nog niet gebeurd
if(empty($_SESSION['DOCUMENT_ROOT'])) {
    $globals_config = parse_ini_file(getcwd() . '/php/configuratie/.globals.ini');
    $_SERVER['DOCUMENT_ROOT']= $globals_config['DOCUMENT_ROOT'];
}
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/sessie.php';

$sessie_controller->ga_naar_startpagina();
