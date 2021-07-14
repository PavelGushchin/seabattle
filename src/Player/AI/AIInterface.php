<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;


/**
 * ShootingAIInterface is supposed to represent 'brains' of CPU.
 *
 * It is just an interface so if you want your CPU to play in a smart way
 * you have to implement it manually.
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
interface AIInterface
{
    /**
     * The main goal of this method is to return an array
     * which contains coordinates for next shooting
     *
     * Return statement of this method should be:
     *  return [
     *      'x' => $x,
     *      'y' => $y,
     *  ]
     */
    public function calculateCoordsForShooting(array $cells, ShootingBoard $boardForShooting): array;
}
