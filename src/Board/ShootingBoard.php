<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class ShootingBoard extends AbstractBoard
{
    protected array $killedShips = [];


    public function addKilledShip(Ship $ship): void
    {
        $this->killedShips[] = $ship;
    }


    public function getNumberOfKilledShips(): int
    {
        return count($this->killedShips);
    }
}
