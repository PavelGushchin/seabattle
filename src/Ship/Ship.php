<?php

namespace SeaBattle\Ship;


class Ship
{
    public const HORIZONTAL = 0;
    public const VERTICAL = 1;

    protected int $size;
    protected int $direction;
    protected array $parts = [];
    protected array $squares = [];
    protected int $numberOfKilledParts = 0;
    protected int $numberOfHits = 0;


    public function __construct(int $size, int $direction, array $startCoords)
    {
        [$x, $y] = $startCoords;

        $this->parts[] = [$x, $y];

        for ($i = 1; $i < $size; $i++) {
            if ($direction === self::HORIZONTAL) {
                $x++;
            } elseif ($direction === self::VERTICAL) {
                $y++;
            }

            $this->parts[] = [$x, $y];
        }

        $this->size = $size;
        $this->direction = $direction;
    }


    /**
     * @throws \Exception if direction of ship is unknown
     */
    public static function calculateEndCoords(int $size, int $direction, array $startCoords): array
    {
        [$startX, $startY] = $startCoords;

        switch ($direction) {
            case self::HORIZONTAL:
                $endX = $startX + $size - 1;
                break;
            case self::VERTICAL:
                $endY = $startY + $size - 1;
                break;
            default:
                throw new \Exception("Unknown ship's direction!");
        }

        return [$endX, $endY];
    }


    public function getParts(): array
    {
        return $this->parts;
    }


    public function getDirection(): int
    {
        return $this->direction;
    }


    public function getSize(): int
    {
        return $this->size;
    }


    public function addHit(): self
    {
        $this->numberOfHits++;

//        foreach ($this->parts as $part) {
//            if ($part["x"] === $x && $part["y"] === $y) {
//
//            }
//        }
//
//        $this->numberOfKilledParts++;

        return $this;
    }


    public function checkIsKilled(): bool
    {
        return $this->numberOfHits === $this->size;
    }

    /**
     * @return array
     */
    public function getSquares(): array
    {
        return $this->squares;
    }

    /**
     * @param array $squares
     */
    public function setSquares(array $squares): void
    {
        $this->squares = $squares;
    }


}
