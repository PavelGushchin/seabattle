<?php

namespace SeaBattle\Board;

use SeaBattle\Ship\Ship;


abstract class AbstractMainBoard extends AbstractBoard
{
    /**
     * Player shoots to [$x, $y] and you have to check
     * if the Board has a ship there or not
     */
    abstract public function checkIfShipWasHit(int $x, int $y): bool;


    abstract public function placeShipsOnBoard();

    /**
     * Clear the Board
     */
    abstract public function clear();



    protected array $aliveShips = [];
    protected array $deadShips = [];

    protected array $shipsToBeCreated = [
        ['size' => 4, 'amount' => 1],
        ['size' => 3, 'amount' => 2],
        ['size' => 2, 'amount' => 3],
        ['size' => 1, 'amount' => 4],
    ];


    public function createShips()
    {
        foreach ($this->shipsToBeCreated as $shipsInfo) {
            for ($i = 0; $i < $shipsInfo['amount']; $i++) {
                $shipId = $this->numOfShipsOnBoard;
                $this->aliveShips[$shipId] = new Ship($shipId, $shipsInfo['size']);
                $this->numOfShipsOnBoard++;
            }
        }

        $this->placeShipsRandomly();
    }


    /**
     * This method randomly places ships on AbstractBoard.
     *
     * First, it choose random x and y coordinate for ship's head.
     * Second, it checks if ship can be located there: if can -
     * it places the ship on AbstractBoard, but if can not - it chooses
     * another x and y coordinates.
     */
    public function placeShipsRandomly()
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
     * This method is used for visual representation of Field's state
     *
     * It is important to know if we are drawing player's Field or
     * enemy's because theirs visual representations are a little
     * bit different
     *
     * @param bool $isEnemy
     */
    public function draw($isEnemy = false)
    {
        echo "<table>";

        for ($y = 0; $y < self::HEIGHT; $y++) {
            echo '<tr>';

            for ($x = 0; $x < self::WIDTH; $x++) {
                echo '<td ';

                switch ($this->slots[$x][$y]->getState()) {
                    case Slot::SLOT_IS_UNCOVERED:
                        echo 'class="uncovered"';
                        break;
                    case Slot::SLOT_IS_NONE:
                        echo 'class="empty"';
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

                echo ' data-x='.$x.' data-y='.$y.'></td>';
            }

            echo '</tr>';
        }

        echo "</table>";
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
     * the ship on AbstractBoard
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
                if ($this->cells[$x][$y]->getState() === AbstractCell::THERE_IS_A_SHIP) {
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
                $this->cells[$x][$y]->setState(AbstractCell::THERE_IS_A_SHIP);
                $this->cells[$x][$y]->setShipId($ship->getId());
            }
        }
    }
}