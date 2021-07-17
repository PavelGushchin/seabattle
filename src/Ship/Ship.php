<?php

namespace SeaBattle\Ship;


class Ship
{
    public const HORIZONTAL = 0;
    public const VERTICAL = 1;

    protected int $size;
    protected int $direction;
    protected array $parts = [];
    protected int $numberOfKilledParts = 0;


    public function __construct(int $size, int $direction, array $coordsOfShipHead)
    {
        [$x, $y] = $coordsOfShipHead;

        $this->parts[] = [
            "x" => $x,
            "y" => $y,
            "isKilled" => false,
        ];

        if ($direction === self::HORIZONTAL) {
            $dx =  1;
            $dy = 0;
        } else {
            $dx =  0;
            $dy = 1;
        }


        for ($i = 1; $i < $size; $i++) {

        }

        $this->size = $size;
        $this->direction = $direction;
    }


    public function checkForDeath(): bool
    {
        if ($this->size <= $this->hits) {
            $this->isDead = true;

            return true;
        }

        return false;
    }


    public function getParts(): array
    {
        return $this->parts;
    }


    public function setParts(array $parts): void
    {
        $this->parts = $parts;
    }




    public function getDirection(): int
    {
        return $this->direction;
    }


    public function setDirection(int $direction): self
    {
        $this->direction = $direction;

        return $this;
    }





    public function getSize(): int
    {
        return $this->size;
    }


    public function getHits(): int
    {
        return $this->hits;
    }


    public function addHit(): self
    {
        $this->hits++;

        return $this;
    }


    public function isDead(): bool
    {
        return $this->isDead;
    }
}
