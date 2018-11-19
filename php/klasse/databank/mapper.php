<?php
include_once '/srv/prjtinapp' . '/php/klasse/databank/databank.php';

/**
 * Class Mapper
 */
class Mapper
{
    /**
     * Protected members
     */
    protected $_fout;

    /**
     * Mapper constructor.
     */
    protected function __construct()
    {
        $this->_fout = "";
    }

    /**
     * @return string
     */
    public function geef_fout() : string {
        return $this->_fout;
    }
}