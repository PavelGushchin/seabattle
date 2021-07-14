<?php

namespace SeaBattle\Board;


class Cell
{
    const EMPTY = "Cell has 'empty' state";
    const PLAYER_MISSED = "Cell has 'missed' state";
    const SHIP = "Cell has 'ship' state";
    const SHIP_IS_HIT = "Cell has 'hit' state";
    const SHIP_IS_DEAD = "Cell has 'dead' state";

    protected string $state = self::EMPTY;

    /**
     * Unique identifier of a ship which possibly placed in the cell
     */
    protected ?int $shipId = null;


    public function getState()
    {
        return $this->state;
    }


    public function setState($state)
    {
        $this->state = $state;
    }


    public function getShipId()
    {
        return $this->shipId;
    }


    public function setShipId($shipId)
    {
        $this->shipId = $shipId;
    }
}
