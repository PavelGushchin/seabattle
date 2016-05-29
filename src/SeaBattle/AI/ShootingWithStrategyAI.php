<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\AI;

use SeaBattle\Field\Field;
use SeaBattle\Field\Slot;

/**
 * ShootingWithStrategyAI represents algorithm which shoots
 * with a good strategy (upper-intermediate level)
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class ShootingWithStrategyAI implements ShootingAIInterface
{
    const SHOOT_HORIZONTALLY = 0;
    const SHOOT_VERTICALLY = 1;
    const SHOOT_BIDIRECTIONALLY = 2;

    /**
     * @var array Array with coordinates of damaged ship's parts
     */
    private $partsOfdamagedShip = [];

    /**
     * @var array Array with coordinates of all possible variants
     *            for next shot
     */
    private $variantsForNextShot = [];

    /**
     * @var int Current shooting direction (one of
     *          ShootingWithStrategyAI::SHOOT_BIDIRECTIONALLY or
     *          ShootingWithStrategyAI::SHOOT_HORIZONTALLY or
     *          ShootingWithStrategyAI::SHOOT_BIDIRECTIONALLY)
     */
    private $shootingDirection = self::SHOOT_BIDIRECTIONALLY;

    /**
     * Each slot has its own value for shooting.
     * First of all we are shooting to slots with the highest values
     *
     * @var array Array with values for slots
     */
    private $valuesForSlots;


    /**
     * Returns name of the algorithm
     *
     * @return string
     */
    public function __toString()
    {
        return 'Strategy algorithm';
    }

    /**
     * Main method of the class.
     *
     * It calculates coordinates for next shot
     * and returns them as array:
     *  [
     *      'x' => $x,
     *      'y' => $y,
     *  ]
     *
     * @param array      $slots Array with slots
     * @param array|null $ships Array with ships
     *
     * @return array Array of shot coordinates
     */
    public function calculateCoordsForShooting($slots, $ships = null)
    {
        $this->variantsForNextShot = [];
        $this->setValuesForSlots($slots, $ships);


        if (empty($this->partsOfdamagedShip)) {
            $this->calculateAllVariantsForRandomShot($slots);
        } else {
            $this->calculateAllVariantsForSmartShot($slots);
        }

        shuffle($this->variantsForNextShot);

        usort($this->variantsForNextShot, function ($a, $b) {
            return $b['value'] - $a['value'];
        });

        $coords = array_shift($this->variantsForNextShot);

        $x = $coords['x'];
        $y = $coords['y'];

        $slotState = !is_null($slots[$x][$y])
            ? $slots[$x][$y]->getState()
            : false;

        if ($slotState === Slot::THERE_IS_A_SHIP) {
            $shipId = $slots[$x][$y]->getShipId();
            $ship = $ships[$shipId];

            $isShipGoingToDie = $ship->getSize() === $ship->getHits() + 1;

            if ($isShipGoingToDie) {
                $this->partsOfdamagedShip = [];
                $this->shootingDirection = self::SHOOT_BIDIRECTIONALLY;
            } else {
                $this->partsOfdamagedShip[] = [
                    'x' => $x,
                    'y' => $y,
                ];

                if (count($this->partsOfdamagedShip) === 2) {
                    $this->defineShootingDirection();
                }
            }
        }

        return $coords;
    }

    /**
     * When ship is hit for the first time we do not know what
     * direction it has. And first of all we have to determine
     * that direction and shoot only at that direction until
     * ship will be killed
     */
    private function defineShootingDirection()
    {
        $firstPart = $this->partsOfdamagedShip[0];
        $secondPart = $this->partsOfdamagedShip[1];

        if ($firstPart['x'] === $secondPart['x']) {
            $this->shootingDirection = self::SHOOT_VERTICALLY;
        } else {
            $this->shootingDirection = self::SHOOT_HORIZONTALLY;
        }
    }

    /**
     * When we do not have any wounded ships we pick variant for next
     * shot randomly
     *
     * @param array $slots Array with slots
     */
    private function calculateAllVariantsForRandomShot($slots)
    {
        for ($i = 0; $i < Field::WIDTH; $i++) {
            for ($j = 0; $j < Field::HEIGT; $j++) {
                $slotState = $slots[$i][$j]->getState();

                if ($slotState === Slot::SLOT_IS_UNCOVERED ||
                    $slotState === Slot::THERE_IS_A_SHIP) {
                    $this->variantsForNextShot[] = [
                        'x' => $i,
                        'y' => $j,
                        'value' => $this->valuesForSlots[$i][$j],
                    ];
                }
            }
        }
    }

    /**
     * When we do have a wounded ship we have to shoot smartly
     * until we kill it
     *
     * @param array $slots Array with slots
     */
    private function calculateAllVariantsForSmartShot($slots)
    {
        foreach ($this->partsOfdamagedShip as $shipPart) {
            switch ($this->shootingDirection) {
                case self::SHOOT_HORIZONTALLY:
                    $this->addNewVariantsForHorizontalShooting($slots, $shipPart);
                    break;
                case self::SHOOT_VERTICALLY:
                    $this->addNewVariantsForVerticalShooting($slots, $shipPart);
                    break;
                case self::SHOOT_BIDIRECTIONALLY:
                    $this->addNewVariantsForHorizontalShooting($slots, $shipPart);
                    $this->addNewVariantsForVerticalShooting($slots, $shipPart);
                    break;
            }
        }
    }

    /**
     * It adds all possible variants for shooting in horizontal direction
     *
     * @param array $slots    Array with slots
     * @param array $shipPart Array with coordinates of ship's part
     */
    private function addNewVariantsForHorizontalShooting($slots, $shipPart)
    {
        $leftSlotX = $shipPart['x'] - 1;
        $leftSlotY = $shipPart['y'];

        if ($leftSlotX >= 0) {
            $leftSlotState = $slots[$leftSlotX][$leftSlotY]->getState();

            if ($leftSlotState === Slot::SLOT_IS_UNCOVERED ||
                $leftSlotState === Slot::THERE_IS_A_SHIP) {
                $this->variantsForNextShot[] = [
                    'x' => $leftSlotX,
                    'y' => $leftSlotY,
                    'value' => $this->valuesForSlots[$leftSlotX][$leftSlotY],
                ];
            }
        }

        $rightSlotX = $shipPart['x'] + 1;
        $rightSlotY = $shipPart['y'];

        if ($rightSlotX < Field::WIDTH) {
            $rightSlotState = $slots[$rightSlotX][$rightSlotY]->getState();

            if ($rightSlotState === Slot::SLOT_IS_UNCOVERED ||
                $rightSlotState === Slot::THERE_IS_A_SHIP) {
                $this->variantsForNextShot[] = [
                    'x' => $rightSlotX,
                    'y' => $rightSlotY,
                    'value' => $this->valuesForSlots[$rightSlotX][$rightSlotY],
                ];
            }
        }
    }

    /**
     * It adds all possible variants for shooting in vertical direction
     *
     * @param array $slots    Array with slots
     * @param array $shipPart Array with coordinates of ship's part
     */
    private function addNewVariantsForVerticalShooting($slots, $shipPart)
    {
        $topSlotX = $shipPart['x'];
        $topSlotY = $shipPart['y'] - 1;

        if ($topSlotY >= 0) {
            $topSlotState = $slots[$topSlotX][$topSlotY]->getState();

            if ($topSlotState === Slot::SLOT_IS_UNCOVERED ||
                $topSlotState === Slot::THERE_IS_A_SHIP) {
                $this->variantsForNextShot[] = [
                    'x' => $topSlotX,
                    'y' => $topSlotY,
                    'value' => $this->valuesForSlots[$topSlotX][$topSlotY],
                ];
            }
        }

        $bottomSlotX = $shipPart['x'];
        $bottomSlotY = $shipPart['y'] + 1;

        if ($bottomSlotY < Field::HEIGT) {
            $bottomSlotState = $slots[$bottomSlotX][$bottomSlotY]->getState();

            if ($bottomSlotState === Slot::SLOT_IS_UNCOVERED ||
                $bottomSlotState === Slot::THERE_IS_A_SHIP) {
                $this->variantsForNextShot[] = [
                    'x' => $bottomSlotX,
                    'y' => $bottomSlotY,
                    'value' => $this->valuesForSlots[$bottomSlotX][$bottomSlotY],
                ];
            }
        }
    }

    /**
     * This method evaluates all slots.
     * The higher slot's value is, the sooner it will be shot
     *
     * @param array $slots Array with slots
     * @param array $ships Array with ships
     */
    private function setValuesForSlots($slots, $ships)
    {
        $this->valuesForSlots = [];

        for ($i = 0; $i < Field::WIDTH; $i++) {
            for ($j = 0; $j < Field::HEIGT; $j++) {
                $this->valuesForSlots[$i][$j] = 0;

                if ($slots[$i][$j]->getState() !== Slot::SLOT_IS_UNCOVERED &&
                    $slots[$i][$j]->getState() !== Slot::THERE_IS_A_SHIP) {
                    continue;
                }

                $leftUncoveredSlots = $this->getAmountOfLeftUncoveredSlots($slots, $i, $j);
                $rightUncoveredSlots = $this->getAmountOfRightUncoveredSlots($slots, $i, $j);
                $topUncoveredSlots = $this->getAmountOfTopUncoveredSlots($slots, $i, $j);
                $bottomUncoveredSlots = $this->getAmountOfBottomUncoveredSlots($slots, $i, $j);

                $horizontalUncoveredSlots = $leftUncoveredSlots + $rightUncoveredSlots + 1;
                $verticalUncoveredSlots = $topUncoveredSlots + $bottomUncoveredSlots + 1;

                foreach ($ships as $ship) {
                    if (!$ship->isDead()) {
                        if ($ship->getSize() <= $horizontalUncoveredSlots) {
                            $this->valuesForSlots[$i][$j] += $ship->getSize();
                        }

                        if ($ship->getSize() <= $verticalUncoveredSlots) {
                            $this->valuesForSlots[$i][$j] += $ship->getSize();
                        }
                    }
                }


                if ($leftUncoveredSlots > 0) {
                    $this->valuesForSlots[$i][$j]++;
                }

                if ($rightUncoveredSlots > 0) {
                    $this->valuesForSlots[$i][$j]++;
                }

                if ($topUncoveredSlots > 0) {
                    $this->valuesForSlots[$i][$j]++;
                }

                if ($bottomUncoveredSlots > 0) {
                    $this->valuesForSlots[$i][$j]++;
                }
            }
        }
    }

    /**
     * Returns number of uncovered slots to the left
     * from our checked slot
     *
     * @param array $slots Array with slots
     * @param int   $x     X-coordinate of checked slot
     * @param int   $y     Y-coordinate of checked slot
     *
     * @return int
     */
    private function getAmountOfLeftUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for ($i = $x - 1; $i >= 0; $i--) {
            $slotState = $slots[$i][$y]->getState();

            if ($slotState === Slot::SLOT_IS_UNCOVERED ||
                $slotState === Slot::THERE_IS_A_SHIP ||
                $slotState === Slot::SHIP_WAS_HIT) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }

    /**
     * Returns number of uncovered slots to the right
     * from our checked slot
     *
     * @param array $slots Array with slots
     * @param int   $x     X-coordinate of checked slot
     * @param int   $y     Y-coordinate of checked slot
     *
     * @return int
     */
    private function getAmountOfRightUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for ($i = $x + 1; $i < Field::WIDTH; $i++) {
            $slotState = $slots[$i][$y]->getState();

            if ($slotState === Slot::SLOT_IS_UNCOVERED ||
                $slotState === Slot::THERE_IS_A_SHIP ||
                $slotState === Slot::SHIP_WAS_HIT) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }

    /**
     * Returns number of uncovered slots from above
     * of our checked slot
     *
     * @param array $slots Array with slots
     * @param int   $x     X-coordinate of checked slot
     * @param int   $y     Y-coordinate of checked slot
     *
     * @return int
     */
    private function getAmountOfTopUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for ($j = $y - 1; $j >= 0; $j--) {
            $slotState = $slots[$x][$j]->getState();

            if ($slotState === Slot::SLOT_IS_UNCOVERED ||
                $slotState === Slot::THERE_IS_A_SHIP ||
                $slotState === Slot::SHIP_WAS_HIT) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }

    /**
     * Returns number of uncovered slots from below
     * of our checked slot
     *
     * @param array $slots Array with slots
     * @param int   $x     X-coordinate of checked slot
     * @param int   $y     Y-coordinate of checked slot
     *
     * @return int
     */
    private function getAmountOfBottomUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for ($j = $y + 1; $j < Field::HEIGT; $j++) {
            $slotState = $slots[$x][$j]->getState();

            if ($slotState === Slot::SLOT_IS_UNCOVERED ||
                $slotState === Slot::THERE_IS_A_SHIP ||
                $slotState === Slot::SHIP_WAS_HIT) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }
}
