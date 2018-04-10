<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class Connection
  {
    /**
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     */
    protected $password;

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
  }

?>
