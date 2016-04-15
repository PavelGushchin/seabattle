<?php

namespace SeaBattle\Field;

use SeaBattle\Field\Slot;
use SeaBattle\Ship\Ship;


class Field
{
    const WIDTH = 10;
    const HEIGT = 10;

    private $slots;
    private $ships;
    private $aliveShips = 0;
    private $shipsToBeCreated = [
        ['size' => 1, 'amount' => 4],
        ['size' => 2, 'amount' => 3],
        ['size' => 3, 'amount' => 2],
        ['size' => 4, 'amount' => 1],
    ];


    public function __construct()
    {
        for ($i = 0; $i < self::WIDTH; $i++) {
            for ($j = 0; $j < self::HEIGT; $j++) {
                $this->slots[$i][$j] = new Slot();
            }
        }
    }

    public function createShips()
    {
        foreach ($this->shipsToBeCreated as $shipsInfo) {
            for ($i=0; $i<$shipsInfo['amount']; $i++) {
                $this->ships[] = new Ship($shipsInfo['size']);
                $this->aliveShips++;
            }
        }
    }


    public function placeShipsRandomly()
    {
        foreach($this->ships as $ship) {

            do {
                $direction = mt_rand(0, 1);

                $firstSlotX = mt_rand(0, 9);
                $firstSlotY = mt_rand(0, 9);

                $isValid = $ship->generateAndCheck($direction, $firstSlotX, $firstSlotY);
            } while (!$isValid);
        }
    }


    public function draw()
    {
        for ($i = 0; $i < self::WIDTH; $i++) {
            echo '<tr>';

            for ($j = 0; $j < self::HEIGT; $j++) {
                echo '<td';

                switch($this->slots[$i][$j]->getState()) {
                    case Slot::SLOT_IS_UNCOVERED:
                        echo ' class="uncovered"';
                        break;
                    case Slot::SLOT_IS_EMPTY:
                        echo ' class="empty"';
                        break;
                    case Slot::PLAYER_MISSED:
                        echo ' class="missed"';
                        break;
                    case Slot::THERE_IS_A_SHIP:
                        echo ' class="ship"';
                        break;
                    case Slot::SHIP_WAS_HIT:
                        echo ' class="hit';
                        break;
                }

                echo '></td>';
            }

            echo '</tr>';
        }
    }


    public function getSlot($x, $y)
    {
        return $this->slots[$x][$y];
    }


    public function handleShot($x, $y)
    {

    }

}