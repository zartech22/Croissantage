<?php


namespace Src\Models;


final class AppModel extends BaseModel
{
    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function getStudents() : array
    {
        $req = $this->db->query('SELECT * FROM student;');

        $res = $req->fetchAll();

        return ($res !== false) ? $res : [];
    }
}