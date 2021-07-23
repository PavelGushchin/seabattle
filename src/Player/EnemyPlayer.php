<?php

namespace SeaBattle\Player;

use SeaBattle\Player\AI\InterfaceAI;


class EnemyPlayer extends AbstractPlayer
{
    protected InterfaceAI $AI;


    public function __construct(InterfaceAI $AI)
    {
        parent::__construct();
        $this->AI = $AI;
    }


    /**
     * Shooting is delegated to AI
     */
    public function getCoordsForShooting(): array
    {
        return $this->AI->getCoordsForShooting($this->shootingBoard);
    }


    public function setAI(InterfaceAI $AI): void
    {
        $this->AI = $AI;
    }
}
