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
<html>
<head>
	<title>jQuery UI Slider - Range slider</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
    <script>
    $( function() {
    $( "#datepicker" ).datepicker({ minDate: -0, });
  } );
  </script>
  <script>
  function () 
		{
			$('#champ').hide();
		
		 alert("$('#afficherchamp option:selected').val()");
		 ("champ").show;
	 }
	 
		}
</script>
</head>
<body>
	<center>
	<form method="get" action="reservation_vehicule">
	<p>Date: <input type="text" id="datepicker" name="date" required><br/></p>
	Heure de début : <input type="time" name="heure_debut" value=""  class="reservation_borne"  required><br/>
	Durée <input type="number" name="heure_fin" min="<?php echo $depart; ?>" class="reservation_borne" required>
	<input type="radio" name="jourheure" >
	<label for="jour"  > jour(s)</label>
	<input type="radio" name="jourheure"checked>
	<label for="jour"> heures(s)</label><br/>
	Kilomètre estimer <input type="number_format" name="kilometre" required> km<br/>
	Raison <select name="raison" id="raisons" required>
		<option value="déplacement1" id="choix1">déplacement1</option>
		<option value="déplacement2">déplacement2</option>
		<option value="déplacement3">déplacement3</option>
		<option value="autre" id="afficherchamp">Autre</option>
		</select><br/>
		<div id="afficherchamp"  >
		<input type="text"  id="champ"name="autreraison">
		</div>
	Voiture <select name="voiture" required>
		<option value="voiture1">Zoé1</option>
		<option value="voiture2">Zoé2</option>
		<option value="voiture3">Kangoo</option>
		<option value="voiture">Kangoo1</option>
		</select>
		
	
	
 
	
	<input type="submit" value="Réserver" id="reserver_vehicule">


</body>