<?php

namespace SeaBattle\Player\AI\PlacingShipsAI;

use SeaBattle\Board\Board;


interface InterfacePlacingShipsAI
{
    public function placeShipsOnBoard(Board $board);
}
