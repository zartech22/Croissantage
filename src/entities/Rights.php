<?php

namespace Src\Entities;

use Src\Repositories\RightsRepository;

class Rights
{
    private $id;
    private $ids;
    private $role;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Rights
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @param mixed $ids
     * @return Rights
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     * @return Rights
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function getRoleId() : ?int
    {
        return array_search($this->role, RightsRepository::ROLES);
    }
}