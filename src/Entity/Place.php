<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class Place {
    /**
    * @Assert\NotBlank()
    */
    protected $name;

    /**
    * @Assert\NotBlank()
    */
    protected $address;

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

    public function getAddress() {
      return $this->address;
    }

    public function setAddress($address) {
      $this->address = $address;
    }

    public function getIdFacility() {
      return $this->id_facility;
    }

    public function setIdFacility($id_facility) {
      $this->id_facility = $id_facility;
    }
  }

?>
