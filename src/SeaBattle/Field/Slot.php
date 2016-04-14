<?php

namespace SeaBattle\Field;


class Slot
{
    const SLOT_IS_EMPTY = 0;
    const PLAYER_MISSED = 1;
    const THERE_IS_A_SHIP = 2;
    const SHIP_WAS_HIT = 3;

    private $state = self::SLOT_IS_EMPTY;
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