<?php

namespace SeaBattle\Ship;


class Ship
{
    const HORIZONTAL = 0;
    const VERTICAL = 1;

    private $id;
    private $isDead;
    private $size;
    private $hits = 0;
    private $direction;
    private $startX;
    private $startY;
    private $endX;
    private $endY;


    public function __construct($id, $size)
    {
        $this->id = $id;
        $this->size = $size;
    }


    public function addHitAndCheckForDeath()
    {
        $this->hits++;

        if ($this->size === $this->hits) {
            $this->isDead = true;
            return true;
        }

        return false;
    }


    public function getDirection()
    {
        return $this->direction;
    }


    public function setDirection($direction)
    {
        $this->direction = $direction;
    }


    public function getId()
    {
        return $this->id;
    }


    public function getStartX()
    {
        return $this->startX;
    }


    public function setStartX($startX)
    {
        $this->startX = $startX;
    }


    public function getStartY()
    {
        return $this->startY;
    }


    public function setStartY($startY)
    {
        $this->startY = $startY;
    }


    public function getEndX()
    {
        return $this->endX;
    }


    public function setEndX($endX)
    {
        $this->endX = $endX;
    }


    public function getEndY()
    {
        return $this->endY;
    }


    public function setEndY($endY)
    {
        $this->endY = $endY;
    }



    public function getSize()
    {
        return $this->size;
    }


    public function setSize($size)
    {
        $this->size = $size;
    }


    public function getHits()
    {
        return $this->hits;
    }

}