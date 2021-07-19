<?php

namespace SeaBattle;

use SeaBattle\Player\AbstractPlayer;
use SeaBattle\Player\AI\VeryEasyAI;
use SeaBattle\Player\EnemyPlayer;
use SeaBattle\Player\MyPlayer;


class Game
{
    public const NO_WINNER = "No winner";
    public const I_AM_THE_WINNER = "I am the winner";
    public const ENEMY_IS_THE_WINNER = "Enemy is the winner";

    public const MY_TURN = "My turn";
    public const ENEMY_TURN = "Enemy's turn";

    protected string $theWinner = self::NO_WINNER;
    protected string $turn = self::MY_TURN;

    protected AbstractPlayer $myPlayer;
    protected AbstractPlayer $enemyPlayer;


    public function __construct()
    {
        $this->myPlayer = new MyPlayer();
        $this->enemyPlayer = new EnemyPlayer(new VeryEasyAI());
    }


    public function startNewGame(): void
    {
        $this->myPlayer->clearBoards();
        $this->myPlayer->createShips();

        $this->enemyPlayer->clearBoards();
        $this->enemyPlayer->createShips();

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

        $answerFromOpponent = $resultOfShooting["answer_from_opponent"];

        if ($answerFromOpponent === EnemyPlayer::YOU_MISSED) {
            $this->turn = self::ENEMY_TURN;
        } else {
            $this->turn = self::MY_TURN;
        }

        if ($myPlayer->hasWon()) {
            $this->theWinner = self::I_AM_THE_WINNER;
            return;
        }

        /** Enemy's shooting **/
        while ($this->turn === self::ENEMY_TURN) {
            [$x, $y] = $enemyPlayer->getCoordsForShooting();

            $resultOfShooting = $myPlayer->handleShotAndGiveResult($x, $y);
            $enemyPlayer->writeResultOfShooting($x, $y, $resultOfShooting);

            $answerFromOpponent = $resultOfShooting["answer_from_opponent"];

            if ($answerFromOpponent === MyPlayer::YOU_MISSED) {
                $this->turn = self::MY_TURN;
            } else {
                $this->turn = self::ENEMY_TURN;
            }

            if ($enemyPlayer->hasWon()) {
                $this->theWinner = self::ENEMY_IS_THE_WINNER;
                return;
            }
        }
    }


    public function isGameOver(): bool
    {
        return $this->theWinner !== self::NO_WINNER;
    }


    public function getTheWinner(): string
    {
        return $this->theWinner;
    }


    public function getMyPlayer(): AbstractPlayer
    {
        return $this->myPlayer;
    }


    public function getEnemyPlayer(): AbstractPlayer
    {
        return $this->enemyPlayer;
    }
}
