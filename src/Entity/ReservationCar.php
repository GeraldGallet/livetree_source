<?php
  namespace App\Entity;

  use Symfony\Component\Validator\Constraints as Assert;
  class ReservationCar
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
	 protected $start_time;

	 /**
     * @Assert\NotBlank()
     */
	 protected $end_time;

	 /**
     * @Assert\NotBlank()
     */
   protected $km_planned;


   /**
   * @Assert\NotBlank()
   */
   protected $id_company_car;

   /**
   * @Assert\NotBlank()
   */
   protected $id_reason;

   protected $id_user;
   protected $km_start;
   protected $km_end;
   protected $id_resa;
   protected $id_state;
   protected $reason_details;


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

	 function getStartTime() {
		 return $this->start_time;
	 }

	 function setStartTime($start_time) {
		 $this->start_time = $start_time;
	 }

	 function getEndTime() {
		 return $this->end_time;
	 }

	 function setEndTime($end_time) {
		 $this->end_time = $end_time;
	 }

	 function getKmPlanned() {
		 return $this->km_planned;
	 }

	 function setKmPlanned($km_planned) {
		 $this->km_planned = $km_planned;
	 }

   function getKmStart() {
		 return $this->km_start;
	 }

	 function setKmStart($km_start) {
		 $this->km_start = $km_start;
	 }

   function getKmEnd() {
		 return $this->km_end;
	 }

	 function setKmEnd($km_end) {
		 $this->km_end = $km_end;
	 }

	 function getIdCompanyCar() {
		 return $this->id_company_car;
	 }

	 function setIdCompanyCar($id_company_car){
		 $this->id_company_car = $id_company_car;
	 }

	 function getIdUser() {
		 return $this->id_user;
	 }

	 function setIdUser($id_user) {
		 $this->id_user = $id_user;
	 }

   function getIdReason() {
		 return $this->id_reason;
	 }

	 function setIdReason($id_reason) {
		 $this->id_reason = $id_reason;
	 }

   function getIdResa() {
		 return $this->id_resa;
	 }

	 function setIdResa($id_resa) {
		 $this->id_resa = $id_resa;
	 }

   function getIdState() {
		 return $this->id_state;
	 }

	 function setIdState($id_state) {
		 $this->id_state = $id_state;
	 }

   function getReasonDetails() {
		 return $this->reason_details;
	 }

	 function setReasonDetails($reason_details) {
		 $this->reason_details = $reason_details;
	 }

  }
?>
