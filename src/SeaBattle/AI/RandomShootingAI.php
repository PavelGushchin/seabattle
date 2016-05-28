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

use SeaBattle\Field\Slot;
use SeaBattle\Field\Field;

/**
 * RandomShootingAI represents algorithm which
 * shoots randomly (easy level)
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class RandomShootingAI implements ShootingAIInterface
{
    public function calculateCoordsForShooting($slots, $ships = null)
    {
        do {
            $x = mt_rand(0, Field::WIDTH - 1);
            $y = mt_rand(0, Field::HEIGT - 1);
            $slotState = $slots[$x][$y]->getState();
        } while ($slotState !== Slot::SLOT_IS_UNCOVERED &&
            $slotState !== Slot::THERE_IS_A_SHIP);

        return [
            'x' => $x,
            'y' => $y,
        ];
    }

    public function __toString()
    {
        return 'Random algorithm';
    }
}
