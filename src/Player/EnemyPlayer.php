<?php

namespace SeaBattle\Player;

use SeaBattle\Player\AI\IShootingAI;


class EnemyPlayer extends AbstractPlayer
{
    protected IShootingAI $AI;


    public function __construct(IShootingAI $AI)
    {
        parent::__construct();
        $this->AI = $AI;
    }


    public function getCoordsForShooting(): array
    {
        return [];
    }
}