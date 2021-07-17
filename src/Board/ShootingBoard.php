<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class ShootingBoard extends AbstractBoard
{
    protected array $killedShips = [];


    public function addKilledShip(int $size, int $direction, array $headCoords): void
    {
        $this->killedShips[] = new Ship($size, $direction, $headCoords);
    }


    public function getNumberOfKilledShips(): int
    {
        return count($this->killedShips);
    }
}
