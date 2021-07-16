<?php

namespace SeaBattle\Board;


class Cell
{
    const EMPTY = 0;
    const SHIP = 1;
    const SHIP_HIT = 2;
    const SHIP_KILLED = 3;
    const PLAYER_MISSED = 4;

    protected int $state = self::EMPTY;

    /** Unique identifier of a ship that possibly placed in this cell **/
    protected ?int $shipId = null;


    public function getState(): int
    {
        return $this->state;
    }


    public function setState(int $state): void
    {
        $this->state = $state;
    }


    public function getShipId(): ?int
    {
        return $this->shipId;
    }


    public function setShipId(int $shipId): void
    {
        $this->shipId = $shipId;
    }
}
