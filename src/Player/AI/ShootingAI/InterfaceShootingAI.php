<?php

namespace SeaBattle\Player\AI\ShootingAI;

use SeaBattle\Board\AbstractBoard;


interface InterfaceShootingAI
{
    /**
     * The main goal of this method is to return an array
     * which contains coordinates for next shooting
     */
    public function getCoordsForShooting(AbstractBoard $shootingBoard): array;
}
