<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/gedeeld/enum.php';

class GEBRUIKER_TYPE extends Enum {
    protected static $caseSensitive = false;
    protected static $capitalize = true;
    // De constante waarden voor gebruikerstype
    protected static $values = [
        'LECTOR' => 'lector',
        'STUDENT' => 'student',
        'BEHEERDER' => 'beheerder',
        'GROEP' => 'groep',
        'EXTERN' => 'extern'
    ];
}