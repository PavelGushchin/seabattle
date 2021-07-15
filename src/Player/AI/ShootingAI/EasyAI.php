<?php

namespace SeaBattle\Player\AI\ShootingAI;

use SeaBattle\Field\Field;
use SeaBattle\Field\Slot;


/**
 * SmartShootingAI represents algorithm which
 * shoots smartly (intermediate level)
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class EasyAI implements InterfaceShootingAI
{
    const SHOOT_HORIZONTALLY = 1;
    const SHOOT_VERTICALLY = 2;
    const SHOOT_BIDIRECTIONALLY = 3;

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
     * @var RandomShootingAI Contains instance of random shooting algorithm
     */
    private $randomShooter;


    /**
     * SmartShootingAI constructor.
     */
    public function __construct()
    {
        $this->randomShooter = new RandomShootingAI();
    }

    /**
     * Returns name of the algorithm
     *
     * @return string
     */
    public function __toString()
    {
        return 'Smart algorithm';
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
     * @param AbstractBoard|null $shootingBoard Array with ships
     *
     * @return array Array of shot coordinates
     */
    public function getCoordsForShooting(AbstractBoard $shootingBoard = null)
    {
        if (empty($this->partsOfdamagedShip)) {
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
            $ship = $shootingBoard[$shipId];

            $isShipGoingToDie = $ship->getSize() === $ship->getHits() + 1;

            if ($isShipGoingToDie) {
                $this->partsOfdamagedShip = [];
                $this->shootingDirection = self::SHOOT_BIDIRECTIONALLY;
            } else {
                $this->partsOfdamagedShip[] = [
                    'x' =>  $x,
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
     * When we have a wounded ship we have to shoot smartly
     * until we kill it
     *
     * This method calculates all possible variants for shooting
     *
     * @param array $slots Array with slots
     */
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
                ];
            }
        }
    }
}
