<?php

namespace SeaBattle\AI;

use SeaBattle\Field\Slot;

class SmartShootingAI implements IShootingAI
{
    private $partsOfdamagedShip = [];
    private $smellBlood = false;
    private $variantsForNextShot = [];

    public function calculateCoordsForShooting($slots)
    {

    }
}