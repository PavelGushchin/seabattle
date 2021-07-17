<?php

namespace SeaBattle\Board;


abstract class AbstractBoard
{
    public const WIDTH = 10;
    public const HEIGHT = 10;

    protected array $squares = [];


    public function __construct()
    {
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                $this->squares[$x][$y] = new Square();
            }
        }
    }


    public function getSquare(int $x, int $y): ?Square
    {
        if ($x < 0 || $y < 0 || $x >= self::WIDTH || $y >= self::HEIGHT) {
            return null;
        }

        return $this->squares[$x][$y];
    }


    public function print(): string
    {
        $boardInHTML = "<table>";

        for ($x = 0; $x < self::HEIGHT; $x++) {
            $boardInHTML .= "<tr>";

            for ($y = 0; $y < self::WIDTH; $y++) {
                $boardInHTML .= "<td ";

                switch ($this->getSquare($x, $y)->getState()) {
                    case Square::EMPTY:
                        $boardInHTML .= "class='empty'";
                        break;
                    case Square::SHIP:
                        $boardInHTML .= "class='ship'";
                        break;
                    case Square::HIT_SHIP:
                        $boardInHTML .= "class='hit-ship'";
                        break;
                    case Square::KILLED_SHIP:
                        $boardInHTML .= "class='killed-ship'";
                        break;
                    case Square::MISSED:
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
