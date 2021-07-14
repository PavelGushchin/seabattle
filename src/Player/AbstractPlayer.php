<?php


namespace SeaBattle\Player;

use SeaBattle\Board\AbstractBoard;
use SeaBattle\Board\EnemyBoard;
use SeaBattle\Board\MyBoard;


abstract class AbstractPlayer
{
    protected MyBoard $myBoard;
    protected EnemyBoard $enemyBoard;


    abstract public function getCoordsForShooting(): array;
    abstract public function answerIfShipWasHit(int $x, int $y): bool;
    abstract public function writeResultOfShooting(int $x, int $y, bool $wasShipHit);


    public function isLost(): bool
    {
        return $this->myBoard->getNumOfAliveShips() === 0;
    }


    public function getMyBoard(): MyBoard
    {
        return $this->myBoard;
    }


    public function getEnemyBoard(): EnemyBoard
    {
        return $this->enemyBoard;
    }
}
