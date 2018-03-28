<?php
  echo "Hello TEST API ;)\n";
  $api_url = 'http://localhost:3000/';
  $mail = "alexis.fardel@isen.yncrea.fr";

  // GET EXAMPLE
  /*
  $ch = curl_init($api_url . 'get_user/');
  $getData = array(
    'email' => 'robin.poiret@isen.yncrea.fr'
  );

  //Encode the array into JSON.
  $getDataEncoded = json_encode($getData);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $getDataEncoded);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  $data = curl_exec($ch);
  $data = json_decode($data, true);
  $user = $data['response'][0];

  var_dump($user);
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
  ///*
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
