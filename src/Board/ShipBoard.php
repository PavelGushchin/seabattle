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


    public function addShip(int $size, int $direction, array $startCoords, array $endCoords): void
    {
        $newShip = new Ship($size, $direction, $startCoords);

        [$startX, $startY] = $startCoords;
        [$endX, $endY] = $endCoords;

        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {
                $this->getSquare($x, $y)->setState(Square::SHIP);
                $this->getSquare($x, $y)->setShip($newShip);
            }
        }

        $this->ships[] = $newShip;
    }


    public function getNumberOfAllShips(): int
    {
        return count($this->ships);
    }
}
