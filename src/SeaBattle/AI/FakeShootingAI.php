<?php

namespace SeaBattle\AI;


class FakeShootingAI implements IShootingAI
{
    public function calculateCoordsForShooting($slots, $ships = null)
    {
        return [
            'x' => 0,
            'y' => 0
        ];
    }

    public function __toString()
    {
        return 'Fake algorithm';
    }
}