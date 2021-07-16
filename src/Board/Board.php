<?php

namespace SeaBattle\Board;


class Board
{
    const WIDTH = 10;
    const HEIGHT = 10;

    protected array $cells = [];

    protected array $shipsToBeCreated = [
        ["size" => 4, "amount" => 1],
        ["size" => 3, "amount" => 2],
        ["size" => 2, "amount" => 3],
        ["size" => 1, "amount" => 4],
    ];


    public function __construct()
    {
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                $this->cells[$x][$y] = new Cell();
            }
        }
    }


    public function getShipsToBeCreated(): array
    {
        return $this->shipsToBeCreated;
    }


    public function getCell(int $x, int $y): ?Cell
    {
        if ($x < 0 || $y < 0 || $x >= self::WIDTH || $y >= self::HEIGHT ) {
            return null;
        }

        return $this->cells[$x][$y];
    }


    public function print(): string
    {
        $boardInHTML = "<table>";

        for ($x = 0; $x < self::HEIGHT; $x++) {
            $boardInHTML .= "<tr>";

            for ($y = 0; $y < self::WIDTH; $y++) {
                $boardInHTML .= "<td ";

                switch ($this->getCell($x, $y)->getState()) {
                    case Cell::EMPTY:
                        $boardInHTML .= "class='none'";
                        break;
                    case Cell::SHIP:
                        $boardInHTML .= "class='ship'";
                        break;
                    case Cell::SHIP_HIT:
                        $boardInHTML .= "class='hit-ship'";
                        break;
                    case Cell::SHIP_KILLED:
                        $boardInHTML .= "class='dead-ship'";
                        break;
                    case Cell::PLAYER_MISSED:
                        $boardInHTML .= "class='missed'";
                        break;
                }

                $boardInHTML .= " data-x=$y data-y=$x></td>";
            }

            $boardInHTML .= "</tr>";
        }

        $boardInHTML .= "</table>";

        return $boardInHTML;
    }
}
