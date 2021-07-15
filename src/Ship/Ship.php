<?php

namespace SeaBattle\Ship;


class Ship
{
    const HORIZONTAL = 0;
    const VERTICAL = 1;

    protected int $id;
    protected bool $isDead = false;
    protected int $size;
    protected int $hits = 0;

    /**
     * @var string Direction of the ship (one of
     *          Ship::HORIZONTAL or
     *          Ship::VERTICAL)
     */
    protected string $direction;

    /**
     * @var int X-coordinate of the ship's head
     */
    protected int $startX;

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

        if ($this->size <= $this->hits) {
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
     * Sets ship's hits
     *
     * @param int $hits Number of times the ship was hit
     */
    public function setHits($hits = 0)
    {
        $this->hits = $hits;
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
