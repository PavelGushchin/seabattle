<?php


namespace SeaBattle\Board;


class ShootingBoard extends Board
{
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
            $y < 0 || $y >= self::HEIGHT) {
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
                $ship = $this->aliveShips[$shipId];

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

}