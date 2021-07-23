<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


class Square
{
    public const EMPTY = 0;
    public const MISSED = 1;
    public const SHIP = 2;
    public const HIT_SHIP = 3;
    public const KILLED_SHIP = 4;

    protected int $state = self::EMPTY;

    /** Ship that is possibly located in this square */
    protected ?Ship $ship = null;

    /** Coords of that Square on Board */
    protected array $coords;


    public function __construct(int $x, int $y)
    {
        $this->coords = [$x, $y];
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


    public function getCoords(): array
    {
        return $this->coords;
    }
}
