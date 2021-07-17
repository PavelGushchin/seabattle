<?php

namespace SeaBattle\Player;

use SeaBattle\Board\AbstractBoard;
use SeaBattle\Board\Square;
use SeaBattle\Board\ShipBoard;
use SeaBattle\Board\ShootingBoard;


abstract class AbstractPlayer
{
    public const YOU_HIT_MY_SHIP = "Answer to opponent that he hit my ship";
    public const YOU_KILLED_MY_SHIP = "Answer to opponent that he killed my ship";
    public const YOU_MISSED = "Answer to opponent that he missed";

    protected array $needToCreateTheseShips = [
        ["size" => 4, "amount" => 1],
        ["size" => 3, "amount" => 2],
        ["size" => 2, "amount" => 3],
        ["size" => 1, "amount" => 4],
    ];

    protected AbstractBoard $shipBoard;
    protected AbstractBoard $shootingBoard;


    abstract public function getCoordsForShooting(): array;


    public function __construct()
    {
        $this->shipBoard = new ShipBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function createShipsOnMainBoard(): void
    {
        foreach ($this->needToCreateTheseShips as $ships) {
            for ($i = 0; $i < $ships["amount"]; $i++) {
                $this->shipBoard->addShip($ships["size"]);
            }
        }

        $this->placeShipsOnBoard($this->shipBoard);
    }


    public function clearBoards(): void
    {
        $this->shipBoard = new ShipBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function writeResultOfShooting(int $x, int $y, int $resultOfShooting): void
    {

    }


    public function checkIfWon(): bool
    {
        return $this->shootingBoard->getNumberOfKilledShips() === $this->shipBoard->getNumberOfAllShips();
    }


    public function printMainBoard(): string
    {
        return $this->shipBoard->print();
    }


    public function printShootingBoard(): string
    {
        return $this->shootingBoard->print();
    }


    public function handleShotAndGiveAnswer(int $x, int $y): string
    {
        $attackedSquare = $this->shipBoard->getSquare($x, $y);

        if ($attackedSquare === null) {
            return self::YOU_MISSED;
        }

        switch ($attackedSquare->getState()) {
            case Square::EMPTY:
                $attackedSquare->setState(Square::MISSED);
                return self::YOU_MISSED;
            case Square::SHIP:
                $ship = $attackedSquare->getShip();

                $wasShipKilled = $this->hitMyShipAndCheckIfItWasKilled($ship);

                if ($isShipKilled) {
                    $this->markShipAsKilledOnBoard($this->shipBoard, $shipId);
                    return self::YOU_KILLED_MY_SHIP;
                }

                $attackedSquare->setState(Square::HIT_SHIP);
                return self::YOU_HIT_MY_SHIP;
        }

        return self::YOU_MISSED;
    }


    protected function hitMyShipAndCheckIfItWasKilled(int $shipId): bool
    {
        $isShipKilled = $ship?->addHit()?->checkIsKilled();

        return $ship?->addHit()?->checkIsKilled();
    }

    protected function markShipAsKilledOnBoard(AbstractBoard $board, int $shipId): void
    {
        $ship = $board->getShipById($shipId);

    }
}
