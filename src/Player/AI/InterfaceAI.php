<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;


interface InterfaceAI
{
    /**
     * The main goal of this method is to return an array
     * which contains coordinates for next shooting
     */
    public function getCoordsForShooting(ShootingBoard $shootingBoard): array;
}
