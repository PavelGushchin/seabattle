<?php

namespace SeaBattle\Player;

use SeaBattle\Board\AbstractMainBoard;
use SeaBattle\Board\AbstractShootingBoard;


abstract class AbstractPlayer
{
    protected AbstractMainBoard $mainBoard;
    protected AbstractShootingBoard $shootingBoard;


    abstract public function __construct();
    abstract public function getCoordsForShooting(): array;


    public function checkIfShipWasHit(int $x, int $y): bool
    {
        return $this->mainBoard->checkIfShipWasHit($x, $y);
    }


    public function writeResultOfShooting(int $x, int $y, bool $wasShipHit)
    {
        $this->shootingBoard->writeResultOfShooting($x, $y, $wasShipHit);
    }


    public function checkIfWon(): bool
    {
        return $this->shootingBoard->areAllShipsKilled();
    }


    public function placeShipsOnMainBoard()
    {
        $this->mainBoard->placeShipsOnBoard();
    }


    public function clearBoards()
    {
        $this->mainBoard->clear();
        $this->shootingBoard->clear();
    }
}
