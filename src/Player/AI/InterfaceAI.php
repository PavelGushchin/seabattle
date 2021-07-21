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


    /**
     * This method is called when user clicks "Start new game"
     * button, so you have to reset all current AI data
     */
    public function reset(): void;
}
