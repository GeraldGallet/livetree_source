<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;
  class FiltreReservationBorne
  {
	 protected $date_start;
	 protected $end_time;
	 protected $charge;
	 protected $id_place;
	 protected $id_user;

   public function __construct()
   {
     $this->date = new \DateTime("now");
   }

	 function getDateStart(){
		 return $this->date_start;
	 }
	 function setDateStart($date_start){
		 $this->date_start=$date_start;
	 }

	 function getStartTime(){
		 return $this->start_time;
	 }
	 function setStartTime($start_time){
		 $this->start_time=$start_time;
	 }

	  function getEndTime(){
		 return $this->end_time;
	 }
	 function setEndTime($end_time){
		 $this->end_time=$end_time;
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
