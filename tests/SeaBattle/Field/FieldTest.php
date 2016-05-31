<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \SeaBattle\Field\Field::createShips
     * @uses   \SeaBattle\Field\Field::__construct
     * @uses   \SeaBattle\Ship\Ship::__construct
     * @uses   \SeaBattle\Field\Field::getShips
     */
    public function testShipsHaveBeenCreated()
    {
        $field = new Field();
        $field->createShips();

        $this->assertSame(10, count($field->getShips()));
    }

    /**
     * Data provider for testShootingToEmptyFieldIsReturningFalse
     *
     * @return array
     */
    public function shootingCoordsProvider()
    {
        return [
            [4, 4],
            [0, 0],
            [-1, -1],
            [Field::WIDTH + 1, Field::HEIGT + 1],
        ];
    }

    /**
     * @covers \SeaBattle\Field\Field::handleShot
     * @uses   \SeaBattle\Field\Field::__construct
     *
     * @dataProvider shootingCoordsProvider
     */
    public function testShootingToEmptyFieldIsReturningFalse($x, $y)
    {
        $field = new Field();
        $shipWasHit = $field->handleShot($x, $y);

        $this->assertFalse($shipWasHit);
    }
}
