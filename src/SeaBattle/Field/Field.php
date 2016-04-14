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
    private $_isReady;

    public function __construct()
    {
        $this->_isReady = false;

        for ($i = 0; $i < self::WIDTH; $i++) {
            for ($j = 0; $j < self::HEIGT; $j++) {
                $this->slots[$i][$j] = new Slot();
            }
        }
    }

    public function isReady()
    {
        return $this->_isReady;
    }

    public function locateShips()
    {
        $this->createShips();

        foreach($this->ships as $ship) {

            do {
                $direction = mt_rand(0, 1);

                $firstSlotX = mt_rand(0, 9);
                $firstSlotY = mt_rand(0, 9);

                $isValid = $ship->generateAndCheck($direction, $firstSlotX, $firstSlotY);
            } while (!$isValid);
        }
    }

    public function createShips()
    {
        $this->ships[] = new Ship(1);
        $this->ships[] = new Ship(1);
        $this->ships[] = new Ship(1);
        $this->ships[] = new Ship(1);

        $this->ships[] = new Ship(2);
        $this->ships[] = new Ship(2);
        $this->ships[] = new Ship(2);

        $this->ships[] = new Ship(3);
        $this->ships[] = new Ship(3);

        $this->ships[] = new Ship(4);
    }


    public function draw()
    {
        for ($i = 0; $i < 10; $i++) {
            echo '<tr>';

            for ($j = 0; $j < 10; $j++) {
                echo '<td';

                switch($this->slots[$i][$j]->getState()) {
                    case Slot::THERE_IS_A_SHIP:
                        echo ' class="placedShip"';
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
}