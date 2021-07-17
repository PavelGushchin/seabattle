<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class MainBoard extends AbstractBoard
{
    protected array $ships = [];


    public function getShip(int $shipId): ?Ship
    {
        if ($shipId < 0 || $shipId >= count($this->ships)) {
            return null;
        }

        return $this->ships[$shipId];
    }


    public function addShip(int $size): void
    {
        $this->ships[] = new Ship($size);
    }


    public function getNumberOfAllShips(): int
    {
        return count($this->ships);
    }
}
