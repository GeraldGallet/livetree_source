<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class CompanyCar {
    /**
    * @Assert\NotBlank()
    */
    protected $name;

    /**
     * @Assert\NotBlank()
     */
    protected $model;

    /**
     * @Assert\NotBlank()
     */
    protected $power;


    /**
     * @Assert\NotBlank()
     */
    protected $id_facility;

    public function getName() {
      return $this->name;
    }

    public function setName($name) {
      $this->name = $name;
    }

    public function getModel() {
      return $this->model;
    }

    public function setModel($model) {
      $this->model = $model;
    }

    public function getPower() {
      return $this->power;
    }

    public function setPower($power) {
      $this->power = $power;
    }

    public function getIdFacility() {
      return $this->id_facility;
    }

    public function setIdFacility($id_facility) {
      $this->id_facility = $id_facility;
    }
  }

?>
