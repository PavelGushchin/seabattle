<?php

namespace SeaBattle\Player\AI\PlacingShipsAI;

use SeaBattle\Board\AbstractBoard;
use SeaBattle\Board\ShipBoard;
use SeaBattle\Board\Square;
use SeaBattle\Ship\Ship;


class RandomAI implements InterfacePlacingShipsAI
{
    public function createShips(AbstractBoard $board): void
    {
        foreach ($this->shipsToCreate as $ship) {
            $shipSize = $ship["ship size"];
            $amount = $ship["amount"];

            for ($i = 0; $i < $amount; $i++) {
                $this->createShip($shipSize);
            }
        }
    }


    public function createShip(int $shipSize): void
    {
        do {
            $direction = rand(Ship::HORIZONTAL, Ship::VERTICAL);
            $startCoords = [
                rand(0, ShipBoard::WIDTH - 1),
                rand(0, ShipBoard::HEIGHT - 1)
            ];

            $endCoords = Ship::calculateEndCoords($shipSize, $direction, $startCoords);

            $canShipBeCreated =
                $this->ifShipDoesNotGoOffBoard($endCoords) &&
                $this->ifAreaAroundShipIsEmpty($startCoords, $endCoords);

        } while (! $canShipBeCreated);

        $this->shipBoard->addShip($shipSize, $direction, $startCoords);
    }


    protected function ifShipDoesNotGoOffBoard($endCoords): bool
    {
        [$shipEndX, $shipEndY] = $endCoords;

        if ($shipEndX < ShipBoard::WIDTH && $shipEndY < ShipBoard::HEIGHT) {
            return true;
        }

        return false;
    }


    protected function ifAreaAroundShipIsEmpty($startCoords, $endCoords): bool
    {
        $areaAroundShip = $this->getCoordsOfAreaAroundShip($startCoords, $endCoords);

        [$areaStartX, $areaStartY, $areaEndX, $areaEndY] = $areaAroundShip;

        for ($x = $areaStartX; $x <= $areaEndX; $x++) {
            for ($y = $areaStartY; $y <= $areaEndY; $y++) {
                if ($this->shipBoard[$x][$y]->getState() !== Square::EMPTY) {
                    return false;
                }
            }
        }

        return true;
    }


    protected function getCoordsOfAreaAroundShip($startCoords, $endCoords): array
    {
        [$shipStartX, $shipStartY] = $startCoords;
        [$shipEndX, $shipEndY] = $endCoords;

        $areaStartX = $shipStartX - 1;
        $areaStartY = $shipStartY - 1;
        $areaEndX = $shipEndX + 1;
        $areaEndY = $shipEndY + 1;

        if ($areaStartX < 0) {
            $areaStartX = 0;
        }

        if ($areaStartY < 0) {
            $areaStartY = 0;
        }

        if ($areaEndX >= ShipBoard::WIDTH) {
            $areaEndX = ShipBoard::WIDTH - 1;
        }

        if ($areaEndY >= ShipBoard::HEIGHT) {
            $areaEndY = ShipBoard::HEIGHT - 1;
        }

        return [$areaStartX, $areaStartY, $areaEndX, $areaEndY];
    }
}
