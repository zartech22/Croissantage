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

    public function persist(PastryType $type) : void
    {
        $req = $this->requests['persist'];

        $data = [
            ':name' => htmlspecialchars($type->getName()),
            ':isAvailable' => $type->getIsAvailable()
        ];

        $req->execute($data);
    }

    public function update(PastryType $type)
    {
        $req = $this->requests['update'];

        $data = [
            ':id' => $type->getId(),
            ':name' => htmlspecialchars($type->getName()),
            ':isAvailable' => $type->getIsAvailable()
        ];

        $req->execute($data);
    }

    protected function prepareStatements(): void
    {
        $this->requests['find'] = $this->bdd->prepare('SELECT * FROM pastrytype WHERE id = :id');
        $this->requests['findAll'] = $this->bdd->prepare('SELECT * FROM pastrytype');
        $this->requests['findAllAvailable'] = $this->bdd->prepare('SELECT * FROM pastrytype WHERE isAvailable IS TRUE');

        $this->requests['persist'] = $this->bdd->prepare('INSERT INTO pastrytype(name, isAvailable) VALUES (:name, :isAvailable)');

        $this->requests['update'] = $this->bdd->prepare('UPDATE pastrytype SET name = :name, isAvailable = :isAvailable WHERE id = :id');
    }
}