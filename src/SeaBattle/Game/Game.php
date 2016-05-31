<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\Game;

use SeaBattle\Field\Field;
use SeaBattle\AI\ShootingWithStrategyAI;
use SeaBattle\AI\SmartShootingAI;

/**
 * This class is playing a role of the main controller of the game
 * which connects all pieces of SeaBattle package together
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class Game
{
    const NO_WINNER = 1;
    const I_AM_WINNER = 2;
    const ENEMY_IS_WINNER = 3;

    const MY_TURN = 4;
    const ENEMY_TURN = 5;

    /**
     * @var Field This variable contains player's Battle Field
     */
    private $myField;

    /**
     * @var Field This variable contains CPU's Battle Field
     */
    private $enemyField;

    /**
     * @var bool Is game over?
     */
    private $gameover = true;

    /**
     * @var int Shows who is the winner of the game (one of
     *          Game::NO_WINNER or
     *          Game::I_AM_WINNER or
     *          Game::ENEMY_IS_WINNER)
     */
    private $winner = self::NO_WINNER;

    /**
     * @var int Shows whom the next turn is (one of
     *          Game::MY_TURN or
     *          Game::ENEMY_TURN)
     */
    private $turn = self::MY_TURN;


    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->myField    = new Field();
        $this->enemyField = new Field();

    }

    /**
     * This method creates new blank Battle Fields for player and CPU
     *
     * Also it randomly places ships on that Battle Fields and assigns
     * default values to some variables
     */
    public function startNewGame()
    {
        $this->myField = new Field();
        $this->myField->createShips();
        $this->myField->placeShipsRandomly();

        $this->enemyField = new Field(new ShootingWithStrategyAI());
        $this->enemyField->createShips();
        $this->enemyField->placeShipsRandomly();

        $this->winner   = self::NO_WINNER;
        $this->gameover = false;
    }

    /**
     * This method creates new blank Battle Fields for 2 CPUs which
     * will be playing against each other
     *
     * Also it randomly places ships on that Battle Fields and assigns
     * default values to some variables
     */
    public function startAutobattleGame()
    {
        $this->myField = new Field(new SmartShootingAI());
        $this->myField->createShips();
        $this->myField->placeShipsRandomly();

        $this->enemyField = new Field(new ShootingWithStrategyAI());
        $this->enemyField->createShips();
        $this->enemyField->placeShipsRandomly();

        $this->winner = self::NO_WINNER;
        $this->gameover = false;
    }

    /**
     * This method is used for shooting to opponent
     *
     * @param  Field $attackedField Indicates which Battle Field is under the fire
     * @param  int   $x             Represents horizontal shooting coordinate
     * @param  int   $y             Represents vertical shooting coordinate
     * @param  bool  $isEnemy       It is 'true' if CPU is shooting
     *
     * @return bool Indicates if our shot was successful or not
     */
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

    /**
     * Return Battle Field of player
     *
     * @return Field
     */
    public function getMyField()
    {
        return $this->myField;
    }

    /**
     * Return Battle Field of CPU
     *
     * @return Field
     */
    public function getEnemyField()
    {
        return $this->enemyField;
    }

    /**
     * Returns 'true' if game is over and 'false' otherwise
     *
     * @return bool
     */
    public function isGameover()
    {
        return $this->gameover;
    }

    /**
     * Sets value for gameover property
     *
     * @param bool $gameover
     */
    public function setGameover($gameover)
    {
        $this->gameover = $gameover;
    }

    /**
     * Returns value which indicates who is the winner: player or CPU
     *
     * @return int
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * Sets the winner
     *
     * @param int $winner
     */
    public function setWinner($winner)
    {
        $this->winner = $winner;
    }

    /**
     * Returns value which indicates whom the next turn is: player's or CPU's
     *
     * @return int
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * Sets value for next turn
     *
     * @param int $turn
     */
    public function setTurn($turn)
    {
        $this->turn = $turn;
    }

    /**
     * This method does what it says: passes turn to next player
     */
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
