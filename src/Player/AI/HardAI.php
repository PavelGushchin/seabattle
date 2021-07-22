<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


class HardAI extends MediumAI
{
    protected VeryEasyAI $randomShooter;


    public function __construct()
    {
        $this->randomShooter = new VeryEasyAI();
    }


    protected function getAllPossibleCoordsForRandomShooting(): array
    {
        $maxShipSize = $this->getMaxSizeOfAliveShips();

        if ($maxShipSize === 1) {
            $coords[$maxShipSize][] = $this->randomShooter->getCoordsForShooting($this->shootingBoard);

            return $coords;
        }

        return $this->tryToFindShipWithMaxSize($maxShipSize);
    }


    protected function getMaxSizeOfAliveShips(): int
    {
        // TODO: check it
        $aliveShips = $this->shootingBoard->getAliveShips();
        rsort($aliveShips);

        return $aliveShips[0];
    }


    protected function tryToFindShipWithMaxSize(int $maxShipSize): array
    {
        $areas = $this->splitBoardToSquareAreas($maxShipSize);
        shuffle($areas);

        $allCoords = [];
        foreach ($areas as $area) {
            $lines = $this->splitAreaToLines($area);

            foreach ($lines as $line) {
                foreach ($line as $coords) {
                    [$x, $y] = $coords;
                    $allCoords["$x$y"] = $coords;
                }
            }
        }

        $result = [];
        foreach ($allCoords as $coords) {
            [$x, $y] = $coords;
            $value = $this->valuesForSquares[$x][$y];
            $result[$value][] = [$x, $y];
        }

        return $result;
    }


    protected function splitBoardToSquareAreas(int $size): array
    {
        $offsetX = ShootingBoard::WIDTH % $size;
        $offsetY = ShootingBoard::HEIGHT % $size;

        if ($offsetX === 0 && $offsetY === 0) {
            $areas = $this->split(0, 0, $size);
        } elseif ($offsetX === 0 && $offsetY !== 0) {
            $areas = array_merge(
                $this->split(0, 0, $size),
                $this->split(0, $offsetY, $size),
            );
        } elseif ($offsetX !== 0 && $offsetY === 0) {
            $areas = array_merge(
                $this->split(0, 0, $size),
                $this->split($offsetX, 0, $size),
            );
        } else {
            $areas = array_merge(
                $this->split(0, 0, $size),
                $this->split($offsetX, 0, $size),
                $this->split(0, $offsetY, $size),
                $this->split($offsetX, $offsetY, $size),
            );
        }

        return $areas;
    }


    protected function split(int $startX, int $startY, int $size): array
    {
        $areas = [];

        for ($x = $startX; $x + $size <= ShootingBoard::WIDTH; $x += $size) {
            for ($y = $startY; $y + $size <= ShootingBoard::HEIGHT; $y += $size) {
                $areas[] = [
                    [$x, $y],
                    [$x + $size - 1, $y + $size - 1]
                ];
            }
        }

        return $areas;
    }


    protected function splitAreaToLines(array $area): ?array
    {
        [$areaStartCoords, $areaEndCoords] = $area;

        [$startX, $startY] = $areaStartCoords;
        [$endX, $endY] = $areaEndCoords;

        return array_merge(
            $this->splitAreaToHorizontalLines($startX, $endX, $startY, $endY),
            $this->splitAreaToVerticalLines($startX, $endX, $startY, $endY),
        );
    }


    protected function splitAreaToHorizontalLines(int $startX, int $endX, int $startY, int $endY): array
    {
        $lines = [];

        for ($x = $startX; $x <= $endX; $x++) {
            $line = [];
            for ($y = $startY; $y <= $endY; $y++) {
                $square = $this->shootingBoard->getSquare($x, $y);
                if ($square->getState() !== Square::EMPTY) {
                    break;
                }

                $line[] = [$x, $y];
            }

            if (count($line) === $endX - $startX + 1) {
                $lines[] = $line;
            }
        }

        return $lines;
    }


    protected function splitAreaToVerticalLines(int $startX, int $endX, int $startY, int $endY): array
    {
        $lines = [];

        for ($y = $startY; $y <= $endY; $y++) {
            $line = [];

            for ($x = $startX; $x <= $endX; $x++) {
                $square = $this->shootingBoard->getSquare($x, $y);
                if ($square->getState() !== Square::EMPTY) {
                    break;
                }

                $line[] = [$x, $y];
            }

            if (count($line) === $endY - $startY + 1) {
                $lines[] = $line;
            }
        }

        return $lines;
    }
}
