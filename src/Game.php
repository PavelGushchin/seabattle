<?php

namespace SeaBattle;

use SeaBattle\Board\AbstractCell;
use SeaBattle\Board\MainBoard;
use SeaBattle\Board\ShootingBoard;
use SeaBattle\Player\AbstractPlayer;
use SeaBattle\Player\AI;
use SeaBattle\Player\EnemyPlayer;
use SeaBattle\Player\MyPlayer;


class Game
{
    const NO_WINNER = "No winner";
    const I_AM_THE_WINNER = "I am the winner";
    const ENEMY_IS_THE_WINNER = "Enemy is the winner";

    const MY_TURN = "My turn";
    const ENEMY_TURN = "Enemy's turn";

    private AbstractPlayer $myPlayer;
    private AbstractPlayer $enemyPlayer;

    private string $theWinner = self::NO_WINNER;
    private string $turn = self::MY_TURN;


    public function __construct()
    {
        $this->myPlayer = new MyPlayer();

        $this->enemyPlayer = new EnemyPlayer(
            new MainBoard(),
            new ShootingBoard(),
            new AI\MediumAI(),
        );
    }


    public function startNewGame()
    {
        $this->myPlayer->clearBoards();
        $this->myPlayer->placeShipsOnMainBoard();

        $this->enemyPlayer->clearBoards();
        $this->enemyPlayer->placeShipsOnMainBoard();

        $this->theWinner = self::NO_WINNER;
        $this->turn = self::MY_TURN;
    }


    public function play()
    {
        if ($this->gameIsOver()) {
            return;
        }

        /** My shooting **/
        [$x, $y] = $this->myPlayer->getCoordsForShooting();

        if ($x === null || $y === null) {
            return;
        }

        $wasShipHit = $this->enemyPlayer->checkIfShipWasHit($x, $y);
        $this->myPlayer->writeResultOfShooting($x, $y, $wasShipHit);

        $this->turn = $wasShipHit ? self::MY_TURN : self::ENEMY_TURN;

        if ($this->myPlayer->checkIfWon()) {
            $this->theWinner = self::I_AM_THE_WINNER;
            return;
        }

        /** Enemy's shooting **/
        while ($this->turn === self::ENEMY_TURN) {
            [$x, $y] = $this->enemyPlayer->getCoordsForShooting();
            $wasShipHit = $this->myPlayer->checkIfShipWasHit($x, $y);
            $this->enemyPlayer->writeResultOfShooting($x, $y, $wasShipHit);

            $this->turn = $wasShipHit ? self::ENEMY_TURN : self::MY_TURN;

            if ($this->enemyPlayer->checkIfWon()) {
                $this->theWinner = self::ENEMY_IS_THE_WINNER;
                return;
            }
        }
    }


    public function startAutobattle()
    {
        $this->myPlayer = new enemyPlayer(new HardAI());
        $this->myPlayer->placeShipsOnBoard();

        $this->enemyPlayer = new enemyPlayer(new MediumAI());
        $this->enemyPlayer->placeShipsOnBoard();

        $this->theWinner = self::NO_WINNER;
        $this->turn = self::MY_TURN;
    }


    public function getMyPlayer(): AbstractPlayer
    {
        return $this->myPlayer;
    }

    public function getEnemyPlayer(): AbstractPlayer
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
