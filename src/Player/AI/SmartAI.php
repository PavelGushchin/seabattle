<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


/**
 * The main idea of this algorithm is that to each Square on Board
 * will be assigned its own value and we'll have to shoot to Squares
 * with the highest values first
 */
class SmartAI implements InterfaceAI
{
    public const HORIZONTAL = "Hit ship has horizontal direction";
    public const VERTICAL = "Hit ship has vertical direction";
    public const UNKNOWN = "Hit ship has unknown direction";

    protected ShootingBoard $shootingBoard;

    /**
     * Array with coordinates of damaged ship's parts
     */
    protected array $partsOfDamagedShip = [];
    protected string $directionOfHitShip = self::UNKNOWN;

    /**
     * Saved coordinates of previous shot
     */
    protected array $previousShootingCoords = [];

    /**
     * Array with values for squares
     */
    protected array $valuesForSquares = [];


    /**
     * The main goal of this method is to return an
     * array with coordinates for the next shot
     */
    public function getCoordsForShooting(ShootingBoard $shootingBoard): array
    {
        $this->shootingBoard = $shootingBoard;

        $this->writeResultOfPreviousShooting();

        $this->setValuesForSquares();

        if (empty($this->partsOfDamagedShip)) {
            /** We don't have a hit ship on Board, so we'll shoot by values of Squares */
            $optionsForNextShot = $this->getCoordsByValues();
        } else {
            /** We have a hit ship on Board, so we have to find it and kill */
            $optionsForNextShot = $this->getCoordsForKillingHitShip();
        }

        ksort($optionsForNextShot);

        /**
         * Extracting options with the biggest values (the best options)
         */
        $bestOptions = array_pop($optionsForNextShot);
        shuffle($bestOptions);

        /**
         * Finally we get coords for next shot
         */
        $coords = array_pop($bestOptions);

        $this->previousShootingCoords = $coords;

        return $coords;
    }


    /**
     *  When we shot previously, we only picked coords [x, y] for that shot,
     *  but we didn't know, what result would be.
     *
     *  But now when we know the result (either "hit", or "killed", or "missed")
     *  we have to write that result down
     */
    protected function writeResultOfPreviousShooting(): void
    {
        if (empty($this->previousShootingCoords)) {
            return;
        }

        [$previousX, $previousY] = $this->previousShootingCoords;

        $previouslyAttackedSquare = $this->shootingBoard->getSquare($previousX, $previousY);

        switch ($previouslyAttackedSquare->getState()) {
            case Square::HIT_SHIP:
                /**
                 * Result of previous shooting is - we hit ship, so we have to
                 * add these coordinates to array which contains damaged ship parts
                 */
                $this->partsOfDamagedShip[] = [$previousX, $previousY];

                /**
                 * If we have 2 parts of hit ship then we can determine it's direction
                 */
                if (count($this->partsOfDamagedShip) === 2) {
                    $this->directionOfHitShip = $this->determineDirectionOfHitShip();
                }
                break;
            case Square::KILLED_SHIP:
                $this->partsOfDamagedShip = [];
                $this->directionOfHitShip = self::UNKNOWN;
                break;
        }
    }


    protected function determineDirectionOfHitShip(): string
    {
        [$firstPartOfHitShip, $secondPartOfHitShip] = $this->partsOfDamagedShip;

        [$x1, $y1] = $firstPartOfHitShip;
        [$x2, $y2] = $secondPartOfHitShip;

        if ($x1 !== $x2) {
            return self::HORIZONTAL;
        }

        return self::VERTICAL;
    }


    /**
     * This method assigns values to all Squares.
     *
     * The higher value of Square is, the sooner it will be shot.
     */
    protected function setValuesForSquares(): void
    {
        $this->valuesForSquares = [];

        for ($i = 0; $i < ShootingBoard::WIDTH; $i++) {
            for ($j = 0; $j < ShootingBoard::HEIGHT; $j++) {
                $this->valuesForSquares[$i][$j] = 0;

                $currentSquare = $this->shootingBoard->getSquare($i, $j);
                if ($currentSquare->getState() !== Square::EMPTY) {
                    continue;
                }

                $leftSquares = $this->getNumberOfLeftSquares($i, $j);
                $rightSquares = $this->getNumberOfRightSquares($i, $j);
                $topSquares = $this->getNumberOfTopSquares($i, $j);
                $bottomSquares = $this->getNumberOfBottomSquares($i, $j);

                $horizontalSquares = $leftSquares + $rightSquares + 1;
                $verticalSquares = $topSquares + $bottomSquares + 1;

                foreach ($this->shootingBoard->getAliveShips() as $shipSize) {
                    if ($shipSize <= $horizontalSquares) {
                        $this->valuesForSquares[$i][$j] += $shipSize;
                    }

                    if ($shipSize <= $verticalSquares) {
                        $this->valuesForSquares[$i][$j] += $shipSize;
                    }
                }

                if ($leftSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }

                if ($rightSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }

                if ($topSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }

                if ($bottomSquares > 0) {
                    $this->valuesForSquares[$i][$j]++;
                }
            }
        }
    }


    protected function getNumberOfLeftSquares(int $x, int $y): int
    {
        $number = 0;

        for ($i = $x - 1; $i >= 0; $i--) {
            $squareState = $this->shootingBoard->getSquare($i, $y)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $number++;
        }

        return $number;
    }


    protected function getNumberOfRightSquares(int $x, int $y): int
    {
        $number = 0;

        for ($i = $x + 1; $i < ShootingBoard::WIDTH; $i++) {
            $squareState = $this->shootingBoard->getSquare($i, $y)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $number++;
        }

        return $number;
    }


    protected function getNumberOfTopSquares(int $x, int $y): int
    {
        $number = 0;

        for ($j = $y - 1; $j >= 0; $j--) {
            $squareState = $this->shootingBoard->getSquare($x, $j)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $number++;
        }

        return $number;
    }


    protected function getNumberOfBottomSquares(int $x, int $y): int
    {
        $number = 0;

        for ($j = $y + 1; $j < ShootingBoard::HEIGHT; $j++) {
            $squareState = $this->shootingBoard->getSquare($x, $j)->getState();

            if ($squareState === Square::MISSED) {
                break;
            }

            $number++;
        }

        return $number;
    }


    /**
     * If we don't have any hit ships on Board, then we'll
     * choose coords for next shot by values of Squares
     */
    protected function getCoordsByValues(): array
    {
        $optionsForNextShot = [];

        for ($i = 0; $i < ShootingBoard::WIDTH; $i++) {
            for ($j = 0; $j < ShootingBoard::HEIGHT; $j++) {

                $currentSquare = $this->shootingBoard->getSquare($i, $j);

                if ($currentSquare->getState() === Square::EMPTY) {
                    $squareValue = $this->valuesForSquares[$i][$j];

                    $optionsForNextShot[$squareValue][] = [$i, $j];
                }
            }
        }

        return $optionsForNextShot;
    }

    /**
     * When we do have a hit ship on Board then
     * we have to find it and kill
     */
    protected function getCoordsForKillingHitShip(): array
    {
        $optionsForNextShot = [];

        $allCoords = match ($this->directionOfHitShip) {
            self::HORIZONTAL => $this->getHorizontalOptions(),
            self::VERTICAL => $this->getVerticalOptions(),
            self::UNKNOWN => $this->getOptionsForBothDirections(),
        };

        foreach ($allCoords as $coords) {
            [$x, $y] = $coords;

            $coordsValue = $this->valuesForSquares[$x][$y];

            $optionsForNextShot[$coordsValue][] = $coords;
        }

        return $optionsForNextShot;
    }


    /**
     * It returns all possible coordinates for shooting in horizontal direction
     */
    protected function getHorizontalOptions(): array
    {
        $options = [];

        foreach ($this->partsOfDamagedShip as $shipPart) {
            [$hitShipX, $hitShipY] = $shipPart;

            $leftSquare = $this->shootingBoard->getSquare($hitShipX - 1, $hitShipY);
            if ($leftSquare?->getState() === Square::EMPTY) {
                $options[] = $leftSquare->getCoords();
            }

            $rightSquare = $this->shootingBoard->getSquare($hitShipX + 1, $hitShipY);
            if ($rightSquare?->getState() === Square::EMPTY) {
                $options[] = $rightSquare->getCoords();
            }
        }

        return $options;
    }


    /**
     * It returns all possible coordinates for shooting in vertical direction
     */
    protected function getVerticalOptions(): array
    {
        $options = [];

        foreach ($this->partsOfDamagedShip as $shipPart) {
            [$hitShipX, $hitShipY] = $shipPart;

            $topSquare = $this->shootingBoard->getSquare($hitShipX, $hitShipY - 1);
            if ($topSquare?->getState() === Square::EMPTY) {
                $options[] = $topSquare->getCoords();
            }

            $bottomSquare = $this->shootingBoard->getSquare($hitShipX, $hitShipY + 1);
            if ($bottomSquare?->getState() === Square::EMPTY) {
                $options[] = $bottomSquare->getCoords();
            }
        }

        return $options;
    }


    /**
     * When size of hit ship is 1 and we don't know it's direction
     * then we'll be shooting in both direction
     */
    protected function getOptionsForBothDirections(): array
    {
        return array_merge(
            $this->getHorizontalOptions(),
            $this->getVerticalOptions()
        );
    }
}
