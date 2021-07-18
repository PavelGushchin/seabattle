<?php

namespace SeaBattle\Player;

use SeaBattle\Board\AbstractBoard;
use SeaBattle\Board\Square;
use SeaBattle\Board\ShipBoard;
use SeaBattle\Board\ShootingBoard;
use SeaBattle\Ship\Ship;


abstract class AbstractPlayer
{
    public const YOU_HIT_MY_SHIP = "Answer to opponent that he hit my ship";
    public const YOU_KILLED_MY_SHIP = "Answer to opponent that he killed my ship";
    public const YOU_MISSED = "Answer to opponent that he missed";

    protected AbstractBoard $shipBoard;
    protected AbstractBoard $shootingBoard;


    abstract public function getCoordsForShooting(): array;


    public function __construct()
    {
        $this->shipBoard = new ShipBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function handleShotAndGiveResult(int $x, int $y): array
    {
        $attackedSquare = $this->shipBoard->getSquare($x, $y);

        switch ($attackedSquare?->getState()) {
            case Square::EMPTY:
                $attackedSquare->setState(Square::MISSED);
                return ["answer_from_opponent" => self::YOU_MISSED];

            case Square::SHIP:
                $attackedSquare->setState(Square::HIT_SHIP);

                $attackedShip = $attackedSquare->getShip();
                $attackedShip->addHit();

                if ($attackedShip->isKilled()) {
                    $killedShipCoords = $attackedShip->getCoords();
                    $this->markShipAsKilledOnBoard($this->shipBoard, $killedShipCoords);

                    return [
                        "answer_from_opponent" => self::YOU_KILLED_MY_SHIP,
                        "killed_ship_coords" => $killedShipCoords
                    ];
                }

                return ["answer_from_opponent" => self::YOU_HIT_MY_SHIP];
        }

        return ["answer_from_opponent" => self::YOU_MISSED];
    }


    public function writeResultOfShooting(int $x, int $y, array $resultOfShooting): void
    {
        $square = $this->shootingBoard->getSquare($x, $y);

        if ($square === null) {
            return;
        }

        switch ($resultOfShooting["answer_from_opponent"]) {
            case self::YOU_MISSED:
                $square->setState(Square::MISSED);
                break;
            case self::YOU_HIT_MY_SHIP:
                $square->setState(Square::HIT_SHIP);
                break;
            case self::YOU_KILLED_MY_SHIP:
                $killedShipCoords = $resultOfShooting["killed_ship_coords"];
                $this->markShipAsKilledOnBoard($this->shootingBoard, $killedShipCoords);
                break;
        }
    }


    protected function markShipAsKilledOnBoard(AbstractBoard $board, array $ship): void
    {

    }


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

        [[$areaStartX, $areaStartY], [$areaEndX, $areaEndY]] = $areaAroundShip;

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

        return [[$areaStartX, $areaStartY], [$areaEndX, $areaEndY]];
    }


    public function clearBoards(): void
    {
        $this->shipBoard = new ShipBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function hasWon(): bool
    {
        return $this->shootingBoard->getNumberOfKilledShips() === $this->shipBoard->getNumberOfAllShips();
    }


    public function printShipBoard(): string
    {
        return $this->shipBoard->print();
    }


    public function printShootingBoard(): string
    {
        return $this->shootingBoard->print();
    }
}
