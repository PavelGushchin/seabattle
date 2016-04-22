<?php

namespace SeaBattle\AI;

use SeaBattle\Field\Field;
use SeaBattle\Field\Slot;


class ShootingWithStrategyAI implements IShootingAI
{
    const SHOOT_HORIZONTALLY = 0;
    const SHOOT_VERTICALLY = 1;
    const SHOOT_BIDIRECTIONALLY = 2;

    private $partsOfdamagedShip = [];
    private $variantsForNextShot = [];
    private $shootingDirection = self::SHOOT_BIDIRECTIONALLY;
//    private $randomShooter;
    private $valuesForSlots;


    public function __construct()
    {
//        $this->randomShooter = new RandomShootingAI();
    }


    public function calculateCoordsForShooting($slots, $ships = null)
    {
        $this->variantsForNextShot = [];
        $this->valuesForSlots = [];
        $this->setValuesForSlots($slots, $ships);


        if ( empty($this->partsOfdamagedShip) ) {
            $this->calculateAllVariantsForRandomShot($slots);
        } else {
            $this->calculateAllVariantsForSmartShot($slots);
        }


        shuffle($this->variantsForNextShot);

        usort($this->variantsForNextShot, function($a, $b) {
            return $a['value'] - $b['value'];
        });

        $coords = array_shift($this->variantsForNextShot);

        $x = $coords['x'];
        $y = $coords['y'];


        $slotState = $slots[$x][$y]->getState();

        if ($slotState === Slot::THERE_IS_A_SHIP) {
            $shipId = $slots[$x][$y]->getShipId();
            $ship = $ships[$shipId];

            $isShipGoingToDie = $ship->getSize() === $ship->getHits() + 1;

            if ($isShipGoingToDie) {
                $this->partsOfdamagedShip = [];
                $this->shootingDirection = self::SHOOT_BIDIRECTIONALLY;
            } else {
                $this->partsOfdamagedShip[] = [
                    'x' =>  $x,
                    'y' => $y
                ];

                if ( count($this->partsOfdamagedShip) === 2 ) {
                    $this->defineShootingDirection();
                }
            }
        }

        return $coords;
    }


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


    private function calculateAllVariantsForRandomShot($slots)
    {
        for($i = 0; $i < Field::WIDTH; $i++) {
            for($j = 0; $j < Field::HEIGT; $j++) {
                $isSlotAvailableForShooting = $slots[$i][$j]->getState() === (
                        Slot::SLOT_IS_UNCOVERED ||
                        Slot::THERE_IS_A_SHIP
                    );

                if($isSlotAvailableForShooting) {
                    $this->variantsForNextShot[] = [
                        'x' => $i,
                        'y' => $j,
                        'value' => $this->valuesForSlots[$i][$j]
                    ];
                }
            }
        }
    }

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
                    'value' => $this->valuesForSlots[$leftSlotX][$leftSlotY]
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
                    'value' => $this->valuesForSlots[$leftSlotX][$leftSlotY]
                ];
            }
        }
    }


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
                    'value' => $this->valuesForSlots[$leftSlotX][$leftSlotY]
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
                    'value' => $this->valuesForSlots[$leftSlotX][$leftSlotY]
                ];
            }
        }
    }


    private function setValuesForSlots($slots, $ships)
    {
        for($i = 0; $i < Field::WIDTH; $i++) {
            for ($j = 0; $j < Field::HEIGT; $j++) {
                $this->valuesForSlots[$i][$j] = 0;

                $leftUncoveredSlots = $this->getAmountOfLeftUncoveredSlots($slots, $i, $j);
                $rightUncoveredSlots = $this->getAmountOfRightUncoveredSlots($slots, $i, $j);
                $topUncoveredSlots = $this->getAmountOfTopUncoveredSlots($slots, $i, $j);
                $bottimUncoveredSlots = $this->getAmountOfBottomUncoveredSlots($slots, $i, $j);

                $horizontalUncoveredSlots = $leftUncoveredSlots + $rightUncoveredSlots + 1;
                $verticalUncoveredSlots = $topUncoveredSlots + $bottimUncoveredSlots + 1;

                foreach($ships as $ship) {
                    if(!$ship->isDead()) {
//                        $isShipCanFit = $ship->getSize() <= ($horizontalUncoveredSlots || $verticalUncoveredSlots);

                        if ($ship->getSize() <= $horizontalUncoveredSlots) {
                            $this->valuesForSlots[$i][$j] += $ship->getSize();
                        }
                        if ($ship->getSize() <= $verticalUncoveredSlots) {
                            $this->valuesForSlots[$i][$j] += $ship->getSize();
                        }
                    }
                }
            }

        }


    }


    private function getAmountOfLeftUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for($i = $y - 1; $i >= 0; $i--) {
            $isSlotAvailableForShooting = $slots[$x][$i]->getState() === (
                Slot::SLOT_IS_UNCOVERED ||
                Slot::THERE_IS_A_SHIP
            );

            if($isSlotAvailableForShooting) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }


    private function getAmountOfRightUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for($i = $x + 1; $i < Field::WIDTH; $i++) {
            $isSlotAvailableForShooting = $slots[$i][$y]->getState() === (
                    Slot::SLOT_IS_UNCOVERED ||
                    Slot::THERE_IS_A_SHIP
                );

            if($isSlotAvailableForShooting) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }


    private function getAmountOfTopUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for($j = $y - 1; $j >= 0; $j--) {
            $isSlotAvailableForShooting = $slots[$x][$j]->getState() === (
                    Slot::SLOT_IS_UNCOVERED ||
                    Slot::THERE_IS_A_SHIP
                );

            if($isSlotAvailableForShooting) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }


    private function getAmountOfBottomUncoveredSlots($slots, $x, $y)
    {
        $amount = 0;

        for($j = $y + 1; $j < Field::HEIGT; $j++) {
            $isSlotAvailableForShooting = $slots[$x][$j]->getState() === (
                    Slot::SLOT_IS_UNCOVERED ||
                    Slot::THERE_IS_A_SHIP
                );

            if($isSlotAvailableForShooting) {
                $amount++;
            } else {
                break;
            }
        }

        return $amount;
    }

}