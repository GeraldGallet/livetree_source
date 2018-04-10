<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class Facility
  {
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
    protected $complementary;

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

    public function getComplementary() {
      return $this->complementary;
    }

    public function setComplementary($complementary) {
      $this->complementary = $complementary;
    }
  }

?>
