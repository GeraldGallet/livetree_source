<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class ResaBorne {
    /**
    * @Assert\NotBlank()
    */
    protected $date_resa;

    /**
    * @Assert\NotBlank()
    */
    protected $start_time;

    /**
    * @Assert\NotBlank()
    */
    protected $end_time;

    /**
    * @Assert\NotBlank()
    */
    protected $charge;

    /**
    * @Assert\NotBlank()
    */
    protected $id_user;

    /**
    * @Assert\NotBlank()
    */
    protected $id_place;

    public function getDateResa() {
      return $this->date_resa;
    }

    public function setDateResa($date_resa) {
      $this->date_resa;
    }

  }
?>
