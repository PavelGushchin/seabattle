<?php

namespace SeaBattle\Board\Cell;


class Cell extends AbstractCell
{
    const NONE = 0;
    const SHIP = 1;
    const HIT_SHIP = 2;
    const DEAD_SHIP = 3;
    const MISSED = 4;


    protected $state = self::NONE;

    /** Unique identifier of a ship that possibly placed in this cell **/
    protected ?int $shipId = null;


    public function getState()
    {
        return $this->state;
    }


    public function setState($state)
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
