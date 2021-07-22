<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


class MediumAI implements InterfaceAI
{
    public const HORIZONTAL = "Hit ship has horizontal direction";
    public const VERTICAL = "Hit ship has vertical direction";
    public const UNKNOWN = "Hit ship has unknown direction";

    protected array $coordsOfHitShip = [];
    protected string $directionOfHitShip = self::UNKNOWN;

    protected ShootingBoard $shootingBoard;
    protected array $previousShootingCoords = [];

    protected array $valuesForSquares = [];


    public function getCoordsForShooting(ShootingBoard $shootingBoard): array
    {
        $this->shootingBoard = $shootingBoard;

        $this->writeResultOfPreviousShooting();

        $this->setValuesForSquares();

        if (empty($this->coordsOfHitShip)) {
            $allPossibleCoords = $this->getAllPossibleCoordsForRandomShooting();
        } else {
            $allPossibleCoords = $this->getAllPossibleCoordsOfHitShip();
        }

        ksort($allPossibleCoords);

        $bestShootingCoords = array_pop($allPossibleCoords);
        shuffle($bestShootingCoords);

        $coords = array_pop($bestShootingCoords);

        $this->previousShootingCoords = $coords;

        return $coords;
    }


    /**
     *  When we shot previously, we only picked coords [x, y] for that shot,
     *  but we didn't know, what result would be.
     *  Now we know the result: either "hit", or "killed" or "missed".
     *  So our AI has to synchronize with that result
     */
    protected function writeResultOfPreviousShooting(): void
    {
        if (empty($this->previousShootingCoords)) {
            return;
        }

        [$previousX, $previousY] = $this->previousShootingCoords;

        $currentStateOfSquare = $this->shootingBoard->getSquare($previousX, $previousY)->getState();

        switch ($currentStateOfSquare) {
            case Square::HIT_SHIP:
                $this->coordsOfHitShip[] = $this->previousShootingCoords;
                if (count($this->coordsOfHitShip) === 2) {
                    $this->directionOfHitShip = $this->defineDirectionOfHitShip();
                }
                break;
            case Square::KILLED_SHIP:
                $this->coordsOfHitShip = [];
                $this->directionOfHitShip = self::UNKNOWN;
                break;
        }
    }


    protected function defineDirectionOfHitShip(): string
    {
        [$firstPartOfHitShip, $secondPartOfHitShip] = $this->coordsOfHitShip;

        [$x1, $y1] = $firstPartOfHitShip;
        [$x2, $y2] = $secondPartOfHitShip;

        if ($x1 !== $x2) {
            return self::HORIZONTAL;
        }

        return self::VERTICAL;
    }


    protected function setValuesForSquares(): void
    {
        $this->valuesForSquares = [];

        for ($i = 0; $i < ShootingBoard::WIDTH; $i++) {
            for ($j = 0; $j < ShootingBoard::HEIGHT; $j++) {
                $this->valuesForSquares[$i][$j] = 0;

                $squareState = $this->shootingBoard->getSquare($i, $j)->getState();
                if ($squareState !== Square::EMPTY) {
                    continue;
                }

                $leftNotMissedSquares = $this->getAmountOfLeftNotMissedSquares($i, $j);
                $rightNotMissedSquares = $this->getAmountOfRightNotMissedSquares($i, $j);
                $topNotMissedSquares = $this->getAmountOfTopNotMissedSquares($i, $j);
                $bottomNotMissedSquares = $this->getAmountOfBottomNotMissedSquares($i, $j);

                $horizontalNotMissedSquares = $leftNotMissedSquares + $rightNotMissedSquares + 1;
                $verticalNotMissedSquares = $topNotMissedSquares + $bottomNotMissedSquares + 1;

                foreach ($this->shootingBoard->getAliveShips() as $shipSize) {
                    if ($shipSize <= $horizontalNotMissedSquares) {
                        $this->valuesForSquares[$i][$j] += $shipSize;
                    }

                    if ($shipSize <= $verticalNotMissedSquares) {
                        $this->valuesForSquares[$i][$j] += $shipSize;
                    }
                }

                if ($leftNotMissedSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }

                if ($rightNotMissedSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }

                if ($topNotMissedSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }

                if ($bottomNotMissedSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }
            }
        }
    }


    protected function getAllPossibleCoordsOfHitShip(): array
    {
        $result = [];

        $coordsOfAllPossibleSquares = match ($this->directionOfHitShip) {
            self::HORIZONTAL => $this->getAllPossibleHorizontalCoordsOfHitShip(),
            self::VERTICAL => $this->getAllPossibleVerticalCoordsOfHitShip(),
            self::UNKNOWN => $this->getAllPossibleBidirectionalCoordsOfHitShip(),
        };

        foreach ($coordsOfAllPossibleSquares as $squareCoords) {
            [$x, $y] = $squareCoords;
            $squareValue = $this->valuesForSquares[$x][$y];

            $result[$squareValue][] = $squareCoords;
        }

        return $result;
    }


    protected function getAllPossibleHorizontalCoordsOfHitShip(): array
    {
        $allCoords = [];

        foreach ($this->coordsOfHitShip as [$hitShipX, $hitShipY]) {
            $leftSquare = $this->shootingBoard->getSquare($hitShipX - 1, $hitShipY);
            if ($leftSquare?->getState() === Square::EMPTY) {
                $allCoords[] = $leftSquare->getCoords();
            }

            $rightSquare = $this->shootingBoard->getSquare($hitShipX + 1, $hitShipY);
            if ($rightSquare?->getState() === Square::EMPTY) {
                $allCoords[] = $rightSquare->getCoords();
            }
        }

        return $allCoords;
    }


    protected function getAllPossibleVerticalCoordsOfHitShip(): array
    {
        $allCoords = [];

        foreach ($this->coordsOfHitShip as [$hitShipX, $hitShipY]) {
            $topSquare = $this->shootingBoard->getSquare($hitShipX, $hitShipY - 1);
            if ($topSquare?->getState() === Square::EMPTY) {
                $allCoords[] = $topSquare->getCoords();
            }

            $bottomSquare = $this->shootingBoard->getSquare($hitShipX, $hitShipY + 1);
            if ($bottomSquare?->getState() === Square::EMPTY) {
                $allCoords[] = $bottomSquare->getCoords();
            }
        }

        return $allCoords;
    }


    protected function getAllPossibleBidirectionalCoordsOfHitShip(): array
    {
        $horizontalCoords = $this->getAllPossibleHorizontalCoordsOfHitShip();
        $verticalCoords = $this->getAllPossibleVerticalCoordsOfHitShip();

        return array_merge($horizontalCoords, $verticalCoords);
    }


    protected function getAmountOfLeftNotMissedSquares(int $x, int $y): int
    {
        $amount = 0;

        for ($i = $x - 1; $i >= 0; $i--) {
            $squareState = $this->shootingBoard->getSquare($i, $y)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $amount++;
        }

        return $amount;
    }


    protected function getAmountOfRightNotMissedSquares(int $x, int $y): int
    {
        $amount = 0;

        for ($i = $x + 1; $i < ShootingBoard::WIDTH; $i++) {
            $squareState = $this->shootingBoard->getSquare($i, $y)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $amount++;
        }

        return $amount;
    }


    protected function getAmountOfTopNotMissedSquares(int $x, int $y): int
    {
        $amount = 0;

        for ($j = $y - 1; $j >= 0; $j--) {
            $squareState = $this->shootingBoard->getSquare($x, $j)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $amount++;
        }

        return $amount;
    }


    protected function getAmountOfBottomNotMissedSquares(int $x, int $y): int
    {
        $amount = 0;

        for ($j = $y + 1; $j < ShootingBoard::HEIGHT; $j++) {
            $squareState = $this->shootingBoard->getSquare($x, $j)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $amount++;
        }

        return $amount;
    }


    protected function getAllPossibleCoordsForRandomShooting(): array
    {
        $allCoords = [];

        for ($i = 0; $i < ShootingBoard::WIDTH; $i++) {
            for ($j = 0; $j < ShootingBoard::HEIGHT; $j++) {
                $squareState = $this->shootingBoard->getSquare($i, $j)->getState();

                if ($squareState === Square::EMPTY) {
                    $squareValue = $this->valuesForSquares[$i][$j];

                    $allCoords[$squareValue][] = [$i, $j];
                }
            }
        }

        return $allCoords;
    }


    public function reset(): void
    {
        $this->valuesForSquares = [];
        $this->previousShootingCoords = [];

        $this->coordsOfHitShip = [];
        $this->directionOfHitShip = self::UNKNOWN;
    }


    protected function shuffle_assoc(&$array): bool
    {
        $new = [];

        $keys = array_keys($array);
        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return true;
    }
}
