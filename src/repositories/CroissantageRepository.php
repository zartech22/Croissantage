<?php


namespace Src\Repositories;


use PDO;
use Src\Entities\Croissantage;

class CroissantageRepository extends BaseRepository
{
    private StudentRepository $studentModel;

    public function __construct(PDO $bdd, StudentRepository $studentModel)
    {
        parent::__construct($bdd);

        $this->studentModel = $studentModel;
    }

    public function find(int $id) : ?Croissantage
    {
        $req = $this->requests['find'];
        $req->execute([':id' => $id]);

        $res = $req->fetchObject(Croissantage::class);

        $this->fillStudents($res);

        return ($res !== false) ? $res : null;
    }

    public function findAll(): array
    {
        $req = $this->requests['findAll'];
        $req->execute();

        $res = $req->fetchAll(PDO::FETCH_CLASS,Croissantage::class);

        foreach ($res as $c)
            $this->fillStudents($c);

        return $res;
    }

    public function findAllForVote() : array
    {
        $req = $this->requests['findAllForVote'];

        $req->execute();
        $res = $req->fetchAll(PDO::FETCH_CLASS, Croissantage::class);

        foreach ($res as $c)
            $this->fillStudents($c);

        return $res;
    }

    public function persist(Croissantage $c) : void
    {
        $req = $this->requests['persist'];

        $data = [
            ':croissanted' => $c->getIdCed()->getId(),
            ':croissanter' => $c->getIdCer()->getId(),
            ':date' => $c->getDateC()->format('Y-m-d h:i:s'),
            ':dateCommand' => $c->getDateCommand()->format('Y-m-d h:i:s'),
            ':dateDelivery' => $c->getDeadline()->format('Y-m-d')
        ];

        $req->execute($data);
    }

    protected function prepareStatements(): void
    {
        $this->requests['find'] = $this->bdd->prepare('SELECT * FROM croissantage WHERE id = :id');
        $this->requests['findAll'] = $this->bdd->prepare('SELECT * FROM croissantage');
        $this->requests['findAllForVote'] = $this->bdd->prepare('SELECT * FROM croissantage WHERE dateCommand >= NOW()');

        $this->requests['persist'] = $this->bdd->prepare('INSERT INTO croissantage(idCed, idCer, dateC, dateCommand, deadline) 
                VALUES (:croissanted, :croissanter, :date, :dateCommand, :dateDelivery)');

        $this->requests['maxCroissanter'] = $this->bdd->prepare('SELECT idCer, COUNT(*) AS total FROM croissantage GROUP BY idCer ORDER BY total DESC LIMIT 1');
        $this->requests['maxCroissanted'] = $this->bdd->prepare('SELECT idCed, COUNT(*) AS total FROM croissantage GROUP BY idCed ORDER BY total DESC LIMIT 1');

        $this->requests['minCroissanter'] = $this->bdd->prepare('SELECT idCer, COUNT(*) AS total FROM croissantage GROUP BY idCer ORDER BY total LIMIT 1');
        $this->requests['minCroissanted'] = $this->bdd->prepare('SELECT idCed, COUNT(*) AS total FROM croissantage GROUP BY idCed ORDER BY total LIMIT 1');

        $this->requests['total'] = $this->bdd->prepare('SELECT COUNT(*) AS total FROM croissantage');
    }

    private function fillStudents(Croissantage $c)
    {
        $croissanter = $c->getIdCer();
        $croissanted = $c->getIdCed();

        if($croissanter !== null)
            $c->setIdCer($this->studentModel->find($croissanter));
        if($croissanted !== null)
            $c->setIdCed($this->studentModel->find($croissanted));
    }
}