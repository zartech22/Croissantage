<?php


namespace Src\Repositories;


use PDO;
use Src\Entities\Rights;
use Src\Entities\Students;

class RightsRepository extends BaseRepository
{
    const ROLES = [
        1 => 'Admin',
        2 => 'BDE',
        3 => 'Délégué',
        4 => ''
    ];

    public function __construct(PDO $bdd)
    {
        parent::__construct($bdd);
    }

    public function find(int $id) : ?Rights
    {
        $req = $this->requests['find'];
        $req->execute([':id' => $id]);

        $res = $req->fetchObject(Rights::class);

        return ($res !== false) ? $res : null;
    }

    public function findAll(): array
    {
        $req = $this->requests['findAll'];
        $req->execute();

        return $req->fetchAll(PDO::FETCH_CLASS, Rights::class);
    }

    public function findByStudent(Students $students) : ?Rights
    {
        $req = $this->requests['findByStudent'];

        $req->execute([':student' => $students->getId()]);
        $res = $req->fetchObject(Rights::class);

        return ($res !== false) ? $res : null;
    }

    public function hasRights(Students $students) : bool
    {
        return ($this->findByStudent($students) !== null);
    }

    public function createEmptyRights(Students $students) : void
    {
        $req = $this->requests['persistEmptyRights'];

        $req->execute([
            ':student' => $students->getId(),
            ':role' => self::ROLES[4]
        ]);
    }

    public function updateRightByStudent(int $studentId, int $roleId) : void
    {
        if(!in_array($roleId, array_keys(self::ROLES)))
        {
            throw new \Exception('Invalid role id!');
        }

        $req = $this->requests['updateByStudent'];

        $req->execute([
            ':student' => $studentId,
            ':role' => self::ROLES[$roleId]
        ]);
    }

    protected function prepareStatements(): void
    {
        $this->requests['find'] = $this->bdd->prepare('SELECT * FROM rights WHERE id = :id');
        $this->requests['findAll'] = $this->bdd->prepare('SELECT * FROM rights');
        $this->requests['findByStudent'] = $this->bdd->prepare('SELECT * FROM rights WHERE idS = :student');

        $this->requests['updateByStudent'] = $this->bdd->prepare('UPDATE rights SET role = :role WHERE idS = :student');

        $this->requests['persistEmptyRights'] = $this->bdd->prepare('INSERT INTO rights(idS, role) VALUES(:student, :role)');
    }
}