<?php

	if (!empty($_SESSION['id_utilisateur'])){
		$sessionid = $_SESSION['id_utilisateur'];
	}
	else{
		$sessionid = "visiteur";//session id est utilisé dans
	}

