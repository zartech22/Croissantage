<?php


namespace Src\Repositories;


use PDO;
use Src\Entities\PastryType;
use Src\Entities\Students;

class StudentRepository extends BaseRepository
{
    private PastryTypeRepository $pastryModel;
    private RightsRepository $rightsModel;

    public function __construct(PDO $bdd, PastryTypeRepository $pastryModel, RightsRepository $rightsModel)
    {
        parent::__construct($bdd);

        $this->pastryModel = $pastryModel;
        $this->rightsModel = $rightsModel;
    }

    public function find(int $id) : ?Students
    {
        $this->requests['find']->execute([':ident' => $id]);

        $res = $this->requests['find']->fetchObject( Students::class);

        if($res !== false)
            $this->fillDefaultPastry($res);

        return ($res !== false) ? $res : null;
    }

    public function findAll() : array
    {
        $this->requests['findAll']->execute();

        $res = $this->requests['findAll']->fetchAll(PDO::FETCH_CLASS, Students::class);

        foreach ($res as $student)
            $this->fillDefaultPastry($student);

        return $res;
    }

    public function findByLogin($login) : ?Students
    {
        $this->requests['findByLogin']->execute([':login' => $login]);

        $student = $this->requests['findByLogin']->fetchObject(Students::class);

        return ($student !== false) ? $student : null;
    }

    public function findAllWithDefaultPastry() : array
    {
        $req = $this->requests['findAllWithDefaultPastry'];

        $req->execute();

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllWithRights() : array
    {
        $students = $this->findAll();

        $res = [];

        foreach ($students as $student)
        {
            if(!$this->rightsModel->hasRights($student))
                $this->rightsModel->createEmptyRights($student);

            $res[] = [
                'student' => $student,
                'right' => $this->rightsModel->findByStudent($student)
            ];
        }

        return $res;
    }

    public function authenticate($login, $password) : bool
    {
        $this->requests['authenticate']->execute([
            ':login' => $login,
            ':password' => $password
        ]);

        return ($this->requests['authenticate']->fetchObject(Students::class) !== false);
    }

    public function isAdmin($id) : bool
    {
        $req = $this->requests['isAdmin'];

        $req->execute([':id' => $id]);

        return ($req->fetch(PDO::FETCH_ASSOC) !== false);
    }

    public function persist(Students $students) : void
    {
        $req = $this->requests['createUser'];

        $data = [
            ':login' => htmlspecialchars($students->getLogin()),
            ':alias' => htmlspecialchars($students->getAlias()),
            ':pwd' => $students->getPwd()
        ];

        $defaultPastry = $students->getDefaultPastry();

        if(is_int($defaultPastry) === true)
            $data[':defaultPastry'] = $defaultPastry;
        else if($defaultPastry instanceof PastryType)
            $data[':defaultPastry'] = $defaultPastry->getId();
        else
            $data[':defaultPastry'] = null;

        $req->execute($data);
    }

    public function update(Students $students) : void
    {
        $this->fillDefaultPastry($students);

        $req = $this->requests['updateUser'];

        $data = [
            ':id' => $students->getId(),
            ':login' => htmlspecialchars($students->getLogin()),
            ':alias' => htmlspecialchars($students->getAlias()),
            ':pwd' => $students->getPwd()
        ];

        $defaultPastry = $students->getDefaultPastry();

        if(is_int($defaultPastry) === true)
            $data[':defaultPastry'] = $defaultPastry;
        else if($defaultPastry instanceof PastryType)
            $data[':defaultPastry'] = $defaultPastry->getId();
        else
            $data[':defaultPastry'] = null;

        $req->execute($data);
    }

    protected function prepareStatements() : void
    {
        // ==================== SELECT ====================
        $this->requests['find']                     = $this->bdd->prepare('SELECT * FROM student WHERE id = :ident');
        $this->requests['findAll']                  = $this->bdd->prepare('SELECT * FROM student');
        $this->requests['findByLogin']              = $this->bdd->prepare('SELECT * FROM student WHERE login = :login');
        $this->requests['authenticate']             = $this->bdd->prepare('SELECT * FROM student WHERE login = :login AND pwd = :password');
        $this->requests['isAdmin']                  = $this->bdd->prepare("SELECT * FROM student INNER JOIN rights r on student.id = r.idS WHERE student.id = :id AND role = 'Admin'");
        $this->requests['findAllWithDefaultPastry'] = $this->bdd->prepare('SELECT student.id AS id, login, alias, pastrytype.name AS defaultPastry
            FROM student LEFT JOIN pastrytype ON defaultPastry = pastrytype.id ORDER BY id');

        // ==================== INSERT ====================
        $this->requests['createUser'] = $this->bdd->prepare('INSERT INTO student(login, alias, pwd, defaultPastry) VALUES(:login, :alias, :pwd, :defaultPastry)');

        // ==================== UPDATE ====================
        $this->requests['updateUser'] = $this->bdd->prepare('UPDATE student SET login = :login, alias = :alias, pwd = :pwd, defaultPastry = :defaultPastry WHERE id = :id');
    }

    private function fillDefaultPastry(Students $students) : void
    {
        $defaultPastry = $students->getDefaultPastry();

        if($defaultPastry !== null)
            $students->setDefaultPastry($this->pastryModel->find($defaultPastry));
    }
}