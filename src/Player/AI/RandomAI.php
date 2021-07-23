<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


class RandomAI implements InterfaceAI
{
    /**
     * The main goal of this method is to return an
     * array with coordinates for the next shot
     */
    public function getCoordsForShooting(ShootingBoard $shootingBoard): array
    {
        do {
            $x = rand(0, ShootingBoard::WIDTH - 1);
            $y = rand(0, ShootingBoard::HEIGHT - 1);

            $attackedSquare = $shootingBoard->getSquare($x, $y);
            $isAttackedSquareEmpty = $attackedSquare->getState() === Square::EMPTY;
        } while (! $isAttackedSquareEmpty);

        return [$x, $y];
    }
}
