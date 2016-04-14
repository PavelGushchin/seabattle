<?php

namespace SeaBattle\Game;

use SeaBattle\Field\Field;


class Game
{
    private $myField;
    private $enemyField;
    private $_isGameRunning = false;
    private $_isMyTurn = true;

    public function __construct(Field $myField, Field $enemyField)
    {
        $this->myField = $myField;
        $this->enemyField = $enemyField;
    }

    public function getMyField()
    {
        return $this->myField;
    }

    public function getEnemyField()
    {
        return $this->enemyField;
    }

    public function isMyTurn()
    {
        return $this->_isMyTurn;
    }

    public function isRunning()
    {
        return $this->_isGameRunning;
    }

    public function setRunning($isRunning)
    {
        $this->_isGameRunning = $isRunning;
    }


    public function passTurnToNextPlayer()
    {
        $this->_isMyTurn = !$this->isMyTurn();
    }


    public function clearFields()
    {
        $this->myField->clear();
        $this->enemyField->clear();
    }


    public function startGame()
    {
        if (!$this->myField->isReady()) {
            throw new \Exception('My Field is not ready!');
        }

        $this->enemyField->locateShips();

        if (!$this->enemyField->isReady()) {
            throw new \Exception('Enemy Field is not ready!');
        }
    }


    public function shootingTo(Field $fieldUnderFire, $x, $y)
    {
        $fieldUnderFire->handleShot($x, $y);
    }


}