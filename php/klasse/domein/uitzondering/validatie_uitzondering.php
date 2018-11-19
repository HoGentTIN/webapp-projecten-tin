<?php

/**
 * Eigen klasse voor validatie uitzonderingen
 */
class ValidatieUitzondering extends Exception
{
    /**
     * ValidatieUitzondering constructor.
     * @param string $boodschap
     */
    public function __construct(string $boodschap)
    {
        parent::__construct($boodschap, 0, null);
    }
}