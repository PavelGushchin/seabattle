<?php

namespace SeaBattle\Ship;

use SeaBattle\Board\AbstractBoard;


class Ship
{
    public const HORIZONTAL = 1;
    public const VERTICAL = 2;

    protected int $size;
    protected int $direction;

    protected array $headCoords;
    protected array $tailCoords;

    /** It shows how many times the ship has been hit by our opponent. */
    protected int $numberOfHits = 0;


    public function __construct(int $size, int $direction, array $headCoords)
    {
        $this->size = $size;
        $this->direction = $direction;
        $this->headCoords = $headCoords;

        [$x, $y] = $headCoords;

        if ($direction === self::HORIZONTAL) {
            $x += $size - 1;
        } elseif ($direction === self::VERTICAL) {
            $y += $size - 1;
        }

        $this->tailCoords = [$x, $y];
    }


    public function addHit(): void
    {
        if ($this->numberOfHits < $this->size) {
            $this->numberOfHits++;
        }
    }


    public function isKilled(): bool
    {
        return $this->numberOfHits === $this->size;
    }


    public function getCoordsOfAreaAroundShip(): array
    {
        [$headShipX, $headShipY] = $this->headCoords;
        [$tailShipX, $tailShipY] = $this->tailCoords;

        $areaStartX = $headShipX - 1;
        $areaStartY = $headShipY - 1;
        $areaEndX = $tailShipX + 1;
        $areaEndY = $tailShipY + 1;

        if ($areaStartX < 0) {
            $areaStartX = 0;
        }

        if ($areaStartY < 0) {
            $areaStartY = 0;
        }

        if ($areaEndX > AbstractBoard::WIDTH - 1) {
            $areaEndX = AbstractBoard::WIDTH - 1;
        }

        if ($areaEndY > AbstractBoard::HEIGHT - 1) {
            $areaEndY = AbstractBoard::HEIGHT - 1;
        }

        return [$areaStartX, $areaStartY, $areaEndX, $areaEndY];
    }


    public function getSize(): int
    {
        return $this->size;
    }


    public function setSize(int $size): void
    {
        $this->size = $size;
    }


    public function getDirection(): int
    {
        return $this->direction;
    }


    public function setDirection(int $direction): void
    {
        $this->direction = $direction;
    }


    public function getHeadCoords(): array
    {
        return $this->headCoords;
    }


    public function setHeadCoords(array $headCoords): void
    {
        $this->headCoords = $headCoords;
    }

    public function getTailCoords(): array
    {
        return $this->tailCoords;
    }


    public function setTailCoords(array $tailCoords): void
    {
        $this->tailCoords = $tailCoords;
    }


    public function getNumberOfHits(): int
    {
        return $this->numberOfHits;
    }


    public function setNumberOfHits(int $numberOfHits): void
    {
        $this->numberOfHits = $numberOfHits;
    }
}
