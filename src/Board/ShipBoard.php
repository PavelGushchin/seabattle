<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class ShipBoard extends AbstractBoard
{
    public const SHIPS_TO_CREATE = [
        ["ship size" => 4, "amount" => 1],
        ["ship size" => 3, "amount" => 2],
        ["ship size" => 2, "amount" => 3],
        ["ship size" => 1, "amount" => 4],
    ];

    protected array $ships;


    public function addShip(Ship $ship): void
    {
        [$headX, $headY] = $ship->getHeadCoords();
        [$tailX, $tailY] = $ship->getTailCoords();

        for ($x = $headX; $x <= $tailX; $x++) {
            for ($y = $headY; $y <= $tailY; $y++) {
                $shipSquare = $this->getSquare($x, $y);

                $shipSquare->setState(Square::SHIP);
                $shipSquare->setShip($ship);
            }
        }

        $this->ships[] = $ship;
    }


    public function getNumberOfAllShips(): int
    {
        return count($this->ships);
    }
}
