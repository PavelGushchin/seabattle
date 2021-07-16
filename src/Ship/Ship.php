<?php

namespace SeaBattle\Ship;


class Ship
{
    const HORIZONTAL = 0;
    const VERTICAL = 1;

    protected int $size;
    protected array $parts = [];
    protected ?int $direction = null;


    protected bool $isDead = false;
    protected int $hits = 0;


    public function __construct(int $size)
    {
        $this->size = $size;
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


    public function Parts(array $parts): void
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
