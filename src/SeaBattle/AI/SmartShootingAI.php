<?php

namespace SeaBattle\AI;

use SeaBattle\Field\Field;
use SeaBattle\Field\Slot;


class SmartShootingAI implements IShootingAI
{
    const SHOOT_HORIZONTALLY = 0;
    const SHOOT_VERTICALLY = 1;
    const SHOOT_BIDIRECTIONALLY = 2;

    private $partsOfdamagedShip = [];
    private $variantsForNextShot = [];
    private $shootingDirection = self::SHOOT_BIDIRECTIONALLY;
    private $randomShooter;


    public function __construct()
    {
        $this->randomShooter = new RandomShootingAI();
    }


    public function calculateCoordsForShooting($slots, $ships = null)
    {
        if ( empty($this->partsOfdamagedShip) ) {
            $coords = $this->randomShooter->calculateCoordsForShooting($slots);
        } else {
            $this->variantsForNextShot = [];
            $this->calculateAllVariantsForNextShot($slots);

            shuffle($this->variantsForNextShot);
            $coords = array_shift($this->variantsForNextShot);
        }

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


    private function calculateAllVariantsForNextShot($slots)
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
                    'y' => $leftSlotY
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
                    'y' => $rightSlotY
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
                    'y' => $topSlotY
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
                    'y' => $bottomSlotY
                ];
            }
        }
    }

    public function __toString()
    {
        return 'Smart algorithm';
    }

}