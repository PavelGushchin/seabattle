<?php

/*
 * This file is part of the SeaBattle package.
 *
 * (c) Pavel Gushchin <pavel_gushchin@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SeaBattle\Ship;

/**
 * This class contains all properties and methods related to ships.
 *
 * @author Pavel Gushchin <pavel_gushchin@mail.ru>
 */
class Ship
{
    /**
     * @var int Represents horizontal positioning of the ship
     */
    const HORIZONTAL = 0;

    /**
     * @var int Represents vertical position of the ship
     */
    const VERTICAL = 1;

    /**
     * @var int Identification number of the ship
     */
    protected $id;

    /**
     * @var bool Is ship dead?
     */
    protected $isDead;

    /**
     * @var int Ship's size
     */
    protected $size;

    /**
     * @var int Number of times the ship was hit
     */
    protected $hits = 0;

    /**
     * @var int Direction of the ship (horizontal or vertical)
     */
    protected $direction;

    /**
     * @var int X-coordinate of the ship's head
     */
    protected $startX;

    /**
     * @var int Y-coordinate of the ship's head
     */
    protected $startY;

    /**
     * @var int X-coordinate of the ship's tail
     */
    protected $endX;

    /**
     * @var int Y-coordinate of the ship's tail
     */
    protected $endY;


    /**
     * Ship constructor.
     *
     * @param int $id   Ship's id
     * @param int $size Ship's size
     */
    public function __construct($id, $size)
    {
        $this->id = $id;
        $this->size = $size;
    }

    /**
     * This method invoked after ship was hit
     *
     * It checks if ship was killed or not
     *
     * @return bool
     */
    public function addHitAndCheckForDeath()
    {
        $this->hits++;

        if ($this->size === $this->hits) {
            $this->isDead = true;

            return true;
        }

        return false;
    }

    /**
     * Returns ship's direction
     *
     * @return int
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Sets ship's direction
     *
     * @param int $direction
     */
    public function setDirection($direction = self::HORIZONTAL)
    {
        $this->direction = $direction;
    }

    /**
     * Returns ship's identification number
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns x-coordinate of the ship's head
     *
     * @return int
     */
    public function getStartX()
    {
        return $this->startX;
    }

    /**
     * Sets x-coordinate of the ship's head
     *
     * @param int $startX
     */
    public function setStartX($startX)
    {
        $this->startX = $startX;
    }

    /**
     * Returns y-coordinate of the ship's head
     *
     * @return int
     */
    public function getStartY()
    {
        return $this->startY;
    }

    /**
     * Sets y-coordinate of the ship's head
     *
     * @param int $startY
     */
    public function setStartY($startY)
    {
        $this->startY = $startY;
    }

    /**
     * Returns x-coordinate of the ship's tail
     *
     * @return int
     */
    public function getEndX()
    {
        return $this->endX;
    }

    /**
     * Sets x-coordinate of the ship's tail
     *
     * @param int $endX
     */
    public function setEndX($endX)
    {
        $this->endX = $endX;
    }

    /**
     * Returns y-coordinate of the ship's tail
     *
     * @return int
     */
    public function getEndY()
    {
        return $this->endY;
    }

    /**
     * Sets y-coordinate of the ship's tail
     *
     * @param int $endY
     */
    public function setEndY($endY)
    {
        $this->endY = $endY;
    }

    /**
     * Returns size of the ship
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets size of the ship
     *
     * @param int $size
     */
    public function setSize($size = 1)
    {
        $this->size = $size;
    }

    /**
     * Returns number of times the ship was hit
     *
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Returns 'true' if ship is dead and 'false' otherwise
     *
     * @return bool
     */
    public function isDead()
    {
        return $this->isDead;
    }
}
