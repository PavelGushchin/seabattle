<?php

namespace SeaBattle\AI;


class MyCustomAI implements ISeabattleAI
{
    public function invoke($battleField)
    {
        do {
            $x = mt_rand(0, 9);
            $y = mt_rand(0, 9);
        } while ($battleField[$x][$y] !== self::CELL_IS_HIDDEN);

        return [
            'x' => $x,
            'y' => $y
        ];
    }
}