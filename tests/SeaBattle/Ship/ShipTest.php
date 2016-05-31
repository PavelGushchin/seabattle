<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\Ship;

class ShipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testAfterShipWasHitItsDeathDeterminedCorrectly
     *
     * @return array
     */
    public function shipDataProvider()
    {
        return [
            [1, 0, true],
            [2, 2, true],
            [3, 0, false],
            [4, 5, true],
        ];
    }

    /**
     * @covers \SeaBattle\Ship\Ship::addHitAndCheckForDeath
     * @uses   \SeaBattle\Ship\Ship::__construct
     * @uses   \SeaBattle\Ship\Ship::setHits
     *
     * @dataProvider shipDataProvider
     */
    public function testAfterShipWasHitItsDeathDeterminedCorrectly(
        $size,
        $hits,
        $expectedResult
    ) {
        $id = 0;
        $ship = new Ship($id, $size);
        $ship->setHits($hits);

        $this->assertSame($expectedResult, $ship->addHitAndCheckForDeath());
    }
}
