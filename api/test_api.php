<?php
  include '../interfaces/api.php';
  $mail = 'robin.poiret@isen.yncrea.fr';
  $pass1 = "passw0rdR";
  $pass2 = "hell0g00dpass";

  echo "Hello TEST API ;)\n";
  // GET EXAMPLE
  ///*
  $api_interface = new CustomAPI();
  //var_dump($api_interface->user_get($mail));
  //$api_interface->user_delete($mail);
  $user = array(
    'email' => 'robin.poiret@isen.yncrea.fr',
    'first_name' => 'Robin',
    'last_name' => 'Poiret',
    'password' => 'passw0rdR',
    'phone_number' => '0606060606',
    'id_status' => 'student'
  );
  //$api_interface->user_add($user);
  $api_interface->user_change_password($mail, $pass2);
  //*/

  // POST EXAMPLE
  /*

  //*/

  // DELETE EXAMPLE
  /*

  //*/

  // PATCH EXAMPLE
  /*
  //*/
?>
