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
        $aliveShips = $this->shootingBoard->getAliveShips();
        rsort($aliveShips);

        return $aliveShips[0];
    }


    protected function tryToFindShipWithMaxSize(int $maxShipSize): array
    {
        $areas = $this->splitBoardToSquareAreas($maxShipSize);
        shuffle($areas);

        foreach ($areas as $area) {
            $lines = $this->splitAreaToLines($area);

            if (empty($lines)) {
                continue;
            }

            shuffle($lines);

            $line = array_pop($lines);
            shuffle($line);

            $coords = array_pop($line);

            return [$maxShipSize => [$coords]];
        }

        throw new \Exception("We should never reach this line!");
    }


    protected function splitBoardToSquareAreas(int $size): array
    {
        $areas = [];

        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $areas[] = $this->split($x, $y, $size);
            }
        }

        return array_merge(...$areas);
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
