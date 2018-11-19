<?php
/**
 * Created by PhpStorm.
 * User: sebastiaan
 * Date: 01/04/18
 * Time: 11:31
 */

class Controller
{
    protected $_fout;

    protected function __construct()
    {
        $this->_fout = "";
    }

    /**
     * Geeft de fout terug van de laatste controlleractie
     * @return string
     */
    public function geef_fout() : string {
        return $this->_fout;
    }
}