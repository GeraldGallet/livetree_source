<?php
  include '../interfaces/api.php';
  $mail = 'robin.poiret@isen.yncrea.fr';

  echo "Hello TEST API ;)\n";
  // GET EXAMPLE
  ///*
  $api_interface = new API();
  var_dump($api_interface->user_get($mail));
  //*/

  // POST EXAMPLE
  /*
  $ch = curl_init($api_url . 'user');
  $postData = array(
    'email' => 'robin.poiret@isen.yncrea.fr',
    'first_name' => 'Robin',
    'last_name' => 'Poiret',
    'password' => 'passw0rdR',
    'phone_number' => '0606060606',
    'id_status' => 'student'
  );
  $postDataEncoded = json_encode($postData);

  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = curl_exec($ch);
  //*/

  // DELETE EXAMPLE
  /*
  $ch = curl_init($api_url . 'user/');
  $postData = array(
    'email' => 'robin.poiret@isen.yncrea.fr'
  );
  $postDataEncoded = json_encode($postData);

  curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

  $result = curl_exec($ch);
  //*/

  // PATCH EXAMPLE
  /*
  $ch = curl_init($api_url . 'user/password');
  $postData = array(
    'email' => 'robin.poiret@isen.yncrea.fr',
    'password' => 'passw0rd2lafamile'
  );
  $postDataEncoded = json_encode($postData);

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = curl_exec($ch);
  curl_close($ch);
  //*/
?>
