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
<html>
<head>
	<title>jQuery UI Slider - Range slider</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
	$( function() {
		$( "#slider-range" ).slider({
			min: 0,
			max: 100,
			step :1,
			values: [ 100 ],
			slide: function( event, ui ) {
				$( "#amount" ).val(  ui.values[ 0 ] +"%" );
				}
			});
		$( "#amount" ).val(  $( "#slider-range" ).slider( "values", 0 )+"%"  );
	} );
  </script>
    <script>
    $( function() {
    $( "#datepicker" ).datepicker({ minDate: -0, });
  } );
  </script>
</head>
<body>
	<center>
	<form method="get" action="reservation_borne">
	<p>Date: <input type="text" id="datepicker" name="date"><br/></p>
	<!-- TODO regarder https://jqueryui.com/datepicker/ identique pour tous les navigateurs a contrario de type="date"-->
	
	
	Heure de début : <input type="time" name="heure_debut" value="" class="reservation_borne"><br/>
	Heure de fin : <input type="time" name="heure_fin" min="<?php echo $depart; ?>" class="reservation_borne"><br/>
	<!-- TODO regarder timepicker a l'instar de datepicker 
		OU datetime pick et on degage la date -->
	<p>
	<label for="amount">Niveau de charge:</label>
	<input type="text" id="amount" name="amount" readonly style="border:0; " >
	</p>
 
	<div id="slider-range"></div>
	
	<input type="submit" value="Réserver" id="reserver_borne">


</body>