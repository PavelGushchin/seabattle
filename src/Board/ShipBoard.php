<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class ShipBoard extends AbstractBoard
{
    protected array $ships = [];


    public function getShipById(int $shipId): ?Ship
    {
        if ($shipId < 0 || $shipId >= count($this->ships)) {
            return null;
        }

        return $this->ships[$shipId];
    }


    public function addShip(int $size, int $direction, array $headCoords): void
    {
        $this->ships[] = new Ship($size, $direction, $headCoords);
    }


    public function getNumberOfAllShips(): int
    {
        return count($this->ships);
    }
}
