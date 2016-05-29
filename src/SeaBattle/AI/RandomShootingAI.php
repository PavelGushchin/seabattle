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
    /**
     * Main method of the class.
     *
     * It calculates coordinates for next shot
     * and returns them as array:
     *  [
     *      'x' => $x,
     *      'y' => $y,
     *  ]
     *
     * @param array      $slots Array with slots
     * @param array|null $ships Array with ships
     *
     * @return array Array of shot coordinates
     */
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

    /**
     * Returns name of the algorithm
     *
     * @return string
     */
    public function __toString()
    {
        return 'Random algorithm';
    }
}
