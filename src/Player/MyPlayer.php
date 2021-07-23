<?php

namespace SeaBattle\Player;

use SeaBattle\Board\Square;


class MyPlayer extends AbstractPlayer
{
    /**
     * Manual shooting
     */
    public function getCoordsForShooting(): array
    {
        if (isset($_GET["x"], $_GET["y"])) {
            $x = (int)$_GET["x"];
            $y = (int)$_GET["y"];

            $attackedSquare = $this->shootingBoard->getSquare($x, $y);

            if ($attackedSquare?->getState() === Square::EMPTY) {
                return [$x, $y];
            }
        }

        return [null, null];
    }
}
