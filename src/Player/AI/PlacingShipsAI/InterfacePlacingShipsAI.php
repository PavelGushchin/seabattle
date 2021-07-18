<?php

namespace SeaBattle\Player\AI\PlacingShipsAI;

use SeaBattle\Board\AbstractBoard;


interface InterfacePlacingShipsAI
{
    public function createShipsOnBoard(AbstractBoard $board);
}
