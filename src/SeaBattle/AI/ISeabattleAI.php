<?php

namespace SeaBattle\AI;


interface ISeabattleAI
{
    const CELL_IS_HIDDEN = 0;
    const PLAYER_MISSED = 1;
    const SHIP_IS_HIT = 2;
    const SHIP_IS_DEAD = 3;

    /**
     * @param array $battleField Array which contains current state of shots to enemy's battle field
     * @return array It has to contain coords for next shot, for example, ['x' =>  4, 'y' => 2]
     */
    public function invoke ($battleField);
}