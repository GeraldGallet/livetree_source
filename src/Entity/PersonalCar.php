<?php
  // src/Entity/Task.php
  namespace App\Entity;

  class PersonalCar
  {
      protected $name;
      protected $model;
      protected $power;

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
  }

?>
