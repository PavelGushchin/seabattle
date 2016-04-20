<?php

namespace SeaBattle\AI;

use SeaBattle\Field\Slot;
use SeaBattle\Field\Field;

class RandomShootingAI implements IShootingAI
{
    public function calculateCoordsForShooting($slots)
    {
        do {
            $x = mt_rand(0, Field::WIDTH - 1);
            $y = mt_rand(0, Field::HEIGT - 1);
            $slotState = $slots[$x][$y]->getState();
        } while ($slotState !== Slot::SLOT_IS_UNCOVERED &&
            $slotState !== Slot::THERE_IS_A_SHIP);

        return [
            'x' => $x,
            'y' => $y
        ];
    }
}