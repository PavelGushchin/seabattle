<?php

namespace SeaBattle;

use SeaBattle\Player\AbstractPlayer;
use SeaBattle\Player\AI\NormalAI;
use SeaBattle\Player\AI\EasyAI;
use SeaBattle\Player\EnemyPlayer;
use SeaBattle\Player\MyPlayer;


/**
 * This class is playing a role of main controller
 * which connects all pieces of the project together
 */
class Game
{
    const NO_WINNER = "No winner";
    const I_AM_THE_WINNER = "I am the winner";
    const ENEMY_IS_THE_WINNER = "Enemy is the winner";

    private AbstractPlayer $myPlayer;
    private AbstractPlayer $enemyPlayer;

    private string $theWinner = Game::NO_WINNER;
    private bool $isMyTurn = true;


    public function __construct()
    {
        $this->myPlayer = new MyPlayer();
        $this->enemyPlayer = new EnemyPlayer();
    }


    public function startNewGame()
    {
        $this->myPlayer = new MyPlayer();
        $this->myPlayer->placeShipsOnBoard();

        $this->enemyPlayer = new enemyPlayer(new NormalAI());
        $this->enemyPlayer->placeShipsOnBoard();

        $this->theWinner = Game::NO_WINNER;
        $this->isMyTurn = true;
    }


    public function play()
    {
        if ($this->gameIsOver()) {
            return;
        }

        $myPlayer = $this->myPlayer;
        $enemyPlayer = $this->enemyPlayer;

        if (isset($_GET['x']) && isset($_GET['y'])) {
            $x = intval($_GET['x']);
            $y = intval($_GET['y']);

            $wasShipHit = $myPlayer->shootTo($enemyPlayer->getBoard(), $x, $y);

            $this->isMyTurn = $wasShipHit;

            if ($enemyPlayer->isLost()) {
                $this->theWinner = Game::I_AM_THE_WINNER;
                return;
            }

            while ($this->isMyTurn === false) {
                $wasShipHit = $enemyPlayer->shootTo($myPlayer->getBoard());

                $this->isMyTurn = !$wasShipHit;

                if ($myPlayer->isLost()) {
                    $this->theWinner = Game::ENEMY_IS_THE_WINNER;
                    return;
                }
            }
        }
    }


    public function startAutobattle()
    {
        $this->myPlayer = new MyPlayer(new EasyAI());
        $this->myPlayer->placeShipsOnBoard();

        $this->enemyPlayer = new enemyPlayer(new NormalAI());
        $this->enemyPlayer->placeShipsOnBoard();

        $this->theWinner = Game::NO_WINNER;
        $this->isMyTurn = true;
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
        return $this->theWinner !== Game::NO_WINNER;
    }


    public function getTheWinner(): string
    {
        return $this->theWinner;
    }
}
