<?php

namespace SeaBattle\AI;

use SeaBattle\Field\Field;

interface IShootingAI
{
    public function calculateCoordsForShooting(Field $attackedField);
}