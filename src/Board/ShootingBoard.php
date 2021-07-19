<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class ShootingBoard extends AbstractBoard
{
    protected array $hitShips = [];
    protected array $killedShips = [];
    protected int $numberOfKilledShips = 0;


//    public function addKilledShip(int $size, int $direction, array $headCoords): void
//    {
//        $this->killedShips[] = new Ship($size, $direction, $headCoords);
//    }

    public function addKilledShip(): void
    {
        $this->numberOfKilledShips++;
    }

    public function getNumberOfKilledShips(): int
    {
        return $this->numberOfKilledShips;
//        return count($this->killedShips);
    }
}
