<?php
/**
 * Created by PhpStorm.
 * User: ISEN
 * Date: 07/04/2018
 * Time: 01:46
 */

namespace App\Entity;


class Reservation
{

    /**
     * @Assert\NotBlank()
     */
    protected $startdatetime;
    /**
     * @Assert\NotBlank()
     */
    protected $stopdatetime;
    /**
     * @Assert\NotBlank()
     */
    protected $reason_proposition;
    /**
     * @Assert\NotBlank()
     */
    protected $reason_description;

    /**
     * @return mixed
     */
    public function getStartdatetime()
    {
        return $this->startdatetime;
    }

    /**
     * @param mixed $startdatetime
     */
    public function setStartdatetime($startdatetime): void
    {
        $this->startdatetime = $startdatetime;
    }

    /**
     * @return mixed
     */
    public function getStopdatetime()
    {
        return $this->stopdatetime;
    }

    /**
     * @param mixed $stopdatetime
     */
    public function setStopdatetime($stopdatetime): void
    {
        $this->stopdatetime = $stopdatetime;
    }

    /**
     * @return mixed
     */
    public function getReasonProposition()
    {
        return $this->reason_proposition;
    }

    /**
     * @param mixed $reason_proposition
     */
    public function setReasonProposition($reason_proposition): void
    {
        $this->reason_proposition = $reason_proposition;
    }

    /**
     * @return mixed
     */
    public function getReasonDescription()
    {
        return $this->reason_description;
    }

    /**
     * @param mixed $reason_description
     */
    public function setReasonDescription($reason_description): void
    {
        $this->reason_description = $reason_description;
    }



}