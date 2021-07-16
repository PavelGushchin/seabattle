<?php

namespace SeaBattle\Player;

use SeaBattle\Board\Cell;
use SeaBattle\Board\MainBoard;
use SeaBattle\Board\ShootingBoard;
use SeaBattle\Player\AI\PlacingShipsAI\InterfacePlacingShipsAI;
use SeaBattle\Player\AI\PlacingShipsAI\RandomAI;
use SeaBattle\Player\AI\ShootingAI\InterfaceShootingAI;


class Player
{
    const HIT = "Answer to enemy that he hit my ship";
    const KILLED = "Answer to enemy that he killed my ship";
    const MISSED = "Answer to enemy that he missed";

    protected MainBoard $mainBoard;
    protected ShootingBoard $shootingBoard;
    protected InterfacePlacingShipsAI $placingShipsAI;
    protected ?InterfaceShootingAI $shootingAI;


    public function __construct(?InterfaceShootingAI $shootingAI = null)
    {
        $this->mainBoard = new MainBoard();
        $this->shootingBoard = new ShootingBoard();
        $this->placingShipsAI = new RandomAI();
        $this->shootingAI = $shootingAI;
    }


    public function getCoordsForShooting(): array
    {
        if ($this->shootingAI) {
            /** AI Shooting by Enemy Player */
            return $this->shootingAI->getCoordsForShooting($this->shootingBoard);
        }

        /** Manual shooting by My Player **/
        $x = isset($_GET["x"]) ? intval($_GET["x"]) : null;
        $y = isset($_GET["y"]) ? intval($_GET["y"]) : null;

        if ($this->checkIfCoordsAreLegalForShooting($x, $y)) {
            return [$x, $y];
        }

        return [null, null];
    }


    protected function checkIfCoordsAreLegalForShooting(?int $x, ?int $y): bool
    {
        $attackedBoardCell = $this->shootingBoard->getCell(intval($x), intval($y));

        if (! $attackedBoardCell) {
            return false;
        }

        if ($attackedBoardCell->getState() === Cell::EMPTY) {
            return true;
        }

        return false;
    }


    public function createShipsOnMainBoard(): void
    {
        $shipsToBeCreated = $this->mainBoard->getShipsToBeCreated();

        foreach ($shipsToBeCreated as $ship) {
            for ($i = 0; $i < $ship["amount"]; $i++) {
                $this->mainBoard->addShip($ship["size"]);
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


    public function setShootingAI(InterfaceShootingAI $shootingAI): void
    {
        $this->shootingAI = $shootingAI;
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
        $shipWasHit = false;
        $slot = $this->getSlot($x, $y);

        switch ($slot->getState()) {
            case Slot::SLOT_IS_UNCOVERED:
                $slot->setState(Slot::PLAYER_MISSED);
                break;
            case Slot::SLOT_IS_NONE:
                $slot->setState(Slot::PLAYER_MISSED);
                break;
            case Slot::THERE_IS_A_SHIP:
                $shipId = $slot->getShipId();
                $ship = $this->aliveShips[$shipId];

                $isShipDead = $ship->addHitAndCheckForDeath();

                if ($isShipDead) {
                    $this->deadShips++;

                    $areaAroundShip = $this->getAreaAroundShip($shipId);

                    for ($i = $areaAroundShip['startX']; $i <= $areaAroundShip['endX']; $i++) {
                        for ($j = $areaAroundShip['startY']; $j <= $areaAroundShip['endY']; $j++) {
                            if ($this->slots[$i][$j]->getState() === Slot::SLOT_IS_UNCOVERED) {
                                $this->slots[$i][$j]->setState(Slot::SLOT_IS_NONE);
                            }
                        }
                    }

                    for ($i = $ship->getStartX(); $i <= $ship->getEndX(); $i++) {
                        for ($j = $ship->getStartY(); $j <= $ship->getEndY(); $j++) {
                            $this->slots[$i][$j]->setState(Slot::SHIP_IS_DEAD);
                        }
                    }
                } else {
                    $slot->setState(Slot::SHIP_WAS_HIT);
                }

                $shipWasHit = true;
                break;
        }

        $this->totalShots++;

        return $shipWasHit;
    }

}
