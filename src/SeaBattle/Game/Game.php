<?php

namespace SeaBattle\Game;

use SeaBattle\Field\Field;


class Game
{
    const NO_WINNER = 0;
    const I_AM_WINNER = 1;
    const ENEMY_IS_WINNER = 2;

    private $myField;
    private $enemyField;
    private $gameover = false;
    private $winner = self::NO_WINNER;


    public function __construct()
    {
        $this->myField = new Field();
        $this->enemyField = new Field();
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


    public function startNewGame()
    {
        $this->myField = new Field();
        $this->myField->createShips();
        $this->myField->placeShipsRandomly();

        $this->enemyField = new Field();
        $this->enemyField->createShips();
        $this->enemyField->placeShipsRandomly();
    }


    public function shootingTo(Field $attackedField, $x, $y)
    {
        $result = $attackedField->handleShot($x, $y);

        if ($result['isGameover'] === true) {
            $this->setGameover(true);
            $this->setWinner($result['winner']);
        }
    }
}