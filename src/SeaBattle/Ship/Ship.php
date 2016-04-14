<?php

namespace SeaBattle\Ship;


class Ship
{
    private $id;
    private $isDead;
    private $size;
    private $hits;
    private $parts;
    private $direction;
    private $valid = false;

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param mixed $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    public function __construct($size)
    {
        $this->size = $size;
    }

    public function clearParts()
    {
        $this->parts = [];
    }

    public function addPart($x, $y)
    {
        $this->parts[] = $x . '-' . $y;
    }

    public function generateAndCheck($direction, $x, $y)
    {

    }
}