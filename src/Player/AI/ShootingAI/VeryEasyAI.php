<?php

namespace SeaBattle\Player\AI\ShootingAI;

use SeaBattle\Field\Slot;
use SeaBattle\Field\Field;


/**
 * RandomShootingAI represents algorithm which
 * shoots randomly (easy level)
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class VeryEasyAI implements InterfaceShootingAI
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
     * @param AbstractBoard|null $shootingBoard Array with ships
     *
     * @return array Array of shot coordinates
     */
    public function getCoordsForShooting(AbstractBoard $shootingBoard = null)
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
