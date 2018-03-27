<?php

	if (!empty($_SESSION['email'])){
		$sessionid = $_SESSION['email'];
	}
	else{
		$sessionid = "visiteur";//session id est utilisé dans
	}

