<?php

namespace SeaBattle\Field;

use SeaBattle\Ship\Ship;
use SeaBattle\AI\IShootingAI;


class Field
{
    const WIDTH = 10;
    const HEIGT = 10;

    private $slots = [];
    private $ships = [];
    private $shootingAI;
    private $totalAmountOfShips = 0;
    private $deadShips = 0;
    private $shipsToBeCreated = [
        ['size' => 4, 'amount' => 1],
        ['size' => 3, 'amount' => 2],
        ['size' => 2, 'amount' => 3],
        ['size' => 1, 'amount' => 4],
    ];


    public function __construct(IShootingAI $shootingAI = null)
    {
        $this->shootingAI = $shootingAI;

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
                $shipId = $this->totalAmountOfShips;
                $this->ships[$shipId] = new Ship($shipId, $shipsInfo['size']);
                $this->totalAmountOfShips++;
            }
        }
    }


    public function placeShipsRandomly()
    {
        foreach($this->ships as $ship) {
            do {
                $ship->setDirection( mt_rand(Ship::HORIZONTAL, Ship::VERTICAL) );

                $ship->setStartX( mt_rand(0, self::WIDTH - 1) );

                $ship->setStartY( mt_rand(0, self::HEIGT - 1) );

                $isShipLocatedCorrectly = $this->isSizeOfShipIsNotVeryLong($ship)
                    && $this->isSpaceAroundShipIsEmpty($ship);
            } while (!$isShipLocatedCorrectly);

            $this->placeShipOnMap($ship);
        }
    }


    private function isSizeOfShipIsNotVeryLong($ship)
    {
        $endX = $ship->getStartX();
        $endY = $ship->getStartY();

        switch ($ship->getDirection()) {
            case Ship::HORIZONTAL:
                $endX = $endX + $ship->getSize() - 1;
                break;
            case Ship::VERTICAL:
                $endY = $endY + $ship->getSize() - 1;
                break;
        }

        if ($endX >= self::WIDTH || $endY >= self::HEIGT) {
            return false;
        }

        $ship->setEndX($endX);
        $ship->setEndY($endY);

        return true;
    }


    private function isSpaceAroundShipIsEmpty($ship)
    {
        $checkedArea = $this->getAreaAroundShip($ship->getId());

        for ($i = $checkedArea['startX']; $i <= $checkedArea['endX']; $i++) {
            for ($j = $checkedArea['startY']; $j <= $checkedArea['endY']; $j++) {
                if ($this->slots[$i][$j]->getState() === Slot::THERE_IS_A_SHIP) {
                    return false;
                }
            }
        }

        return true;
    }


    private function getAreaAroundShip($shipId)
    {
        $ship = $this->ships[$shipId];

        $areaStartX = $ship->getStartX() - 1;
        $areaStartY = $ship->getStartY() - 1;

        if ($areaStartX < 0) {
            $areaStartX = 0;
        }

        if ($areaStartY < 0) {
            $areaStartY = 0;
        }


        $areaEndX = $ship->getEndX() + 1;
        $areaEndY = $ship->getEndY() + 1;

        if ($areaEndX >= self::WIDTH) {
            $areaEndX = self::WIDTH - 1;
        }

        if ($areaEndY >= self::HEIGT) {
            $areaEndY = self::HEIGT - 1;
        }

        return [
            'startX' => $areaStartX,
            'startY' => $areaStartY,
            'endX' => $areaEndX,
            'endY' => $areaEndY
        ];
    }


    private function placeShipOnMap($ship)
    {
        for ($i = $ship->getStartX(); $i <= $ship->getEndX(); $i++) {
            for ($j = $ship->getStartY(); $j <= $ship->getEndY(); $j++) {
                $this->slots[$i][$j]->setState(Slot::THERE_IS_A_SHIP);
                $this->slots[$i][$j]->setShipId($ship->getId());
            }
        }
    }


    public function handleShot($x, $y)
    {
        if ($x < 0 || $x >= self::WIDTH ||
            $y < 0 || $y >= self::HEIGT) {
            return;
        }

        $slot = $this->getSlot($x, $y);

        switch ($slot->getState()) {
            case Slot::SLOT_IS_UNCOVERED:
                $slot->setState(Slot::PLAYER_MISSED);
                break;
            case Slot::SLOT_IS_EMPTY:
                $slot->setState(Slot::PLAYER_MISSED);
                break;
            case Slot::THERE_IS_A_SHIP:
                $shipId = $slot->getShipId();
                $ship = $this->ships[$shipId];

                $isShipDead = $ship->addHitAndCheckForDeath();

                if ($isShipDead) {
                    $this->deadShips++;

                    $areaAroundShip = $this->getAreaAroundShip($shipId);

                    for ($i = $areaAroundShip['startX']; $i <= $areaAroundShip['endX']; $i++) {
                        for ($j = $areaAroundShip['startY']; $j <= $areaAroundShip['endY']; $j++) {
                            if ($this->slots[$i][$j]->getState() === Slot::SLOT_IS_UNCOVERED) {
                                $this->slots[$i][$j]->setState(Slot::SLOT_IS_EMPTY);
                            }
                        }
                    }

                    for ($i = $ship->getStartX(); $i <= $ship->getEndX(); $i++) {
                        for ($j = $ship->getStartY(); $j <= $ship->getEndY(); $j++) {
                            $this->getSlot($i,$j)->setState(Slot::SHIP_IS_DEAD);
                        }
                    }
                } else {
                    $slot->setState(Slot::SHIP_WAS_HIT);
                }

                break;
        }
    }


    public function draw($isEnemy = false)
    {
        for ($i = 0; $i < self::WIDTH; $i++) {
            echo '<tr>';

            for ($j = 0; $j < self::HEIGT; $j++) {
                echo '<td ';

                switch($this->slots[$i][$j]->getState()) {
                    case Slot::SLOT_IS_UNCOVERED:
                    case Slot::SLOT_IS_EMPTY:
                        echo 'class="uncovered"';
                        break;
                    case Slot::PLAYER_MISSED:
                        echo 'class="missed"';
                        break;
                    case Slot::THERE_IS_A_SHIP:
                        echo $isEnemy === false
                            ? 'class="ship"'
                            : 'class="uncovered"';
                        break;
                    case Slot::SHIP_WAS_HIT:
                        echo 'class="hit"';
                        break;
                    case Slot::SHIP_IS_DEAD:
                        echo 'class="dead"';
                        break;
                }

                echo ' data-x=' . $i . ' data-y=' . $j . '></td>';
            }

            echo '</tr>';
        }
    }


    public function allShipsAreDead()
    {
        return ($this->totalAmountOfShips === $this->deadShips)
            ? true
            : false
        ;
    }


    public function getSlot($x, $y)
    {
        return $this->slots[$x][$y];
    }


    public function getSlots()
    {
        return $this->slots;
    }

    public function getShips()
    {
        return $this->ships;
    }


    public function getShootingAI()
    {
        return $this->shootingAI;
    }
}