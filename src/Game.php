<?php

namespace SeaBattle;

use SeaBattle\Player\AbstractPlayer;
use SeaBattle\Player\AI;
use SeaBattle\Player\EnemyPlayer;
use SeaBattle\Player\MyPlayer;


class Game
{
    public const NO_WINNER = "No winner";
    public const I_AM_THE_WINNER = "I am the winner";
    public const ENEMY_IS_THE_WINNER = "Enemy is the winner";

    public const I_AM_NEXT = "I am next";
    public const ENEMY_IS_NEXT = "Enemy is next";

    protected string $theWinner = self::NO_WINNER;
    protected string $whoIsNext = self::I_AM_NEXT;
    protected bool $gameIsRunning = false;

    protected AbstractPlayer $myPlayer;
    protected AbstractPlayer $enemyPlayer;


    public function __construct()
    {
        $this->startNewGame();
    }


    public function startNewGame(): void
    {
        $this->myPlayer = new MyPlayer();
        $this->myPlayer->createShips();

        $this->enemyPlayer = new EnemyPlayer(new AI\SmartAI());
        $this->enemyPlayer->createShips();

        $this->theWinner = self::NO_WINNER;
        $this->whoIsNext = self::I_AM_NEXT;
        $this->gameIsRunning = true;
    }


    public function play(): void
    {
        if (! $this->gameIsRunning) {
            return;
        }

        $myPlayer = $this->myPlayer;
        $enemyPlayer = $this->enemyPlayer;

        /** My player is shooting **/
        [$x, $y] = $myPlayer->getCoordsForShooting();

        if ($x === null || $y === null) {
            return;
        }

        $resultOfShooting = $enemyPlayer->handleShotAndGiveResult($x, $y);
        $myPlayer->writeResultOfShooting($x, $y, $resultOfShooting);

        if ($resultOfShooting["answer"] === EnemyPlayer::YOU_MISSED) {
            $this->whoIsNext = self::ENEMY_IS_NEXT;
        } else {
            $this->whoIsNext = self::I_AM_NEXT;
        }

        if ($myPlayer->hasWon()) {
            $this->theWinner = self::I_AM_THE_WINNER;
            $this->gameIsRunning = false;
            return;
        }

        /** Enemy player is shooting **/
        while ($this->whoIsNext === self::ENEMY_IS_NEXT) {
            [$x, $y] = $enemyPlayer->getCoordsForShooting();

            $resultOfShooting = $myPlayer->handleShotAndGiveResult($x, $y);
            $enemyPlayer->writeResultOfShooting($x, $y, $resultOfShooting);

            if ($resultOfShooting["answer"] === MyPlayer::YOU_MISSED) {
                $this->whoIsNext = self::I_AM_NEXT;
            } else {
                $this->whoIsNext = self::ENEMY_IS_NEXT;
            }

            if ($enemyPlayer->hasWon()) {
                $this->theWinner = self::ENEMY_IS_THE_WINNER;
                $this->gameIsRunning = false;
                return;
            }
        }
    }


    public function getMyPlayer(): AbstractPlayer
    {
        return $this->myPlayer;
    }


    public function getEnemyPlayer(): AbstractPlayer
    {
        return $this->enemyPlayer;
    }


    public function getTheWinner(): string
    {
        return $this->theWinner;
    }


    public function startAutobattleGame()
    {
        $this->myPlayer = new EnemyPlayer(new AI\SmartAI());
        $this->myPlayer->createShips();

        $this->enemyPlayer = new EnemyPlayer(new AI\ImprovedRandomAI());
        $this->enemyPlayer->createShips();

        $this->theWinner = self::NO_WINNER;
        $this->whoIsNext = self::I_AM_NEXT;
        $this->gameIsRunning = true;


        $firstAI = $this->myPlayer;
        $secondAI = $this->enemyPlayer;

        while($this->gameIsRunning) {
            /** First AI is shooting **/
            while ($this->whoIsNext === self::I_AM_NEXT) {
                [$x, $y] = $firstAI->getCoordsForShooting();

                $resultOfShooting = $secondAI->handleShotAndGiveResult($x, $y);
                $firstAI->writeResultOfShooting($x, $y, $resultOfShooting);

                if ($resultOfShooting["answer"] === EnemyPlayer::YOU_MISSED) {
                    $this->whoIsNext = self::ENEMY_IS_NEXT;
                } else {
                    $this->whoIsNext = self::I_AM_NEXT;
                }

                if ($firstAI->hasWon()) {
                    $this->theWinner = self::I_AM_THE_WINNER;
                    $this->gameIsRunning = false;
                    return;
                }
            }

            /** Second AI is shooting **/
            while ($this->whoIsNext === self::ENEMY_IS_NEXT) {
                [$x, $y] = $secondAI->getCoordsForShooting();

                $resultOfShooting = $firstAI->handleShotAndGiveResult($x, $y);
                $secondAI->writeResultOfShooting($x, $y, $resultOfShooting);

                if ($resultOfShooting["answer"] === MyPlayer::YOU_MISSED) {
                    $this->whoIsNext = self::I_AM_NEXT;
                } else {
                    $this->whoIsNext = self::ENEMY_IS_NEXT;
                }

                if ($secondAI->hasWon()) {
                    $this->theWinner = self::ENEMY_IS_THE_WINNER;
                    $this->gameIsRunning = false;
                    return;
                }
            }
        }
    }
}
