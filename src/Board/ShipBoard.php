<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class ShipBoard extends AbstractBoard
{
    public const SHIPS_TO_CREATE = [
        ["ship size" => 4, "number" => 1],
        ["ship size" => 3, "number" => 2],
        ["ship size" => 2, "number" => 3],
        ["ship size" => 1, "number" => 4],
    ];

    protected array $ships;


    public function addShip(Ship $ship): void
    {
        $this->ships[] = $ship;

        /**
         * Marking squares of ShipBoard as "SHIP"
         */
        [$headShipX, $headShipY] = $ship->getHeadCoords();
        [$tailShipX, $tailShipY] = $ship->getTailCoords();

        for ($x = $headShipX; $x <= $tailShipX; $x++) {
            for ($y = $headShipY; $y <= $tailShipY; $y++) {
                $this->getSquare($x, $y)->setState(Square::SHIP);
                $this->getSquare($x, $y)->setShip($ship);
            }
        }
    }


    public function getShips(): array
    {
        return $this->ships;
    }


    public function getNumberOfShips(): int
    {
        return count($this->ships);
    }
}
