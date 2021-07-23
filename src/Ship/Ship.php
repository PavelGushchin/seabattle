<?php

namespace SeaBattle\Ship;

use SeaBattle\Board\AbstractBoard;


class Ship
{
    public const HORIZONTAL = 0;
    public const VERTICAL = 1;

    protected int $size;
    protected int $direction;

    protected array $headCoords;
    protected array $tailCoords;

    /** It shows how many times the ship has been hit by opponent. */
    protected int $numberOfHits = 0;


    /**
     * @throws \Exception if ship's direction is neither HORIZONTAL, nor VERTICAL
     */
    public function __construct(int $size, int $direction, array $headCoords)
    {
        $this->size = $size;
        $this->direction = $direction;
        $this->headCoords = $headCoords;


        [$headX, $headY] = $headCoords;

        /** Calculating coords of ship's tail */
        if ($direction === self::HORIZONTAL) {
            $tailX = $headX + $size - 1;
            $tailY = $headY;
        } elseif ($direction === self::VERTICAL) {
            $tailX = $headX;
            $tailY = $headY + $size - 1;
        } else {
            throw new \Exception("Unknown direction!");
        }

        $this->tailCoords = [$tailX, $tailY];
    }


    public function addHit(): void
    {
        $this->numberOfHits++;
    }


    public function isKilled(): bool
    {
        return $this->numberOfHits === $this->size;
    }


    /**
     * Usages of that function:
     *
     * 1) When we create ship we have to be sure that area around
     * ship is empty
     * 2) When we kill ship we have to mark all squares around the
     * ship, so player couldn't shoot to them
     */
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


    public function getHeadCoords(): array
    {
        return $this->headCoords;
    }


    public function getTailCoords(): array
    {
        return $this->tailCoords;
    }
}
