<?php
session_start();
	$currentdate2 = date('Y-m-d ');
	 
	$amount=isset($_GET['amount']) ? $_GET['amount'] : ''  ;
	$date=isset($_GET['date']) ? $_GET['date'] : ''  ;
	$arrive=isset($_GET['heure_debut']) ? $_GET['heure_debut'] : ''  ;
	$depart=isset($_GET['heure_fin']) ? $_GET['heure_fin'] : ''  ;
	
	
	if($amount == "100%" && $amount!='' ){
		 echo"ERROR: Please enter the level of charge!";
	}
	if ($date !=''|| $arrive !=''|| $depart !=''){
			if ($date === '')
                echo"ERROR: Please enter the date!";
			if ($arrive === '')
                echo"ERROR: Please enter the arrive!";
			if ($depart === '')
                echo"ERROR: Please enter the depart!";
	}
	
  
	



?>
