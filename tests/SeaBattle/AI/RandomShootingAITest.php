<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\AI;

use SeaBattle\Field\Field;

class RandomShootingAITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \SeaBattle\AI\RandomShootingAI::calculateCoordsForShooting
     * @uses   \SeaBattle\Field\Field::__construct
     * @uses   \SeaBattle\Field\Field::createShips
     * @uses   \SeaBattle\Field\Field::getSlots
     * @uses   \SeaBattle\Field\Field::getShips
     */
    public function testReturnedCoordsAreWithinAppropriateRange()
    {
        $attackedField = new Field();
        $attackedField->createShips();

        $shootingAI = new RandomShootingAI();
        $coordsForShooting = $shootingAI->calculateCoordsForShooting(
            $attackedField->getSlots(),
            $attackedField->getShips()
        );

        $x = $coordsForShooting['x'];
        $y = $coordsForShooting['y'];

        $this->assertTrue($x >= 0 && $x < Field::WIDTH);
        $this->assertTrue($y >= 0 && $y < Field::HEIGT);
    }

    /**
     * @covers \SeaBattle\AI\RandomShootingAI::__toString
     */
    public function testReturnsStringWhenToStringMethodIsCalled()
    {
        $shootingAI = new RandomShootingAI();
        $algorithmName = $shootingAI->__toString();

        $this->assertTrue(is_string($algorithmName));
    }
}
