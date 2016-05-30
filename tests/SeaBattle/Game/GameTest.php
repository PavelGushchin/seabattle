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

class GameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \SeaBattle\Game\Game::__construct
     */
    public function testObjectCanBeConstructed()
    {
        $g = new Game();

        $this->assertInstanceOf('SeaBattle\\Game\\Game', $g);

        return $g;
    }

    /**
     * @covers  \SeaBattle\Game\Game::getMyField
     * @uses    \SeaBattle\Field\Field
     * @depends testObjectCanBeConstructed
     */
    public function testMyFieldCanBeRetrieved(Game $g)
    {
        $this->assertInstanceOf('SeaBattle\\Field\\Field', $g->getMyField());
    }

    /**
     * @covers  \SeaBattle\Game\Game::getEnemyField
     * @uses    \SeaBattle\Field\Field
     * @depends testObjectCanBeConstructed
     */
    public function testEnemyFieldCanBeRetrieved(Game $g)
    {
        $this->assertInstanceOf('SeaBattle\\Field\\Field', $g->getEnemyField());
    }

    /**
     * @covers  \SeaBattle\Game\Game::isGameover
     * @depends testObjectCanBeConstructed
     */
    public function testGameoverCanBeRetrieved(Game $g)
    {
        $this->assertSame(true, $g->isGameover());
    }

    /**
     * @covers  \SeaBattle\Game\Game::getWinner
     * @depends testObjectCanBeConstructed
     */
    public function testWinnerCanBeRetrieved(Game $g)
    {
        $this->assertSame(Game::NO_WINNER, $g->getWinner());
    }

    /**
     * @covers \SeaBattle\Game\Game::getTurn
     * @depends testObjectCanBeConstructed
     */
    public function testTurnCanBeRetrieved(Game $g)
    {
        $this->assertSame(Game::MY_TURN, $g->getTurn());
    }

    /**
     * @covers \SeaBattle\Game\Game::passTurnToNextPlayer
     * @depends testObjectCanBeConstructed
     */
    public function testTurnIsPassedToNextPlayer(Game $g)
    {
        $g->passTurnToNextPlayer();

        $this->assertSame(Game::ENEMY_TURN, $g->getTurn());
    }
}
