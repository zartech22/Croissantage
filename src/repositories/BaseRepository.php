<?php


namespace Src\Repositories;


use PDO as PDO;

abstract class BaseRepository
{
    protected PDO $bdd;

    /**
     * @var \PDOStatement[]
     */
    protected array $requests;

    protected function __construct(PDO $bdd)
    {
        $this->bdd = $bdd;
        $this->prepareStatements();
    }

    protected abstract function prepareStatements() : void;

    public abstract function find(int $id);
    public abstract function findAll() : array;
}