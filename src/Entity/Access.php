<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class Access {
    /**
    * @Assert\NotBlank()
    */
    protected $id_place;
    protected $user;

    public function getIdPlace() {
      return $this->id_place;
    }

    public function setIdPlace($id_place) {
      $this->id_place = $id_place;
    }

    public function getIdUser() {
      return $this->id_user;
    }

    public function setIdUser($id_user) {
      $this->id_user = $id_user;
    }
  }

?>
