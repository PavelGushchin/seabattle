<?php

namespace SeaBattle\AI;

interface IShootingAI
{
    public function calculateCoordsForShooting($slots, $ships = null);
}