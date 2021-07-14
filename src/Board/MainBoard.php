<?php


namespace SeaBattle\Board;


use SeaBattle\Ship\Ship;

class MainBoard extends Board
{
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
     * This method randomly places ships on Board.
     *
     * First, it choose random x and y coordinate for ship's head.
     * Second, it checks if ship can be located there: if can -
     * it places the ship on Board, but if can not - it chooses
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

        echo "</table>";
    }

}