<?php
session_start();
	$date=isset($_GET['date']) ? $_GET['date'] : ''  ;
	$arrive=isset($_GET['heure_debut']) ? $_GET['heure_debut'] : ''  ;
	$depart=isset($_GET['heure_fin']) ? $_GET['heure_fin'] : ''  ;
	$raison=isset($_GET['raison']) ? $_GET['raison'] : ''  ;
	if ($date !=''|| $arrive !=''|| $depart !=''){
			if ($date === '')
                echo"ERROR: Please enter the date!";
			if ($arrive === '')
                echo"ERROR: Please enter the arrive!";
			if ($depart === '')
                echo"ERROR: Please enter the depart!";
	}
?>
