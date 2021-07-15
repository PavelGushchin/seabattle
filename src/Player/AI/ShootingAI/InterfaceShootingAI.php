<?php

namespace SeaBattle\Player\AI\ShootingAI;

use SeaBattle\Board\Board;


interface InterfaceShootingAI
{
    /**
     * The main goal of this method is to return an array
     * which contains coordinates for next shooting
     */
    public function getCoordsForShooting(Board $shootingBoard): array;
}
