<?php

namespace SeaBattle\Player\AI;

use SeaBattle\Board\ShootingBoard;
use SeaBattle\Board\Square;


class ImprovedRandomAI implements InterfaceAI
{
    public const HORIZONTAL = "Hit ship has horizontal direction";
    public const VERTICAL = "Hit ship has vertical direction";
    public const UNKNOWN = "Hit ship has unknown direction";

    /** Damaged parts of hit ship will be stored here */
    protected array $partsOfDamagedShip = [];
    protected string $directionOfHitShip = self::UNKNOWN;

    protected ShootingBoard $shootingBoard;
    protected array $previousShootingCoords = [];

    protected InterfaceAI $randomShooter;


    public function __construct()
    {
        $this->randomShooter = new RandomAI();
    }


    /**
     * The main goal of this method is to return
     * an array with coordinates for the next shot
     */
    public function getCoordsForShooting(ShootingBoard $shootingBoard): array
    {
        $this->shootingBoard = $shootingBoard;

        $this->writeResultOfPreviousShooting();

        if (empty($this->partsOfDamagedShip)) {
            /** We don't have a hit ship on Board, so we'll shoot randomly */
            $randomCoords = $this->randomShooter->getCoordsForShooting($this->shootingBoard);

            $this->previousShootingCoords = $randomCoords;

            return $randomCoords;
        }

        /**
         * We have a hit ship on Board, so we have to find it and kill
         */
        $allPossibleCoordsOfHitShip = $this->getAllPossibleCoordsOfHitShip();
        shuffle($allPossibleCoordsOfHitShip);

        $coords = array_pop($allPossibleCoordsOfHitShip);

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


    protected function getAllPossibleCoordsOfHitShip(): array
    {
        /**
         * If we know direction of hit ship then we
         * can shoot only horizontally or vertically
         */
        switch ($this->directionOfHitShip) {
            case self::HORIZONTAL:
                return $this->getAllPossibleHorizontalCoordsOfHitShip();
            case self::VERTICAL:
                return $this->getAllPossibleVerticalCoordsOfHitShip();
        }

        /**
         * If we don't know ship's direction then we will
         * shoot in both directions
         */
        return $this->getAllPossibleBidirectionalCoordsOfHitShip();
    }


    protected function getAllPossibleHorizontalCoordsOfHitShip(): array
    {
        $allCoords = [];

        foreach ($this->partsOfDamagedShip as [$hitShipX, $hitShipY]) {
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

        foreach ($this->partsOfDamagedShip as [$hitShipX, $hitShipY]) {
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
}
