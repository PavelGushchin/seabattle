<?php

namespace SeaBattle\Field;


class Slot
{
    const SLOT_IS_UNCOVERED = 0;
    const SLOT_IS_EMPTY = 1;
    const PLAYER_MISSED = 2;
    const THERE_IS_A_SHIP = 3;
    const SHIP_WAS_HIT = 4;

    private $state = self::SLOT_IS_UNCOVERED;
    private $shipId = 0;

    public function setShipId($shipId)
    {
        $this->shipId = $shipId;
    }

    public function getShipId()
    {
        return $this->shipId;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}