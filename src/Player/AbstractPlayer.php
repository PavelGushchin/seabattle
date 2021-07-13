<?php


namespace SeaBattle\Player;


abstract class AbstractPlayer
{
    public function getBoard()
    {

    }


    /**
     * This method is used for shooting to opponent
     *
     * @param  Board $attackedBoard Indicates which Battle Board is under the fire
     * @param  int   $x             Represents horizontal shooting coordinate
     * @param  int   $y             Represents vertical shooting coordinate
     * @param  bool  $isEnemy       It is 'true' if CPU is shooting
     *
     * @return bool Indicates if our shot was successful or not
     */
    public function shootingTo(Board $attackedBoard, $x, $y, $isEnemy = false)
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