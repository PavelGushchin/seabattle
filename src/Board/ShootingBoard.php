<?php

namespace SeaBattle\Board;


class ShootingBoard extends AbstractBoard
{
    protected array $allShips = [];
    protected array $killedShips = [];


    public function getKilledShips(): array
    {
        return $this->killedShips;
    }


    public function addKilledShip(int $shipSize): void
    {
        $this->killedShips[] = $shipSize;
    }


    public function getNumberOfKilledShips(): int
    {
        return count($this->killedShips);
    }


    public function getAllShips(): array
    {
        return $this->allShips;
    }


    public function addShip(int $shipSize): void
    {
        $this->allShips[] = $shipSize;
    }


    public function getAliveShips(): array
    {
        return array_diff($this->allShips, $this->killedShips);
    }
}
