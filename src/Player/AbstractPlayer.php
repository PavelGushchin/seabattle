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

    protected ShipBoard $shipBoard;
    protected ShootingBoard $shootingBoard;


    /**
     * Every child class should implement this method and return
     * array with coords for shooting: [int $x, int $y]
     */
    abstract public function getCoordsForShooting(): array;


    public function __construct()
    {
        $this->shipBoard = new ShipBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function createShips(): void
    {
        foreach (ShipBoard::SHIPS_TO_CREATE as $ship) {
            for ($i = 0; $i < $ship["number"]; $i++) {
                $this->createShip($ship["ship size"]);
            }
        }
    }


    public function createShip(int $shipSize): void
    {
        do {
            $direction = rand(Ship::HORIZONTAL, Ship::VERTICAL);
            $headCoords = [
                rand(0, ShipBoard::WIDTH - 1),
                rand(0, ShipBoard::HEIGHT - 1)
            ];

            $ship = new Ship($shipSize, $direction, $headCoords);

            $canShipBeCreated =
                $this->ifShipDoesNotGoOffBoard($ship) &&
                $this->ifAreaAroundShipIsEmpty($ship);

        } while (! $canShipBeCreated);

        $this->shipBoard->addShip($ship);
        $this->shootingBoard->addAliveShip($shipSize);
    }


    protected function ifShipDoesNotGoOffBoard(Ship $ship): bool
    {
        [$tailX, $tailY] = $ship->getTailCoords();

        return $tailX < ShipBoard::WIDTH && $tailY < ShipBoard::HEIGHT;
    }


    protected function ifAreaAroundShipIsEmpty(Ship $ship): bool
    {
        [$areaStartX, $areaStartY, $areaEndX, $areaEndY] = $ship->getCoordsOfAreaAroundShip();

        for ($x = $areaStartX; $x <= $areaEndX; $x++) {
            for ($y = $areaStartY; $y <= $areaEndY; $y++) {
                if ($this->shipBoard->getSquare($x, $y)->getState() !== Square::EMPTY) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Opponent gave us coordinates for shooting: $x and $y, and we are
     * checking either he missed, or hit, or killed our ship.
     */
    public function handleShotAndGiveResult(int $x, int $y): array
    {
        $attackedSquare = $this->shipBoard->getSquare($x, $y);

        switch ($attackedSquare?->getState()) {
            case Square::EMPTY:
                $attackedSquare->setState(Square::MISSED);
                return ["answer" => self::YOU_MISSED];

            case Square::SHIP:
                $attackedSquare->setState(Square::HIT_SHIP);

                $attackedShip = $attackedSquare->getShip();
                $attackedShip->addHit();

                if ($attackedShip->isKilled()) {
                    $this->markShipAsKilledOnBoard($this->shipBoard, $attackedShip);

                    return [
                        "answer" => self::YOU_KILLED_MY_SHIP,
                        "killed_ship" => $attackedShip
                    ];
                }

                return ["answer" => self::YOU_HIT_MY_SHIP];
        }

        return ["answer" => self::YOU_MISSED];
    }


    /**
     * Writing the result of my shooting to ShootingBoard
     */
    public function writeResultOfShooting(int $x, int $y, array $resultOfShooting): void
    {
        $attackedSquare = $this->shootingBoard->getSquare($x, $y);

        if ($attackedSquare === null) {
            return;
        }

        $answerFromOpponent = $resultOfShooting["answer"];

        switch ($answerFromOpponent) {
            case self::YOU_MISSED:
                $attackedSquare->setState(Square::MISSED);
                break;
            case self::YOU_HIT_MY_SHIP:
                $attackedSquare->setState(Square::HIT_SHIP);
                break;
            case self::YOU_KILLED_MY_SHIP:
                $attackedSquare->setState(Square::KILLED_SHIP);

                $killedShip = $resultOfShooting["killed_ship"];

                $this->shootingBoard->addKilledShip($killedShip->getSize());
                $this->markShipAsKilledOnBoard($this->shootingBoard, $killedShip);
                break;
        }
    }

    /**
     * After ship was killed we have to mark Squares, which represents that ship
     * on Board, as "KILLED SHIP".
     * And also we have to mark area around the ship as "MISSED", so player
     * couldn't shoot to it
     */
    protected function markShipAsKilledOnBoard(AbstractBoard $board, Ship $ship): void
    {
        [$areaStartX, $areaStartY, $areaEndX, $areaEndY] = $ship->getCoordsOfAreaAroundShip();

        for ($x = $areaStartX; $x <= $areaEndX; $x++) {
            for ($y = $areaStartY; $y <= $areaEndY; $y++) {
                $squareOnBoard = $board->getSquare($x, $y);

                switch ($squareOnBoard->getState()) {
                    case Square::EMPTY:
                        $squareOnBoard->setState(Square::MISSED);
                        break;
                    case Square::HIT_SHIP:
                        $squareOnBoard->setState(Square::KILLED_SHIP);
                        break;
                }
            }
        }
    }


    public function hasWon(): bool
    {
        $killedEnemyShips = $this->shootingBoard->getNumberOfKilledShips();
        $allEnemyShips = $this->shipBoard->getNumberOfShips();

        return $allEnemyShips === $killedEnemyShips;
    }


    /**
     * If I lost I want to see where all enemy's alive ships were
     */
    public function lookAtOpponentsShips(array $ships): void
    {
        foreach ($ships as $ship) {
            [$headX, $headY] = $ship->getHeadCoords();
            [$tailX, $tailY] = $ship->getTailCoords();

            for ($x = $headX; $x <= $tailX; $x++) {
                for ($y = $headY; $y <= $tailY; $y++) {
                    $currentSquare = $this->shootingBoard->getSquare($x, $y);
                    if ($currentSquare->getState() === Square::EMPTY) {
                        $currentSquare->setState(Square::SHIP);
                    }
                }
            }
        }
    }


    public function getShipBoard(): ShipBoard
    {
        return $this->shipBoard;
    }


    public function getShootingBoard(): ShootingBoard
    {
        return $this->shootingBoard;
    }
}
