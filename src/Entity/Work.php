<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class Work {

    protected $id_user;

    /**
     * @Assert\NotBlank()
     */
    protected $id_facility;


    public function getIdUser() {
      return $this->id_user;
    }

    public function setIdUser($id_user) {
      $this->id_user = $id_user;
    }

    public function getIdFacility() {
      return $this->id_facility;
    }

    public function setIdFacility($id_facility) {
      $this->id_facility = $id_facility;
    }
  }

?>
