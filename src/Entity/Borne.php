<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class Borne {
    /**
    * @Assert\NotBlank()
    */
    protected $name;

    /**
    * @Assert\NotBlank()
    */
    protected $place;

    /**
    * @Assert\NotBlank()
    */
    protected $id_place;

    public function getName() {
      return $this->name;
    }

    public function setName($name) {
      $this->name = $name;
    }

    public function getPlace() {
      return $this->place;
    }

    public function setPlace($place) {
      $this->place = $place;
    }

    public function getIdPlace() {
      return $this->id_place;
    }

    public function setIdPlace($id_place) {
      $this->id_place = $id_place;
    }
  }

?>
