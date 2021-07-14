<?php

namespace SeaBattle\Player;

use SeaBattle\Board\MainBoard;
use SeaBattle\Board\ShootingBoard;


abstract class AbstractPlayer
{
    protected MainBoard $myBoard;
    protected ShootingBoard $shootingBoard;


    abstract public function getCoordsForShooting(): array;


    public function __construct()
    {
        $this->myBoard = new MainBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function checkIfShipWasHit(int $x, int $y): bool
    {
        return $this->myBoard->checkIfShipWasHit($x, $y);
    }


    public function writeResultOfShooting(int $x, int $y, bool $wasShipHit)
    {
        $this->shootingBoard->writeResultOfShooting($x, $y, $wasShipHit);
    }


    public function placeShipsOnBoard()
    {
        $this->myBoard->placeShipsOnBoard();
    }


    public function isLost(): bool
    {
        return $this->myBoard->areAllShipsDead();
    }

}
