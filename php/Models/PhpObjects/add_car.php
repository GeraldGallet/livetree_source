<?php
 session_start();
	    $name = isset($_GET['name']) ? $_GET['name'] : ''  ;
        $model = isset($_GET['model'])? $_GET['model'] : '';
		$power = isset($_GET['power']) ? $_GET['power'] : '';
		
		if ( $name != ''|| $model !=''||  $power != '')
		{
			if ($model === '')
                echo"ERROR: Please enter the model!";
			
			if ($name === '')
                die("ERROR: Please enter the username!");
			
			if ($power === '')	
                die("ERROR: Please enter the power!");
			
			
		}
	
   
		




?>





