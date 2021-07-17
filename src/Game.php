<?php

namespace SeaBattle;

use SeaBattle\Board\Cell;
use SeaBattle\Board\AbstractBoard;
use SeaBattle\Player\AbstractPlayer;
use SeaBattle\Player\MyPlayer;
use SeaBattle\Player\EnemyPlayer;
use SeaBattle\Player\AI\ShootingAI\MediumAI;


class Game
{
    public const NO_WINNER = "No winner";
    public const I_AM_THE_WINNER = "I am the winner";
    public const ENEMY_IS_THE_WINNER = "Enemy is the winner";

    public const MY_TURN = "My turn";
    public const ENEMY_TURN = "Enemy's turn";

    protected AbstractPlayer $myPlayer;
    protected AbstractPlayer $enemyPlayer;

    protected string $theWinner = self::NO_WINNER;
    protected string $turn = self::MY_TURN;


    public function __construct()
    {
        $this->myPlayer = new MyPlayer();
        $this->enemyPlayer = new EnemyPlayer(new MediumAI());
    }


    public function startNewGame(): void
    {
        $this->myPlayer->clearBoards();
        $this->myPlayer->createShipsOnMainBoard();

        $this->enemyPlayer->clearBoards();
        $this->enemyPlayer->createShipsOnMainBoard();

        $this->theWinner = self::NO_WINNER;
        $this->turn = self::MY_TURN;
    }


    public function play(): void
    {
        if ($this->isGameOver()) {
            return;
        }

        $myPlayer = $this->myPlayer;
        $enemyPlayer = $this->enemyPlayer;

        /** My shooting **/
        [$x, $y] = $myPlayer->getCoordsForShooting();

        if ($x === null || $y === null) {
            return;
        }

        $resultOfShooting = $enemyPlayer->handleShotAndGiveResult($x, $y);
        $myPlayer->writeResultOfShooting($x, $y, $resultOfShooting);

        if ($resultOfShooting === MyPlayer::MISSED) {
            $this->turn = self::ENEMY_TURN;
        } else {
            $this->turn = self::MY_TURN;
        }

        if ($myPlayer->checkIfWon()) {
            $this->theWinner = self::I_AM_THE_WINNER;
            return;
        }

        /** Enemy's shooting **/
        while ($this->turn === self::ENEMY_TURN) {
            [$x, $y] = $enemyPlayer->getCoordsForShooting();

            $resultOfShooting = $enemyPlayer->handleShotAndGiveResult($x, $y);
            $myPlayer->writeResultOfShooting($x, $y, $resultOfShooting);

            if ($resultOfShooting === EnemyPlayer::MISSED) {
                $this->turn = self::ENEMY_TURN;
            } else {
                $this->turn = self::MY_TURN;
            }

            if ($enemyPlayer->checkIfWon()) {
                $this->theWinner = self::ENEMY_IS_THE_WINNER;
                return;
            }
        }
    }


    public function startAutobattle(): void
    {

    }


    public function getMyPlayer(): AbstractPlayer
    {
        return $this->myPlayer;
    }


    public function getEnemyPlayer(): AbstractPlayer
    {
        return $this->enemyPlayer;
    }


    public function isGameOver(): bool
    {
        return $this->theWinner !== self::NO_WINNER;
    }


    public function getTheWinner(): string
    {
        return $this->theWinner;
    }
}
