<?php

namespace SeaBattle\Board;


class Cell
{
    const NONE = 0;
    const SHIP = 1;
    const HIT_SHIP = 2;
    const DEAD_SHIP = 3;
    const MISSED = 4;


    protected int $state = self::NONE;

    /** Unique identifier of a ship that possibly placed in this cell **/
    protected ?int $shipId = null;


    public function getState(): int
    {
        return $this->state;
    }


    public function setState(int $state)
    {
        $this->state = $state;
    }


    public function getShipId(): ?int
    {
        return $this->shipId;
    }


    public function setShipId(int $shipId)
    {
        $this->shipId = $shipId;
    }
}
