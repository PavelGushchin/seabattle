<?php

namespace SeaBattle\Player;

use SeaBattle\Player\AI\ShootingAI\InterfaceShootingAI;


class EnemyPlayer extends AbstractPlayer
{
    protected InterfaceShootingAI $shootingAI;


    public function __construct(InterfaceShootingAI $shootingAI)
    {
        parent::__construct();
        $this->shootingAI = $shootingAI;
    }


    public function getCoordsForShooting(): array
    {
        return $this->shootingAI->getCoordsForShooting($this->shootingBoard);
    }


    public function setShootingAI(InterfaceShootingAI $shootingAI): void
    {
        $this->shootingAI = $shootingAI;
    }
}
