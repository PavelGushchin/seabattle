<?php


namespace SeaBattle\Player;


use SeaBattle\Player\AI\AIInterface;

class EnemyPlayer extends AbstractPlayer
{
    protected $AI;

    public function __construct($AI = null)
    {
        $this->AI = $AI;
    }

}