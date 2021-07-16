<?php

namespace SeaBattle\Board;


use SeaBattle\Ship\Ship;

class ShootingBoard extends Board
{
    protected array $killedShips = [];


    public function addKilledShip(int $size): void
    {
        $this->killedShips[] = new Ship($size);
    }


    public function getNumberOfKilledShips(): int
    {
        return count($this->killedShips);
    }
}
