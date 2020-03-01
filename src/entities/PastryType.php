<?php


namespace Src\Entities;


class PastryType
{
    private $id;
    private $name;
    private $isAvailable;

    public function __construct() {}

    /**
     * @return mixed
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return PastryType
     */
    public function setId(int $id) : PastryType
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return PastryType
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsAvailable() : bool
    {
        return $this->isAvailable;
    }

    /**
     * @param mixed $isAvailable
     * @return PastryType
     */
    public function setIsAvailable(bool $isAvailable)
    {
        $this->isAvailable = $isAvailable;
        return $this;
    }
}