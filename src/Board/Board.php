<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;
use SeaBattle\Board\Cell;


class Board
{
    const WIDTH = 10;
    const HEIGHT = 10;

    protected array $cells;

//    protected int $numOfShipsOnBoard = 0;

    public function __construct()
    {
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                $this->cells[$x][$y] = new Cell();
            }
        }
    }



    /**
     * It is used to determine if game is over or not.
     *
     * @return bool
     */
    public function allShipsAreDead()
    {
        return ($this->numOfShipsOnBoard <= $this->deadShips)
            ? true
            : false
        ;
    }

    /**
     * Returns slot (@see \SeaBattle\Field\Slot) from array by its
     * x and y coordinates.
     *
     * @param int $x
     * @param int $y
     *
     * @return Slot
     */
    public function getSlot($x, $y)
    {
        return $this->slots[$x][$y];
    }

    /**
     * Returns all Field's slots
     *
     * @return array Array of slots
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Returns all Field's ships
     *
     * @return array Array of ships
     */
    public function getAliveShips()
    {
        return $this->aliveShips;
    }

    /**
     * Returns current AI of CPU
     *
     * @return ShootingAIInterface|null
     */
    public function getShootingAI()
    {
        return $this->shootingAI;
    }

    /**
     * Returns total number of shots made by opponent
     *
     * @return int
     */
    public function getTotalShots()
    {
        return $this->totalShots;
    }

    /**
     * Checks if ship can fit on Field with given size, direction and
     * x and y coordinates of ship's head
     *
     * @param Ship $ship Checked ship
     *
     * @return bool
     */
    private function isSizeOfShipIsNotVeryLong($ship)
    {
        $startX = $ship->getStartX();
        $startY = $ship->getStartY();

        switch ($ship->getDirection()) {
            case Ship::HORIZONTAL:
                $endX = $startX + $ship->getSize() - 1;
                break;
            case Ship::VERTICAL:
                $endY = $startY + $ship->getSize() - 1;
                break;
            default:
                throw new \Exception("Ship doesn't have a direction!");
        }

        if ($endX >= self::WIDTH || $endY >= self::HEIGHT) {
            return false;
        }

        $ship->setEndX($endX);
        $ship->setEndY($endY);

        return true;
    }

    /**
     * Checks if cells around ship is empty so we can place
     * the ship on Board
     *
     * @param Ship $ship Checked ship
     *
     * @return bool
     */
    private function isSpaceAroundShipIsEmpty($ship)
    {
        $checkedArea = $this->getAreaAroundShip($ship->getId());

        for ($x = $checkedArea['startX']; $x <= $checkedArea['endX']; $x++) {
            for ($y = $checkedArea['startY']; $y <= $checkedArea['endY']; $y++) {
                if ($this->cells[$x][$y]->getState() === Cell::THERE_IS_A_SHIP) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * This method is just a helper for another method
     * (@see Field::isSpaceAroundShipIsEmpty)
     *
     * It returns array which contains coordinates for starting and ending
     * points of area that surrounds the ship
     *
     * @param int $shipId
     *
     * @return array
     */
    private function getAreaAroundShip($shipId)
    {
        $ship = $this->aliveShips[$shipId];

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

        if ($areaEndY >= self::HEIGHT) {
            $areaEndY = self::HEIGHT - 1;
        }

        return [
            'startX' => $areaStartX,
            'startY' => $areaStartY,
            'endX' => $areaEndX,
            'endY' => $areaEndY,
        ];
    }

    /**
     * This method is called when ship passed all checks and therefore
     * located correctly.
     *
     * @param Ship $ship Ship to place on map
     */
    private function placeShipOnBoard($ship)
    {
        for ($x = $ship->getStartX(); $x <= $ship->getEndX(); $x++) {
            for ($y = $ship->getStartY(); $y <= $ship->getEndY(); $y++) {
                $this->slots[$x][$y]->setState(Slot::THERE_IS_A_SHIP);
                $this->slots[$x][$y]->setShipId($ship->getId());
            }
        }
    }
}
