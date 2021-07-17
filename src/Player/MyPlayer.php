<?php

namespace SeaBattle\Player;

use SeaBattle\Board\Cell;


class MyPlayer extends AbstractPlayer
{
    public function getCoordsForShooting(): array
    {
        if (!isset($_GET["x"]) || !isset($_GET["y"])) {
            return [null, null];
        }

        $x = (int)$_GET["x"];
        $y = (int)$_GET["y"];

        $attackedCell = $this->shootingBoard->getCell($x, $y);

        if ($attackedCell && $attackedCell->getState() === Cell::EMPTY) {
            return [$x, $y];
        }

        return [null, null];
    }
}
