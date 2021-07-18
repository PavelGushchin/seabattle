<?php

namespace SeaBattle;

use SeaBattle\Board\Square;
use SeaBattle\Board\AbstractBoard;
use SeaBattle\Player\AbstractPlayer;
use SeaBattle\Player\AI\ShootingAI\VeryEasyAI;
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

        $answerFromEnemyPlayer = $enemyPlayer->handleShotAndGiveAnswer($x, $y);
        $myPlayer->writeResultOfShooting($x, $y, $answerFromEnemyPlayer);

        if ($myPlayer->checkIfWon()) {
            $this->theWinner = self::I_AM_THE_WINNER;
            return;
        }

        if ($answerFromEnemyPlayer === EnemyPlayer::YOU_MISSED) {
            $this->turn = self::ENEMY_TURN;
        } else {
            $this->turn = self::MY_TURN;
        }

        /** Enemy's shooting **/
        while ($this->turn === self::ENEMY_TURN) {
            [$x, $y] = $enemyPlayer->getCoordsForShooting();

            $answerFromMyPlayer = $myPlayer->handleShotAndGiveAnswer($x, $y);
            $enemyPlayer->writeResultOfShooting($x, $y, $answerFromMyPlayer);

            if ($enemyPlayer->checkIfWon()) {
                $this->theWinner = self::ENEMY_IS_THE_WINNER;
                return;
            }

            if ($answerFromMyPlayer === MyPlayer::YOU_MISSED) {
                $this->turn = self::MY_TURN;
            } else {
                $this->turn = self::ENEMY_TURN;
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
