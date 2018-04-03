<?php

require_once dirname(__FILE__).'/../Models/PhpFunctions/User_functions.php';
$msgErreurConnexion = checIfUserExistAndConnexionWhenSubmitConnexion();
$msgErreurInscription = checIfUserExistAndConnexionWhenSubmitSinscrire();
require dirname(__FILE__).'/../Views/layout/page_Authentification.php';
