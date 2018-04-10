<?php
   session_start();
   if (get_magic_quotes_gpc())
   {
      echo "WARNING! Magic quotes are on\n";
   }
   if (empty($_SESSION['sessid']))
   {
        $name = isset($_GET['name'])? $_GET['name'] : '' ;
        $pass = isset($_GET['pass'])? $_GET['pass'] : '';
		

        if ($name != '' || $pass != '')
        {
            if ($name === '')
                die("ERROR: Please enter the username!");

            if ($pass === '')
                die("ERROR: Please enter the password!");

            

        }
		echo " $name";
		
    }
    // no submitted data, display form:
?>
<html>
<head></head>
<body>
    <center>
    <form method="get" action="login.php">
    Username: <input type="text" name="name" value=""><br>
    Password: <input type="password" name="pass"><br>
    <input type="submit" value="Log In" >
    </form>
    </center>
</body>
</html>
