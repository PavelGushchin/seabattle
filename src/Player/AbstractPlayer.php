<?php

namespace SeaBattle\Player;

use SeaBattle\Board\AbstractBoard;
use SeaBattle\Board\Cell;
use SeaBattle\Board\MainBoard;
use SeaBattle\Board\ShootingBoard;
use SeaBattle\Player\AI\PlacingShipsAI\InterfacePlacingShipsAI;
use SeaBattle\Player\AI\PlacingShipsAI\RandomAI;
use SeaBattle\Player\AI\ShootingAI\InterfaceShootingAI;


abstract class AbstractPlayer
{
    public const HIT = "Answer to enemy that he hit my ship";
    public const KILLED = "Answer to enemy that he killed my ship";
    public const MISSED = "Answer to enemy that he missed";

    protected array $needToCreateTheseShips = [
        ["size" => 4, "amount" => 1],
        ["size" => 3, "amount" => 2],
        ["size" => 2, "amount" => 3],
        ["size" => 1, "amount" => 4],
    ];

    protected AbstractBoard $mainBoard;
    protected AbstractBoard $shootingBoard;
    protected InterfacePlacingShipsAI $placingShipsAI;


    abstract public function getCoordsForShooting(): array;


    public function __construct()
    {
        $this->mainBoard = new MainBoard();
        $this->shootingBoard = new ShootingBoard();
        $this->placingShipsAI = new RandomAI();
    }


    public function createShipsOnMainBoard(): void
    {
        foreach ($this->needToCreateTheseShips as $ships) {
            for ($i = 0; $i < $ships["amount"]; $i++) {
                $this->mainBoard->addShip($ships["size"]);
            }
        }

        $this->placingShipsAI->placeShipsOnBoard($this->mainBoard);
    }


    public function clearBoards(): void
    {
        $this->mainBoard = new MainBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function writeResultOfShooting(int $x, int $y, bool $wasShipHit): void
    {

    }


    public function checkIfWon(): bool
    {
        return $this->shootingBoard->getNumberOfKilledShips() === $this->mainBoard->getNumberOfAllShips();
    }


    public function setPlacingShipsAI(InterfacePlacingShipsAI $placingShipsAI): void
    {
        $this->placingShipsAI = $placingShipsAI;
    }


    public function printMainBoard(): string
    {
        return $this->mainBoard->print();
    }


    public function printShootingBoard(): string
    {
        return $this->shootingBoard->print();
    }


    public function handleShotAndGiveResult(int $x, int $y): int
    {
        $cell = $this->mainBoard->getCell($x, $y);

        switch ($cell->getState()) {
            case Cell::SLOT_IS_UNCOVERED:
                $cell->setState(Cell::PLAYER_MISSED);
                break;
            case Cell::SLOT_IS_NONE:
                $cell->setState(Cell::PLAYER_MISSED);
                break;
            case Cell::THERE_IS_A_SHIP:
                $shipId = $cell->getShipId();
                $ship = $this->aliveShips[$shipId];

                $isShipDead = $ship->addHitAndCheckForDeath();

                if ($isShipDead) {
                    $this->deadShips++;

                    $areaAroundShip = $this->getAreaAroundShip($shipId);

                    for ($i = $areaAroundShip['startX']; $i <= $areaAroundShip['endX']; $i++) {
                        for ($j = $areaAroundShip['startY']; $j <= $areaAroundShip['endY']; $j++) {
                            if ($this->slots[$i][$j]->getState() === Cell::SLOT_IS_UNCOVERED) {
                                $this->slots[$i][$j]->setState(Cell::SLOT_IS_NONE);
                            }
                        }
                    }

                    for ($i = $ship->getStartX(); $i <= $ship->getEndX(); $i++) {
                        for ($j = $ship->getStartY(); $j <= $ship->getEndY(); $j++) {
                            $this->slots[$i][$j]->setState(Cell::SHIP_IS_DEAD);
                        }
                    }
                } else {
                    $cell->setState(Cell::SHIP_WAS_HIT);
                }

                $shipWasHit = true;
                break;
        }

        return $shipWasHit;
    }


    protected function checkIfCoordsAreLegalForShooting(?int $x, ?int $y): bool
    {

    }

}
