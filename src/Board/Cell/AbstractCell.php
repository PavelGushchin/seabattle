<?php

namespace SeaBattle\Board\Cell;


abstract class AbstractCell
{
    protected $state;

    abstract public function getState();
    abstract public function setState($state);
}
