<?php

namespace SeaBattle\Board;


class ShootingBoard extends AbstractBoard
{
    protected array $aliveShips = [];
    protected array $killedShips = [];


    public function addKilledShip(int $shipSize): void
    {
        $this->killedShips[] = $shipSize;

        $indexOfShip = array_search($shipSize, $this->aliveShips, true);
        array_splice($this->aliveShips, $indexOfShip, 1);
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
