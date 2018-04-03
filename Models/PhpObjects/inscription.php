<?php
 session_start();

		//$response = (array) json_decode(file_get_contents('http://localhost:3000/user/' . $mail));
   
		
   
        $name = isset($_GET['nom']) ? $_GET['nom'] : ''  ;
        $prenom = isset($_GET['prenom'])? $_GET['prenom'] : '';
		$email = isset($_GET['email'])? $_GET['email'] : '';
		$phoneNumber = isset($_GET['phoneNumber']) ? $_GET['phoneNumber'] : '';
		$etablissement = isset($_GET['etablissement'])? $_GET['etablissement'] : '';
		$statut = isset($_GET['statut'])? $_GET['statut'] : '';
		
		if ( $name != ''|| $prenom !=''|| $email != ''|| $phoneNumber != '' || $etablissement != '' || $statut != '')
		{
			if ($prenom === '')
                echo"ERROR: Please enter the firstname!";
			if ($name === '')
                die("ERROR: Please enter the name!");
			if ($email === '')
                die("ERROR: Please enter the email!");
			if ($phoneNumber === '')	
                die("ERROR: Please enter the phoneNumber!");
			if ($etablissement === '')	
                die("ERROR: Please enter the facility!");
			if ($statut === '')	
                die("ERROR: Please enter the status!");
			if (validate_phone_number($phoneNumber)){}
			else
			{ 
				echo "Votre tel n'est pas valide";
			}
			
		}
	
  
		function validate_phone_number($phoneNumber)
		{
			$filtered_phone_number = filter_var($phoneNumber, FILTER_SANITIZE_NUMBER_INT);
			$phone_to_check = str_replace("-", "", $filtered_phone_number);
			$test= strlen($phone_to_check);
			if (strlen($phone_to_check) !=10 ) {
				return false;
			} 
			else {
				return true;
			}
		}
		if (isset($email))
		{
			
		include_once 'class.verifyEmail.php';
		$vmail = new verifyEmail();
		$vmail->setStreamTimeoutWait(20);
		$vmail->Debug=FALSE;
		$vmail->Debugoutput= 'html';
		$vmail->setEmailFrom('viska@viska.is');
		if ($vmail->check($email)) {
		} elseif (verifyEmail::validate($email)) {
			echo 'email &lt;' . $email . '&gt; valid, but not exist!';
		} else {
			echo 'email &lt;' . $email . '&gt; not valid and not exist!';
		}
		}


?>
<html>
<head></head>
<body>
    <center>
    <form method="get" action=" inscription">
    Nom	: <input type="text" name="nom" value="" class=" inscription" ><br>
	Prenom: <input type="text" name="prenom" value="" class=" inscription"><br>
	Email: <input type="email" name="email" value="" class=" inscription"><br>
    Numéros de téléphone: <input type="text" name="phoneNumber" value="" class=" inscription"><br>
	Etablissement : <input type="text" name="etablissement" value="" class=" inscription"><br>
	Statut : <input type="text" name="statut" value="" class=" inscription"><br>
    <input type="submit" value="valider" i="bouton_inscription_valider">
    </form>
    </center>
</body>
</html>