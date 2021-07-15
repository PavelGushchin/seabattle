<?php

namespace SeaBattle\Player;

use SeaBattle\Board\Board;
use SeaBattle\Board\Cell;
use SeaBattle\Player\AI\ShootingAI\InterfaceShootingAI;
use SeaBattle\Player\AI\PlacingShipsAI\InterfacePlacingShipsAI;
use SeaBattle\Player\AI\PlacingShipsAI\RandomAI;


class Player
{
    protected Board $mainBoard;
    protected Board $shootingBoard;
    protected InterfacePlacingShipsAI $placingShipsAI;
    protected ?InterfaceShootingAI $shootingAI;


    public function __construct(?InterfaceShootingAI $shootingAI = null)
    {
        $this->mainBoard = new Board();
        $this->shootingBoard = new Board();
        $this->placingShipsAI = new RandomAI();
        $this->shootingAI = $shootingAI;
    }


    public function getCoordsForShooting(): array
    {
        /** Shooting by AI */
        if ($this->shootingAI) {
            return $this->shootingAI->getCoordsForShooting($this->shootingBoard);
        }

        /** Manual shooting **/
        $x = isset($_GET["x"]) ? intval($_GET["x"]) : null;
        $y = isset($_GET["y"]) ? intval($_GET["y"]) : null;

        if ($this->coordsAreLegalForShooting($x, $y)) {
            return [$x, $y];
        }

        return [null, null];
    }


    protected function coordsAreLegalForShooting(?int $x, ?int $y): bool
    {
        if ($x === null || $y === null) {
            return false;
        }

        $cell = $this->shootingBoard->getCell($x, $y);

        if (! $cell) {
            return false;
        }

        if ($cell->getStatus() === Cell::NONE) {
            return true;
        }

        return false;
    }


    public function placeShipsOnMainBoard(): void
    {
        $this->placingShipsAI->placeShipsOnBoard($this->mainBoard);
    }


    public function clearBoards(): void
    {
        $this->mainBoard = new Board();
        $this->shootingBoard = new Board();
    }


    public function checkIfShipWasHit(int $x, int $y): bool
    {
        // TODO: implement this function
    }


    public function writeResultOfShooting(int $x, int $y, bool $wasShipHit): void
    {
        // TODO: implement this function
    }


    public function checkIfWon(): bool
    {
        // TODO: implement this function
    }


    public function setPlacingShipsAI(InterfacePlacingShipsAI $placingShipsAI): void
    {
        $this->placingShipsAI = $placingShipsAI;
    }


    public function setShootingAI(InterfaceShootingAI $shootingAI): void
    {
        $this->shootingAI = $shootingAI;
    }
}
