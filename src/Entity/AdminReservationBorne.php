<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;
  class AdminReservationBorne
  {
	 protected $date_creation;


	 protected $start_date;


	 protected $end_date;


	 protected $charge;


	 protected $id_place;
	 protected $id_user;
   protected $voitures;



	 function getDateCreation(){
		 return $this->date_creation;
	 }
	 function setDateCreation($date_creation){
		 $this->date_creation=$date_creation;
	 }

	 function getStartDate(){
		 return $this->start_date;
	 }
	 function setStartDate($start_date){
		 $this->start_date=$start_date;
	 }

	  function getEndDate(){
		 return $this->end_date;
	 }
	 function setEndDate($end_date){
		 $this->end_date=$end_date;
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
   function getVoitures(){
		 return $this->voitures;
	 }
	 function setVoitures($voitures){
		 $this->voitures=$voitures;
	 }
  }
?>
