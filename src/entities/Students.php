<?php

namespace Src\Entities;

class Students
{
    private  $id;
    private  $login;
    private  $alias;
    private  $pwd;
    private  $defaultPastry;

    public static function createStudent(string $login, string $alias, string $pwd) : Students
    {
        $student = new Students();

        $student->setLogin($login);
        $student->setAlias($alias);
        $student->setRawPwd($pwd);

        return $student;
    }

    public static function createFromArray(array $data) : ?Students
    {
        $student = null;

        if(isset($data['login']) &&
        isset($data['alias']) &&
        isset($data['pwd']))
        {
            $student = Students::createStudent($data['login'], $data['alias'], $data['pwd']);
        }

        return $student;
    }

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Students
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogin() : string
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     * @return Students
     */
    public function setLogin(string $login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlias() : string
    {
        return htmlspecialchars($this->alias);
    }

    /**
     * @param mixed $alias
     * @return Students
     */
    public function setAlias(string $alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPwd() : string
    {
        return $this->pwd;
    }

    /**
     * @param mixed $pwd
     * @return Students
     */
    public function setPwd(string $pwd)
    {
        $this->pwd = $pwd;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultPastry()
    {
        return $this->defaultPastry;
    }

    /**
     * @param mixed $defaultPastry
     * @return Students
     */
    public function setDefaultPastry($defaultPastry)
    {
        $this->defaultPastry = $defaultPastry;
        return $this;
    }

    public function setRawPwd(string $password) : void
    {
        $this->pwd = md5($password);
    }

    public function getHashedPwd() : string
    {
        return md5($this->pwd);
    }
}