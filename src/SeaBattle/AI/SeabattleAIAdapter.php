<?php

namespace SeaBattle\AI;

use SeaBattle\Field\Field;
use SeaBattle\Field\Slot;

class SeabattleAIAdapter implements IShootingAI
{
    const CELL_IS_HIDDEN = 0;
    const PLAYER_MISSED = 1;
    const SHIP_IS_HIT = 2;
    const SHIP_IS_DEAD = 3;

    private $shooter;


    public function __construct($shooter)
    {
        $this->shooter = $shooter;
    }

    public function calculateCoordsForShooting($slots, $ships = null)
    {
        for ($i = 0; $i < Field::WIDTH; $i++) {
            for($j = 0; $j < Field::HEIGT; $j++) {
                switch($slots[$i][$j]->getState()) {
                    case Slot::SLOT_IS_UNCOVERED:
                    case Slot::THERE_IS_A_SHIP:
                        $battleField[$i][$j] = self::CELL_IS_HIDDEN;
                        break;
                    case Slot::PLAYER_MISSED:
                    case Slot::SLOT_IS_EMPTY:
                        $battleField[$i][$j] = self::PLAYER_MISSED;
                        break;
                    case Slot::SHIP_WAS_HIT:
                        $battleField[$i][$j] = self::SHIP_IS_HIT;
                        break;
                    case Slot::SHIP_IS_DEAD:
                        $battleField[$i][$j] = self::SHIP_IS_DEAD;
                        break;
                }
            }
        }

        return $this->shooter->invoke($battleField);
    }

    public function __toString()
    {
        return 'Adapter';
    }
}