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
            $square = $shootingBoard->getSquare($x, $y);
            $isSquareEmpty = $square->getState() === Square::EMPTY;
        } while (! $isSquareEmpty);

        return [$x, $y];
    }
}
