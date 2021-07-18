<?php

namespace SeaBattle\Player;

use SeaBattle\Player\AI\ShootingAI\InterfaceAI;


class EnemyPlayer extends AbstractPlayer
{
    protected InterfaceAI $shootingAI;


    public function __construct(InterfaceAI $shootingAI)
    {
        parent::__construct();
        $this->shootingAI = $shootingAI;
    }


    public function getCoordsForShooting(): array
    {
        return $this->shootingAI->getCoordsForShooting($this->shootingBoard);
    }


    public function setShootingAI(InterfaceAI $shootingAI): void
    {
        $this->shootingAI = $shootingAI;
    }
}
