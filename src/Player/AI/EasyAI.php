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

    protected InterfaceAI $randomShooter;
    protected ShootingBoard $shootingBoard;
    protected array $previousShootingCoords = [];



    public function __construct()
    {
        $this->randomShooter = new VeryEasyAI();
    }


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
                return $this->getAllPossibleHorizontalCoordsOfHitShip();
            case self::VERTICAL:
                return $this->getAllPossibleVerticalCoordsOfHitShip();
        }

        return $this->getAllPossibleBidirectionalCoordsOfHitShip();
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


    protected function getRandomCoords(): array
    {
        return $this->randomShooter->getCoordsForShooting($this->shootingBoard);
    }


    public function reset(): void
    {
        $this->directionOfHitShip = self::UNKNOWN;
        $this->coordsOfHitShip = [];
        $this->previousShootingCoords = [];
    }
}
