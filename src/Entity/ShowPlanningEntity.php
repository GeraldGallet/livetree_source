<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;
  class ShowPlanningEntity
  {
	 /**
     * @Assert\NotBlank()
     */
	 protected $date_start;

   /**
     * @Assert\NotBlank()
     */
	 protected $date_end;

	 /**
     * @Assert\NotBlank()
     */
	 protected $id_place_planning;

	 function getDateStart() {
		 return $this->date_start;
	 }
	 function setDateStart($date_start) {
		 $this->date_start = $date_start;
	 }

   function getDateEnd() {
		 return $this->date_end;
	 }

	 function setDateEnd($date_end){
		 $this->date_end = $date_end;
	 }

	 function getIdPlacePlanning() {
		 return $this->id_place_planning;
	 }

	 function setIdPlacePlanning($id_place_planning) {
		 $this->id_place_planning = $id_place_planning;
	 }

  }
?>
