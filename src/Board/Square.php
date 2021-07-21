<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class Square
{
    public const EMPTY = 1;
    public const MISSED = 2;
    public const SHIP = 3;
    public const HIT_SHIP = 4;
    public const KILLED_SHIP = 5;

    protected int $state = self::EMPTY;

    /** Ship that is possibly located in this square */
    protected ?Ship $ship = null;

    /** Coords on Board */
    protected int $x, $y;


    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
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
}
