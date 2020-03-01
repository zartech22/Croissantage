<?php


namespace Src\Repositories;


use PDO;
use Src\Entities\Croissantage;
use Src\Entities\Students;

class VoteRepository extends BaseRepository
{
    public function __construct(PDO $bdd)
    {
        parent::__construct($bdd);
    }

    public function find(int $id) : array
    {
        $req = $this->requests['find'];

        $req->execute([':id' => $id]);
        $res = $req->fetch(\PDO::FETCH_ASSOC);

        return ($res !== false) ? $res : [];
    }

    public function findAll(): array
    {
        $req = $this->requests['findAll'];

        $req->execute();
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function hasVoted(Croissantage $c, Students $students) : bool
    {
        $req = $this->requests['hasVoted'];

        $req->execute([
            ':croissantage' => $c->getId(),
            ':student' => $students->getId()
        ]);

        return ($req->fetch(\PDO::FETCH_ASSOC) !== false);
    }

    protected function prepareStatements(): void
    {
        $this->requests['find'] = $this->bdd->prepare('SELECT * FROM currentcommand WHERE id = :id');
        $this->requests['findAll'] = $this->bdd->prepare('SELECT * FROM currentcommand');
        $this->requests['hasVoted'] = $this->bdd->prepare('SELECT * FROM currentcommand WHERE idCroissantage = :croissantage AND idStudent = :student');
    }
}