<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\Field;

/**
 * Slot represents a cell of a battle field.
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class Slot
{
    /**
     * @var int Slot is hidden from player
     */
    const SLOT_IS_UNCOVERED = 1;

    /**
     * @var int Slot is empty for sure because beside it is a ship
     */
    const SLOT_IS_EMPTY = 2;

    /**
     * @var int Player shot to slot but it was empty
     */
    const PLAYER_MISSED = 3;

    /**
     * @var int Slot contains a ship
     */
    const THERE_IS_A_SHIP = 4;

    /**
     * @var int Player shot to slot and there was a ship
     */
    const SHIP_WAS_HIT = 5;

    /**
     * @var int Slot contains already dead ship
     */
    const SHIP_IS_DEAD = 6;

    /**
     * @var int Current state of the slot
     */
    protected $state = self::SLOT_IS_UNCOVERED;

    /**
     * @var int|null Unique identifier of a ship which placed in the slot
     */
    protected $shipId = null;

    /**
     * Returns current state of the slot
     *
     * @return int Slot's state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets state to the slot
     *
     * @param int $state Slot's state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Returns id of a ship that located in the slot
     *
     * @return int|null Ship's id
     */
    public function getShipId()
    {
        return $this->shipId;
    }

    /**
     * Sets for the slot id of a ship
     *
     * @param int $shipId Ship's id
     */
    public function setShipId($shipId)
    {
        $this->shipId = $shipId;
    }
}
