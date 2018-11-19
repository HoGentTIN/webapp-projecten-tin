<?php
/**
 * Created by PhpStorm.
 * User: sebastiaan
 * Date: 19/04/18
 * Time: 22:52
 */

class SessieGroep
{
    private $_id;
    private $_deelnemers;

    /**
     * SessieGroep constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->_id = $id;
    }

    /**
     * @return int
     */
    public function geef_id(): int
    {
        return $this->_id;
    }

    /**
     * Geeft het object als string terug.
     */
    public function __toString() : string {
        $string = "Klasse: ". self::class .
            "<br>Id: " .  $this->geef_id();
        return $string;
    }
}