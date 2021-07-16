<?php

namespace SeaBattle;

use SeaBattle\Board\Cell;
use SeaBattle\Board\Board;
use SeaBattle\Player\Player;
use SeaBattle\Player\AI\ShootingAI\MediumAI;


class Game
{
    const NO_WINNER = "No winner";
    const I_AM_THE_WINNER = "I am the winner";
    const ENEMY_IS_THE_WINNER = "Enemy is the winner";

    const MY_TURN = "My turn";
    const ENEMY_TURN = "Enemy's turn";

    protected Player $myPlayer;
    protected Player $enemyPlayer;

    protected string $theWinner = self::NO_WINNER;
    protected string $turn = self::MY_TURN;


    public function __construct()
    {
        $this->myPlayer = new Player();
        $this->enemyPlayer = new Player(new MediumAI());
    }


    public function startNewGame()
    {
        $this->myPlayer->clearBoards();
        $this->myPlayer->createShipsOnMainBoard();

        $this->enemyPlayer->clearBoards();
        $this->enemyPlayer->createShipsOnMainBoard();

        $this->theWinner = self::NO_WINNER;
        $this->turn = self::MY_TURN;
    }


    public function play()
    {
        if ($this->gameIsOver()) {
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

        if ($resultOfShooting === Player::MISSED) {
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

            if ($resultOfShooting === Player::MISSED) {
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


    public function startAutobattle()
    {

    }


    public function getMyPlayer(): Player
    {
        return $this->myPlayer;
    }

    public function getEnemyPlayer(): Player
    {
        return $this->enemyPlayer;
    }


    public function gameIsOver(): bool
    {
        return $this->theWinner !== self::NO_WINNER;
    }


    public function getTheWinner(): string
    {
        return $this->theWinner;
    }
}
