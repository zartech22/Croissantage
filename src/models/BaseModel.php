<?php
namespace Src\Models;

class BaseModel
{
    /**
     * @var \PDO
     */
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
}