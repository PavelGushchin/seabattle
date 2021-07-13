<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\Player\AI;

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
     *
     * @param array      $slots Array of slots
     * @param array|null $ships Array of ships
     *
     * @return array Array of shot coordinates
     */
    public function calculateCoordsForShooting($slots, $ships = null);
}
