<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class Square
{
    public const EMPTY = 0;
    public const SHIP = 1;
    public const HIT_SHIP = 2;
    public const KILLED_SHIP = 3;
    public const MISSED = 4;

    protected int $x;
    protected int $y;
    protected int $state = self::EMPTY;
    protected ?Ship $ship = null;


    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }


    public function getX(): int
    {
        return $this->x;
    }


    public function getY(): int
    {
        return $this->y;
    }


    public function getCoords(): array
    {
        return [$this->x, $this->y];
    }


    public function getState(): int
    {
        return $this->state;
    }


    public function setState(string $state): void
    {
        $this->state = $state;
    }


    public function getShip(): ?Ship
    {
        return $this->ship;
    }


    public function setShip(Ship $ship): void
    {
        $this->ship = $ship;
    }
}
