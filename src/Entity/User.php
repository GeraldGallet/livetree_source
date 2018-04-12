<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class User
  {
    /**
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     */
    protected $password;

    /**
     * @Assert\NotBlank()
     */
    protected $phone_number;

    /**
     * @Assert\NotBlank()
     */
    protected $first_name;

    /**
     * @Assert\NotBlank()
     */
    protected $last_name;

    /**
     * @Assert\NotBlank()
     */
    protected $id_status;

    /**
     * @Assert\NotBlank()
     */
    protected $indicative;
    protected $activated;

    function getEmail() {
      return $this->email;
    }

    function setEmail($email) {
      $this->email = $email;
    }

    function getPassword() {
      return $this->password;
    }

    function setPassword($password) {
      $this->password = $password;
    }

    function getPhoneNumber() {
      return $this->phone_number;
    }

    function setPhoneNumber($phone_number) {
      $this->phone_number = $phone_number;
    }

    function getFirstName() {
      return $this->first_name;
    }

    function setFirstName($first_name) {
      $this->first_name = $first_name;
    }

    function getLastName() {
      return $this->last_name;
    }

    function setLastName($last_name) {
      $this->last_name = $last_name;
    }

    function getIdStatus() {
      return $this->id_status;
    }

    function setIdStatus($id_status) {
      $this->id_status = $id_status;
    }

    function getIndicative() {
      return $this->indicative;
    }

    function setIndicative($indicative) {
      $this->indicative = $indicative;
    }

    function getActivated() {
      return $this->activated;
    }

    function setActivated($activated) {
      $this->activated = $activated;
    }
  }

?>
