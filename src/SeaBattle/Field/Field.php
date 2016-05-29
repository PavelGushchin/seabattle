<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\Field;

use SeaBattle\Ship\Ship;
use SeaBattle\AI\ShootingAIInterface;

/**
 * Field represents battle field on which player can locate ships.
 * Also Field can handle shots from opponent.
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class Field
{
    const WIDTH = 10;
    const HEIGT = 10;

    /**
     * @var array Array of slots from which consists Field
     */
    protected $slots = [];

    /**
     * @var array Array of ships which are placed on the Field
     */
    protected $ships = [];

    /**
     * @var ShootingAIInterface Contains AI for CPU
     */
    protected $shootingAI;

    /**
     * @var int Total number of ships placed on the Field
     */
    protected $totalAmountOfShips = 0;

    /**
     * @var int Number of ships killed by opponent
     */
    protected $deadShips = 0;

    /**
     * @var int Total number of shots made by opponent
     */
    protected $totalShots = 0;

    /**
     * @var array Configuration for ship creation
     */
    protected $shipsToBeCreated = [
        ['size' => 4, 'amount' => 1],
        ['size' => 3, 'amount' => 2],
        ['size' => 2, 'amount' => 3],
        ['size' => 1, 'amount' => 4],
    ];


    /**
     * Field constructor.
     *
     * @param ShootingAIInterface|null $shootingAI Represents AI for CPU
     */
    public function __construct(ShootingAIInterface $shootingAI = null)
    {
        $this->shootingAI = $shootingAI;

        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGT; $y++) {
                $this->slots[$x][$y] = new Slot();
            }
        }
    }

    /**
     * This method is used to create ships.
     *
     * @see Field::$shipsToBeCreated
     */
    public function createShips()
    {
        foreach ($this->shipsToBeCreated as $shipsInfo) {
            for ($i = 0; $i < $shipsInfo['amount']; $i++) {
                $shipId = $this->totalAmountOfShips;
                $this->ships[$shipId] = new Ship($shipId, $shipsInfo['size']);
                $this->totalAmountOfShips++;
            }
        }
    }

    /**
     * This method chooses random locations for all ships.
     *
     * First, it choose random x and y coordinate for ship's head.
     * Second, it checks if ship can be located there: if can -
     * it places the ship on Field, but if can not - it chooses
     * another x and y coordinates.
     */
    public function placeShipsRandomly()
    {
        foreach ($this->ships as $ship) {
            do {
                $ship->setDirection(mt_rand(Ship::HORIZONTAL, Ship::VERTICAL));

                $ship->setStartX(mt_rand(0, self::WIDTH - 1));

                $ship->setStartY(mt_rand(0, self::HEIGT - 1));

                $isShipLocatedCorrectly = $this->isSizeOfShipIsNotVeryLong($ship)
                    && $this->isSpaceAroundShipIsEmpty($ship);
            } while (!$isShipLocatedCorrectly);

            $this->placeShipOnMap($ship);
        }
    }

    /**
     * This method is designed to handle shots from opponent
     *
     * @param int $x Shot's x-coordinate
     * @param int $y Shot's y-coordinate
     *
     * @return bool Was shot successful and some ship was hit?
     */
    public function handleShot($x, $y)
    {
        if ($x < 0 || $x >= self::WIDTH ||
            $y < 0 || $y >= self::HEIGT) {
            return false;
        }

        $shipWasHit = false;
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
                            $this->slots[$i][$j]->setState(Slot::SHIP_IS_DEAD);
                        }
                    }
                } else {
                    $slot->setState(Slot::SHIP_WAS_HIT);
                }

                $shipWasHit = true;
                break;
        }

        $this->totalShots++;

        return $shipWasHit;
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
        for ($y = 0; $y < self::HEIGT; $y++) {
            echo '<tr>';

            for ($x = 0; $x < self::WIDTH; $x++) {
                echo '<td ';

                switch ($this->slots[$x][$y]->getState()) {
                    case Slot::SLOT_IS_UNCOVERED:
                        echo 'class="uncovered"';
                        break;
                    case Slot::SLOT_IS_EMPTY:
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
    }

    /**
     * It is used to determine if game is over or not.
     *
     * @return bool
     */
    public function allShipsAreDead()
    {
        return ($this->totalAmountOfShips === $this->deadShips)
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
    public function getShips()
    {
        return $this->ships;
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

    /**
     * Checks if cells around ship is empty so we can place
     * the ship on Field
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
                if ($this->slots[$x][$y]->getState() === Slot::THERE_IS_A_SHIP) {
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
            'endY' => $areaEndY,
        ];
    }

    /**
     * This method is called when ship passed all checks and therefore
     * located correctly.
     *
     * @param Ship $ship Ship to place on map
     */
    private function placeShipOnMap($ship)
    {
        for ($x = $ship->getStartX(); $x <= $ship->getEndX(); $x++) {
            for ($y = $ship->getStartY(); $y <= $ship->getEndY(); $y++) {
                $this->slots[$x][$y]->setState(Slot::THERE_IS_A_SHIP);
                $this->slots[$x][$y]->setShipId($ship->getId());
            }
        }
    }
}
