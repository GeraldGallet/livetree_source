<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;
  class FiltreReservationBorne
  {
	 protected $date_start;
	 protected $date_end;
	 protected $charge;
	 protected $id_place;
	 protected $id_user;

	 function getDateStart(){
		 return $this->date_start;
	 }
	 function setDateStart($date_start){
		 $this->date_start=$date_start;
	 }

   function getDateEnd(){
		 return $this->date_end;
	 }
	 function setDateEnd($date_end){
		 $this->date_end = $date_end;
	 }

	  function getCharge(){
		 return $this->charge;
	 }
	 function setCharge($charge){
		 $this->charge=$charge;
	 }

	  function getIdPlace(){
		 return $this->id_place;
	 }
	 function setIdPlace($id_place){
		 $this->id_place=$id_place;
	 }

	   function getIdUser(){
		 return $this->id_user;
	 }
	 function setIdUser($id_user){
		 $this->id_user=$id_user;
	 }
  }
?>
