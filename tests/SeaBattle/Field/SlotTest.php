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

class SlotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \SeaBattle\Field\Slot::setState
     * @uses   \SeaBattle\Field\Slot::getState
     */
    public function testSlotStateCanBeSetCorrectly()
    {
        $slot = new Slot();
        $slot->setState(Slot::THERE_IS_A_SHIP);

        $this->assertSame(Slot::THERE_IS_A_SHIP, $slot->getState());
    }

    /**
     * @covers \SeaBattle\Field\Slot::setShipId
     * @uses   \SeaBattle\Field\Slot::getShipId
     */
    public function testShipIdCanBeSetCorrectly()
    {
        $slot = new Slot();
        $slot->setShipId(3);

        $this->assertSame(3, $slot->getShipId());
    }
}
