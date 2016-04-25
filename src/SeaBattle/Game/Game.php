<?php

namespace SeaBattle\Game;

use SeaBattle\Field\Field;
use SeaBattle\AI\SmartShootingAI;
use SeaBattle\AI\RandomShootingAI;
use SeaBattle\AI\ShootingWithStrategyAI;


class Game
{
    const NO_WINNER = 0;
    const I_AM_WINNER = 1;
    const ENEMY_IS_WINNER = 2;

    const MY_TURN = 0;
    const ENEMY_TURN = 1;

    private $myField;
    private $enemyField;
    private $gameover = false;
    private $winner = self::NO_WINNER;
    private $turn = self::MY_TURN;


    public function __construct()
    {
        $this->myField = new Field();
        $this->enemyField = new Field();
    }


    public function startNewGame()
    {
        $this->myField = new Field();
        $this->myField->createShips();
        $this->myField->placeShipsRandomly();

        $this->enemyField = new Field(new ShootingWithStrategyAI());
        $this->enemyField->createShips();
        $this->enemyField->placeShipsRandomly();

        $this->winner = self::NO_WINNER;
        $this->gameover = false;
    }


    /*public function startAutobattleGame()
    {
        $this->myField = new Field(new SmartShootingAI());
        $this->myField->createShips();
        $this->myField->placeShipsRandomly();

        $this->enemyField = new Field(new ShootingWithStrategyAI());
        $this->enemyField->createShips();
        $this->enemyField->placeShipsRandomly();

        $this->winner = self::NO_WINNER;
        $this->gameover = false;
    }*/


    public function shootingTo(Field $attackedField, $x, $y, $isEnemy = false)
    {
        $shipWasHit = $attackedField->handleShot($x, $y);

        if ($attackedField->allShipsAreDead()) {
            $this->setGameover(true);

            if ($isEnemy === false) {
                $this->setWinner(Game::I_AM_WINNER);
            } else {
                $this->setWinner(Game::ENEMY_IS_WINNER);
            }
        }

        return $shipWasHit;
    }


    public function getMyField()
    {
        return $this->myField;
    }

    public function getEnemyField()
    {
        return $this->enemyField;
    }


    public function isGameover()
    {
        return $this->gameover;
    }

    public function setGameover($gameover)
    {
        $this->gameover = $gameover;
    }


    public function getWinner()
    {
        return $this->winner;
    }

    public function setWinner($winner)
    {
        $this->winner = $winner;
    }


    public function getTurn()
    {
        return $this->turn;
    }

    public function setTurn($turn)
    {
        $this->turn = $turn;
    }

    public function passTurnToNextPlayer()
    {
        switch ($this->getTurn()) {
            case self::MY_TURN:
                $this->turn = self::ENEMY_TURN;
                break;
            case self::ENEMY_TURN:
                $this->turn = self::MY_TURN;
                break;
        }
    }
}