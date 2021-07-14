<?php

namespace SeaBattle\Player;

use SeaBattle\Player\AI\AIInterface;


class EnemyPlayer extends AbstractPlayer
{
    protected AIInterface $AI;


    public function __construct(AIInterface $AI)
    {
        parent::__construct();
        $this->AI = $AI;
    }


    public function getCoordsForShooting(): array
    {
        return [];
    }
}