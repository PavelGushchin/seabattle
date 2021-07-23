<?php

namespace SeaBattle\Board;


class ShootingBoard extends AbstractBoard
{
    protected array $aliveShips = [];
    protected array $killedShips = [];


    public function addKilledShip(int $shipSize): void
    {
        $this->killedShips[] = $shipSize;

        /**
         * Removing the killed ship from $this->aliveShips array
         */
        array_splice(
            $this->aliveShips,
            array_search($shipSize, $this->aliveShips),
            1
        );
    }


    public function getNumberOfKilledShips(): int
    {
        return count($this->killedShips);
    }


    public function getAliveShips(): array
    {
        return $this->aliveShips;
    }


    public function addAliveShip(int $shipSize): void
    {
        $this->aliveShips[] = $shipSize;
    }
}
