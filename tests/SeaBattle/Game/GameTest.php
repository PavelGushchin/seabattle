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

class GameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \SeaBattle\Game\Game::__construct
     */
    public function testObjectCanBeConstructed()
    {
        $game = new Game();

        $this->assertInstanceOf('SeaBattle\\Game\\Game', $game);

        return $game;
    }

    /**
     * @covers  \SeaBattle\Game\Game::getMyField
     * @uses    \SeaBattle\Field\Field
     * @depends testObjectCanBeConstructed
     */
    public function testMyFieldCanBeRetrieved(Game $game)
    {
        $this->assertInstanceOf('SeaBattle\\Field\\Field', $game->getMyField());
    }

    /**
     * @covers  \SeaBattle\Game\Game::getEnemyField
     * @uses    \SeaBattle\Field\Field
     * @depends testObjectCanBeConstructed
     */
    public function testEnemyFieldCanBeRetrieved(Game $game)
    {
        $this->assertInstanceOf('SeaBattle\\Field\\Field', $game->getEnemyField());
    }

    /**
     * @covers  \SeaBattle\Game\Game::isGameover
     * @depends testObjectCanBeConstructed
     */
    public function testGameoverCanBeRetrieved(Game $game)
    {
        $this->assertSame(true, $game->isGameover());
    }

    /**
     * @covers  \SeaBattle\Game\Game::getWinner
     * @depends testObjectCanBeConstructed
     */
    public function testWinnerCanBeRetrieved(Game $game)
    {
        $this->assertSame(Game::NO_WINNER, $game->getWinner());
    }

    /**
     * @covers \SeaBattle\Game\Game::getTurn
     * @depends testObjectCanBeConstructed
     */
    public function testTurnCanBeRetrieved(Game $game)
    {
        $this->assertSame(Game::MY_TURN, $game->getTurn());
    }

    /**
     * @covers \SeaBattle\Game\Game::passTurnToNextPlayer
     * @depends testObjectCanBeConstructed
     */
    public function testTurnIsPassedToNextPlayer(Game $game)
    {
        $game->passTurnToNextPlayer();

        $this->assertSame(Game::ENEMY_TURN, $game->getTurn());
    }

    /**
     * @covers \SeaBattle\Game\Game::startNewGame
     * @uses   \SeaBattle\Field\Field
     */
    public function testStartingNewGameCorrectDefaultsAreAssigned()
    {
        $game = new Game();
        $game->startNewGame();

        $this->assertSame(Game::NO_WINNER, $game->getWinner());
        $this->assertSame(false, $game->isGameover());
    }

    /**
     * Data provider for testWrongCoordsForShotsDoNotHitShips
     *
     * @return array
     */
    public function wrongCoordsProvider()
    {
        return [
            [-1, -2],
            [Field::WIDTH + 1, Field::HEIGT + 1],
            [-1, Field::HEIGT + 1],
            [Field::WIDTH + 1, -2],
        ];
    }

    /**
     * @covers \SeaBattle\Game\Game::shootingTo
     * @covers \SeaBattle\Field\Field::handleShot
     * @uses   \SeaBattle\Field\Field
     * @uses   \SeaBattle\Game\Game::getEnemyField
     *
     * @dataProvider wrongCoordsProvider
     */
    public function testWrongCoordsForShotsDoNotHitShips($x, $y)
    {
        $game = new Game();
        $game->startNewGame();

        $enemyField = $game->getEnemyField();

        $this->assertFalse($game->shootingTo($enemyField, $x, $y));
    }

    /**
     * @covers \SeaBattle\Game\Game::setGameover
     * @uses   \SeaBattle\Game\Game::isGameover
     */
    public function testGameoverCanBeSetCorrectly()
    {
        $game = new Game();

        $game->setGameover(false);
        $this->assertSame(false, $game->isGameover());

        $game->setGameover(true);
        $this->assertSame(true, $game->isGameover());
    }

    /**
     * @covers \SeaBattle\Game\Game::setWinner
     * @uses   \SeaBattle\Game\Game::getWinner
     */
    public function testWinnerCanBeSetCorrectly()
    {
        $game = new Game();

        $game->setWinner(Game::I_AM_THE_WINNER);
        $this->assertSame(Game::I_AM_THE_WINNER, $game->getWinner());

        $game->setWinner(Game::ENEMY_IS_THE_WINNER);
        $this->assertSame(Game::ENEMY_IS_THE_WINNER, $game->getWinner());

        $game->setWinner(Game::NO_WINNER);
        $this->assertSame(Game::NO_WINNER, $game->getWinner());
    }

    /**
     * @covers \SeaBattle\Game\Game::setTurn
     * @uses   \SeaBattle\Game\Game::getTurn
     */
    public function testTurnCanBeSetCorrectly()
    {
        $game = new Game();

        $game->setTurn(Game::ENEMY_TURN);
        $this->assertSame(Game::ENEMY_TURN, $game->getTurn());

        $game->setTurn(Game::MY_TURN);
        $this->assertSame(Game::MY_TURN, $game->getTurn());
    }
}
