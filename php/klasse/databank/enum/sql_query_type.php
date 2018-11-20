<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/gedeeld/enum.php';

class SQL_QUERY_TYPE extends Enum {
    protected static $caseSensitive = true;
    protected static $capitalize = true;
    // De constante waarden
    protected static $values = [
        'UPDATE' => 0,
        'DELETE' => 1,
        'SELECT_COLUMN' => 2,
        'SELECT_ALL' => 3,
        'INSERT' => 4,
    ];
}