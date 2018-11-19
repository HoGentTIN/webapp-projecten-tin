<?php
include_once '/srv/prjtinapp' . '/php/klasse/domein/periode.php';
include_once '/srv/prjtinapp' . '/php/klasse/domein/sessiegroep.php';
include_once '/srv/prjtinapp' . '/php/klasse/domein/vragenlijst.php';

/**
 * Class Sessie
 */
class Sessie
{
    private $_omschrijving;
    private $_periode;
    private $_vragenlijst;
    private $_sessiegroepen;

    /**
     * Sessie constructor.
     * @param string $omschrijving
     * @param OpleidingsOnderdeel[] $olods
     * @param Periode $periode
     * @param Vragenlijst $sjabloon
     * @param SessieGroep[] $sessiegroepen
     */
    public function __construct(string $omschrijving, Periode $periode, Vragenlijst $vragenlijst, array $sessiegroepen=[])
    {
        $this->_omschrijving = $omschrijving;
        $this->_periode = $periode;
        $this->_vragenlijst = $vragenlijst;
        $this->_sessiegroepen = $sessiegroepen;
    }

    /**
     * @return string
     */
    public function geef_omschrijving(): string
    {
        return $this->_omschrijving;
    }

    /**
     * @return Periode
     */
    public function geef_periode(): Periode
    {
        return $this->_periode;
    }

    /**
     * @return Vragenlijst
     */
    public function geef_vragenlijst(): Vragenlijst
    {
        return $this->_vragenlijst;
    }

    /**
     * @return SessieGroep[]
     */
    public function geef_sessiegroepen() : array
    {
        return $this->_sessiegroepen;
    }
}