<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


class VeryEasyAI implements InterfaceAI
{
    public function getCoordsForShooting(ShootingBoard $shootingBoard): array
    {
        do {
            $x = rand(0, ShootingBoard::WIDTH - 1);
            $y = rand(0, ShootingBoard::HEIGHT - 1);
            $attackedSquare = $shootingBoard->getSquare($x, $y);
        } while ($attackedSquare->getState() !== Square::EMPTY);

        return [$x, $y];
    }
}
