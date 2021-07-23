<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;


interface InterfaceAI
{
    /**
     * The main goal of this method is to return an
     * array with coordinates for the next shot
     */
    public function getCoordsForShooting(ShootingBoard $shootingBoard): array;
}
