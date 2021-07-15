<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


abstract class AbstractBoard
{
    const WIDTH = 10;
    const HEIGHT = 10;

    protected array $cells = [];
    protected array $ships = [];


    abstract public function clear();


    public function __construct()
    {
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                $this->cells[$x][$y] = new AbstractCell();
            }
        }
    }


    public function getAllCells(): array
    {
        return $this->cells;
    }


    public function getCell(int $x, int $y)
    {
        return $this->cells[$x][$y];
    }


    public function getAllShips(): array
    {
        return $this->ships;
    }


    public function getShip(int $shipId): Ship
    {
        return $this->ships[$shipId];
    }
}
