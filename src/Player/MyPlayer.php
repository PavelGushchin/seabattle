<?php

namespace SeaBattle\Player;

use SeaBattle\Board\AbstractBoard;
use SeaBattle\Board\MyBoard;


class MyPlayer extends AbstractPlayer
{

    public function __construct()
    {
        $this->board = new MyBoard();
    }

    public function getCoordsForShooting(): array
    {
        if (isset($_GET['x']) && isset($_GET['y'])) {

        }

        return [];
    }


    public function shootTo(AbstractBoard $attackedBoard, $x = null, $y = null): bool
    {
        $shipWasHit = $attackedBoard->handleShot($x, $y);

        if ($attackedBoard->allShipsAreDead()) {
            $this->setGameover(true);

            if ($isEnemy === false) {
                $this->setWinner(Game::I_AM_THE_WINNER);
            } else {
                $this->setWinner(Game::ENEMY_IS_THE_WINNER);
            }
        }

        return $shipWasHit;
    }
}