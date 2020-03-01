<?php


namespace Src\Entities;


use DateTime;

class Croissantage
{
    private $id;
    private $idCed;
    private $idCer;
    private $dateC;
    private $dateCommand;
    private $deadline;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Croissantage
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdCed()
    {
        return $this->idCed;
    }

    /**
     * @param mixed $idCed
     * @return Croissantage
     */
    public function setIdCed($idCed)
    {
        $this->idCed = $idCed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdCer()
    {
        return $this->idCer;
    }

    /**
     * @param mixed $idCer
     * @return Croissantage
     */
    public function setIdCer($idCer)
    {
        $this->idCer = $idCer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateC()
    {
        return $this->dateC;
    }

    public function getFormatedDate()
    {
        $res = $this->dateC;

        try {
            $date = new DateTime($this->dateC);
            $res = $date->format('d/m/Y');
        } catch (\Exception $e) {
        }

        return $res;
    }

    /**
     * @param mixed $dateC
     * @return Croissantage
     */
    public function setDateC($dateC)
    {
        $this->dateC = $dateC;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCommand()
    {
        return $this->dateCommand;
    }

    public function getFormatedDateCommand()
    {
        $res = $this->dateCommand;

        try {
            $date = new DateTime($this->dateCommand);
            $res = $date->format('d/m/Y');
        } catch (\Exception $e) {
        }

        return $res;
    }

    /**
     * @param mixed $dateCommand
     * @return Croissantage
     */
    public function setDateCommand($dateCommand)
    {
        $this->dateCommand = $dateCommand;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    public function getFormatedDeadline()
    {
        $res = $this->deadline;

        try {
            $date = new DateTime($this->deadline);
            $res = $date->format('d/m/Y');
        } catch (\Exception $e) {
        }

        return $res;
    }

    /**
     * @param mixed $deadline
     * @return Croissantage
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
        return $this;
    }


}