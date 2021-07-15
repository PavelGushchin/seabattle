<?php

namespace SeaBattle\Player;

use SeaBattle\Board\AbstractCell;
use SeaBattle\Board\MainBoard;
use SeaBattle\Board\ShootingBoard;


class MyPlayer extends AbstractPlayer
{
    public function __construct()
    {
        $this->mainBoard = new MainBoard();
        $this->shootingBoard = new ShootingBoard();
    }


    public function getCoordsForShooting(): array
    {
        $x = isset($_GET["x"]) ? intval($_GET["x"]) : null;
        $y = isset($_GET["y"]) ? intval($_GET["y"]) : null;

        if ($this->coordsAreLegalForShooting($x, $y)) {
            return [$x, $y];
        }

        return [null, null];
    }


    private function coordsAreLegalForShooting(?int $x, ?int $y): bool
    {
        if ($x === null || $y === null) {
            return false;
        }

        $cell = $this->shootingBoard->getCell($x, $y);

        if (! $cell) {
            return false;
        }

        if ($cell->getStatus() === AbstractCell::NONE) {
            return true;
        }

        return false;
    }
}
