<?php
  // src/Entity/Task.php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class PersonalCar
  {
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
      protected $id_user;

      public function getName()
      {
          return $this->name;
      }

      public function setName($name)
      {
          $this->name = $name;
      }

      public function getModel()
      {
          return $this->model;
      }

      public function setModel($model)
      {
          $this->model = $model;
      }

      public function getPower()
      {
          return $this->power;
      }

      public function setPower($power)
      {
          $this->power = $power;
      }

      public function getIdUser()
      {
          return $this->id_user;
      }

      public function setIdUser($id_user)
      {
          $this->id_user = $id_user;
      }
  }

?>
