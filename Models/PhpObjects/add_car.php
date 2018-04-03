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
<html>
<head></head>
<body>
    <center>
    <form method="get" action="add_car.php">
    model: <input type="text" name="model" value="" class="add_car" ><br/>
	power: <input type="text" name="power" value="" class="add_car"><br/>
    name: <input type="text" name="name" value="" class="add_car"><br/>
    <input type="submit" value="valider">
    </form>
    </center>
</body>
</html>




