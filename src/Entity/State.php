<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;

  class State
  {
    protected $front;
    protected $back;
    protected $left_side;
    protected $right_side;
    protected $inside;
    protected $commentary;
    protected $id_resa;
    protected $id_state;

    public function getFront() {
      return $this->front;
    }

    public function setFront($front) {
      $this->front = $front;
    }

    public function getBack() {
      return $this->back;
    }

    public function setBack($back) {
      $this->back = $back;
    }

    public function getLeftSide() {
      return $this->left_side;
    }

    public function setLeftSide($left_side) {
      $this->left_side = $left_side;
    }

    public function getRightSide() {
      return $this->right_side;
    }

    public function setRightSide($right_side) {
      $this->right_side = $right_side;
    }

    public function getInside() {
      return $this->inside;
    }

    public function setInside($inside) {
      $this->inside = $inside;
    }

    public function getCommentary() {
      return $this->commentary;
    }

    public function setCommentary($commentary) {
      $this->commentary = $commentary;
    }

    public function getIdResa() {
      return $this->id_resa;
    }

    public function setIdResa($id_resa) {
      $this->id_resa = $id_resa;
    }

    public function getIdState() {
      return $this->id_state;
    }

    public function setIdState($id_state) {
      $this->id_state = $id_state;
    }
  }

?>
