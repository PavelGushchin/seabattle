<?php

namespace SeaBattle\Player\AI\ShootingAI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


class VeryEasyAI implements InterfaceShootingAI
{
    public function getCoordsForShooting(ShootingBoard $shootingBoard): array
    {
        do {
            $x = rand(0, ShootingBoard::WIDTH - 1);
            $y = rand(0, ShootingBoard::HEIGT - 1);
            $attackedSquare = $shootingBoard->getSquare($x, $y);
        } while ($attackedSquare->getState() !== Square::EMPTY);

        return [$x, $y];
    }


    /**
     * Returns name of the algorithm
     */
    public function __toString(): string
    {
        return "Random algorithm";
    }
}
