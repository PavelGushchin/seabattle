<?php

namespace SeaBattle\Board;


abstract class AbstractBoard
{
    public const WIDTH = 10;
    public const HEIGHT = 10;

    protected array $squares = [];

    protected array $shipsToBeCreated = [
        ["ship size" => 4, "amount" => 1],
        ["ship size" => 3, "amount" => 2],
        ["ship size" => 2, "amount" => 3],
        ["ship size" => 1, "amount" => 4],
    ];


    public function __construct()
    {
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                $this->squares[$x][$y] = new Square($x, $y);
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
        $board = "<table>";

        for ($x = 0; $x < self::WIDTH; $x++) {
            $board .= "<tr>";

            for ($y = 0; $y < self::HEIGHT; $y++) {
                $squareState = $this->getSquare($x, $y)->getState();

                $cssClass = match ($squareState) {
                    Square::EMPTY => "empty",
                    Square::SHIP => "ship",
                    Square::HIT_SHIP => "hit-ship",
                    Square::KILLED_SHIP => "killed-ship",
                    Square::MISSED => "missed",
                };

                $board .= "<td class='$cssClass' data-x='$x' data-y='$y'></td>";
            }

            $board .= "</tr>";
        }

        $board .= "</table>";

        return $board;
    }
}
