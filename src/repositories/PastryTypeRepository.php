<?php


namespace Src\Repositories;


use PDO as PDO;
use Src\Entities\PastryType;

class PastryTypeRepository extends BaseRepository
{
    public function __construct(PDO $bdd)
    {
        parent::__construct($bdd);
    }

    public function find(int $id) : ?PastryType
    {
        $this->requests['find']->execute([':id' => $id]);

        $res = $this->requests['find']->fetchObject(PastryType::class);

        return ($res !== false) ? $res : null;
    }

    public function findAll(): array
    {
        $this->requests['findAll']->execute();

        return $this->requests['findAll']->fetchAll(PDO::FETCH_CLASS, PastryType::class);
    }

    public function findAllAvailable() : array
    {
        $this->requests['findAllAvailable']->execute();

        return $this->requests['findAllAvailable']->fetchAll(PDO::FETCH_CLASS, PastryType::class);
    }

    protected function prepareStatements(): void
    {
        $this->requests['find'] = $this->bdd->prepare('SELECT * FROM pastrytype WHERE id = :id');
        $this->requests['findAll'] = $this->bdd->prepare('SELECT * FROM pastrytype');
        $this->requests['findAllAvailable'] = $this->bdd->prepare('SELECT * FROM pastrytype WHERE isAvailable IS TRUE');
    }
}