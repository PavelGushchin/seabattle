<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


class EasyAI implements InterfaceAI
{
    public const HORIZONTAL = "Hit ship has horizontal direction";
    public const VERTICAL = "Hit ship has vertical direction";
    public const UNKNOWN = "Hit ship has unknown direction";

    protected array $coordsOfHitShip = [];
    protected string $directionOfHitShip = self::UNKNOWN;

    protected ShootingBoard $shootingBoard;
    protected array $previousShootingCoords = [];


    public function getCoordsForShooting(ShootingBoard $shootingBoard): array
    {
        $this->shootingBoard = $shootingBoard;

        $this->writeResultOfPreviousShooting();

        if (empty($this->coordsOfHitShip)) {
            /** If we don't have a hit ship on Board, then we'll shoot randomly */
            $randomCoords = $this->getRandomCoords();
            $this->previousShootingCoords = $randomCoords;

            return $randomCoords;
        }

        /** At this point we know that we have a hit ship on Board,
         *  so we have to find it and kill
         */
        $allPossibleCoordsOfHitShip = $this->getAllPossibleCoordsOfHitShip();
        shuffle($allPossibleCoordsOfHitShip);

        $shootingCoords = array_pop($allPossibleCoordsOfHitShip);

        $this->previousShootingCoords = $shootingCoords;

        return $shootingCoords;
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


    protected function getAllPossibleCoordsOfHitShip(): array
    {
        switch ($this->directionOfHitShip) {
            case self::HORIZONTAL:
                return $this->getAllPossibleHorizontalCoords();
            case self::VERTICAL:
                return $this->getAllPossibleVerticalCoords();
        }

        return $this->getAllPossibleBidirectionalCoords();
    }


    protected function getAllPossibleHorizontalCoords(): array
    {
        $allCoords = [];

        foreach ($this->coordsOfHitShip as [$x, $y]) {
            $leftSquare = $this->shootingBoard->getSquare($x - 1, $y);
            if ($leftSquare?->getState() === Square::EMPTY) {
                $allCoords[] = [$leftSquare->getX(), $leftSquare->getY()];
            }

            $rightSquare = $this->shootingBoard->getSquare($x + 1, $y);
            if ($rightSquare?->getState() === Square::EMPTY) {
                $allCoords[] = [$rightSquare->getX(), $rightSquare->getY()];
            }
        }

        return $allCoords;
    }


    protected function getAllPossibleVerticalCoords(): array
    {
        $allCoords = [];

        foreach ($this->coordsOfHitShip as [$x, $y]) {
            $topSquare = $this->shootingBoard->getSquare($x, $y - 1);
            if ($topSquare?->getState() === Square::EMPTY) {
                $allCoords[] = [$topSquare->getX(), $topSquare->getY()];
            }

            $rightSquare = $this->shootingBoard->getSquare($x, $y + 1);
            if ($rightSquare?->getState() === Square::EMPTY) {
                $allCoords[] = [$rightSquare->getX(), $rightSquare->getY()];
            }
        }

        return $allCoords;
    }


    protected function getAllPossibleBidirectionalCoords(): array
    {
        $horizontalCoords = $this->getAllPossibleHorizontalCoords();
        $verticalCoords = $this->getAllPossibleVerticalCoords();

        return array_merge($horizontalCoords, $verticalCoords);
    }


    protected function getRandomCoords(): array
    {
        do {
            $x = rand(0, ShootingBoard::WIDTH - 1);
            $y = rand(0, ShootingBoard::HEIGHT - 1);
            $square = $this->shootingBoard->getSquare($x, $y);
            $isSquareEmpty = $square->getState() === Square::EMPTY;
        } while (! $isSquareEmpty);

        return [$x, $y];
    }


    public function reset(): void
    {
        $this->directionOfHitShip = self::UNKNOWN;
        $this->coordsOfHitShip = [];
        $this->previousShootingCoords = [];
    }
}
