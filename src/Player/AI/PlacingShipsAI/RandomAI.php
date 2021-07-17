<?php

namespace SeaBattle\Player\AI\PlacingShipsAI;


use SeaBattle\Board\AbstractBoard;
use SeaBattle\Board\Cell;
use SeaBattle\Ship\Ship;

class RandomAI implements InterfacePlacingShipsAI
{
    /**
     * This method places ships on Board randomly.
     *
     * First, it choose random x and y coordinate for ship's head.
     * Second, it checks if ship can be located there: if can -
     * it places the ship on Board, but if can not - it chooses
     * another x and y coordinates.
     */
    public function placeShipsOnBoard(AbstractBoard $board)
    {
        foreach ($this->aliveShips as $ship) {
            do {
                $ship->setDirection(mt_rand(Ship::HORIZONTAL, Ship::VERTICAL));

                $ship->setStartX(mt_rand(0, self::WIDTH - 1));

                $ship->setStartY(mt_rand(0, self::HEIGHT - 1));

                $isShipLocatedCorrectly = $this->isSizeOfShipIsNotVeryLong($ship)
                    && $this->isSpaceAroundShipIsEmpty($ship);
            } while (!$isShipLocatedCorrectly);

            $this->placeShipOnBoard($ship);
        }
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
                $this->cells[$x][$y]->setState(Cell::THERE_IS_A_SHIP);
                $this->cells[$x][$y]->setShipId($ship->getId());
            }
        }
    }

}